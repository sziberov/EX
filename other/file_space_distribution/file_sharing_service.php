<?php
// Подключение к базе данных
$mysqli = new mysqli("localhost", "username", "password", "file_sharing_service");

// Получение IP-адреса пользователя
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Регистрация пользователя
function registerUser($mysqli, $login, $password) {
    $ip = getUserIP();
    $stmt = $mysqli->prepare("INSERT INTO users (login, password, registration_ip) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $login, $password, $ip);
    $stmt->execute();
    $stmt->close();

    // Пересчитать квоты после регистрации нового пользователя
    allocateSpace($mysqli);
}

// Получение общего и использованного пространства
function getDiskUsage($mysqli) {
    $totalSpace = 1 * 1024 * 1024 * 1024; // 1 TB в байтах
    $usedSpaceResult = $mysqli->query("SELECT SUM(file_size) as used_space FROM files");
    $usedSpaceRow = $usedSpaceResult->fetch_assoc();
    $usedSpace = $usedSpaceRow['used_space'];
    return array($totalSpace, $usedSpace);
}

// Получение рейтинга пользователей
function getUserRanks($mysqli) {
    $result = $mysqli->query("SELECT users.id, users.login, users.registration_ip, COUNT(DISTINCT files.file_hash) AS unique_files
                              FROM users
                              LEFT JOIN files ON users.id = files.user_id
                              GROUP BY users.id
                              ORDER BY unique_files DESC");
    $ranks = array();
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $ranks[] = array(
            'id' => $row['id'],
            'login' => $row['login'],
            'registration_ip' => $row['registration_ip'],
            'unique_files' => $row['unique_files'],
            'rank' => $rank++
        );
    }
    return $ranks;
}

// Функции для работы с кэшем
function getCache($key) {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    return $redis->get($key);
}

function setCache($key, $value, $ttl = 3600) {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->set($key, $value, $ttl);
}

function getUserRanksCached($mysqli) {
    $cacheKey = 'user_ranks';
    $ranks = getCache($cacheKey);
    if ($ranks !== false) {
        return json_decode($ranks, true);
    }
    $ranks = getUserRanks($mysqli);
    setCache($cacheKey, json_encode($ranks));
    return $ranks;
}

// Получение суммарного использования пространства для всех аккаунтов с одного IP
function getTotalUsedSpaceByIP($mysqli, $ip) {
    $stmt = $mysqli->prepare("SELECT SUM(files.file_size) as used_space 
                              FROM files 
                              JOIN users ON files.user_id = users.id 
                              WHERE users.registration_ip = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['used_space'];
}

function calculateQuotaByIP($mysqli, $ip) {
    $cacheKey = 'quota_ip_' . $ip;
    $quota = getCache($cacheKey);
    if ($quota !== false) {
        return $quota;
    }
    list($totalSpace, $usedSpace) = getDiskUsage($mysqli);
    $freeSpace = $totalSpace - $usedSpace;
    $ranks = getUserRanksCached($mysqli);
    $numUsers = count($ranks);
    $baseQuota = $freeSpace / $numUsers;

    $ipQuotas = [];

    foreach ($ranks as $user) {
        $userIP = $user['registration_ip'];
        $bonusFactor = 1 / $user['rank'];
        $bonusQuota = $baseQuota * $bonusFactor;
        $totalQuota = $baseQuota + $bonusQuota;

        if (!isset($ipQuotas[$userIP])) {
            $ipQuotas[$userIP] = 0;
        }
        $ipQuotas[$userIP] += $totalQuota;
    }

    $quota = isset($ipQuotas[$ip]) ? $ipQuotas[$ip] : 0;
    setCache($cacheKey, $quota);
    return $quota;
}

function allocateSpace($mysqli) {
    list($totalSpace, $usedSpace) = getDiskUsage($mysqli);
    $freeSpace = $totalSpace - $usedSpace;

    $ranks = getUserRanksCached($mysqli);
    $numUsers = count($ranks);
    $baseQuota = $freeSpace / $numUsers;

    // Массив для хранения суммарного использования и квот по IP
    $ipQuotas = [];

    foreach ($ranks as $user) {
        $userIP = $user['registration_ip'];
        $bonusFactor = 1 / $user['rank'];
        $bonusQuota = $baseQuota * $bonusFactor;
        $totalQuota = $baseQuota + $bonusQuota;

        if (!isset($ipQuotas[$userIP])) {
            $ipQuotas[$userIP] = 0;
        }
        $ipQuotas[$userIP] += $totalQuota;
    }

    // Обновляем квоты для каждого пользователя на основе IP
    foreach ($ranks as $user) {
        $userIP = $user['registration_ip'];
        $effectiveQuota = $ipQuotas[$userIP];

        // Сохранение или вывод квоты пользователя
        echo "User ID: " . $user['id'] . ", IP: " . $userIP . ", Quota: " . $effectiveQuota . " bytes\n";

        // Сохранение квоты в кэш
        setCache('quota_ip_' . $userIP, $effectiveQuota);
    }
}

function updateQuotaOnFileUpload($mysqli, $userId, $fileSize) {
    $stmt = $mysqli->prepare("SELECT registration_ip FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userIP = $row['registration_ip'];
    $stmt->close();

    $totalQuota = getCache('quota_ip_' . $userIP);
    if ($totalQuota === false) {
        $totalQuota = calculateQuotaByIP($mysqli, $userIP);
    }

    $usedSpace = getCache('used_space_ip_' . $userIP);
    if ($usedSpace === false) {
        $usedSpace = getTotalUsedSpaceByIP($mysqli, $userIP);
    }

    if ($usedSpace + $fileSize > $totalQuota) {
        throw new Exception("Not enough space for the file upload.");
    }

    // Обновление кэшированной квоты после загрузки файла
    $newUsedSpace = $usedSpace + $fileSize;
    setCache('used_space_ip_' . $userIP, $newUsedSpace);
}

function uploadFile($mysqli, $userId, $fileName, $fileSize, $fileHash) {
    updateQuotaOnFileUpload($mysqli, $userId, $fileSize);

    // Загрузка файла
    $stmt = $mysqli->prepare("INSERT INTO files (user_id, file_name, file_size, file_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $userId, $fileName, $fileSize, $fileHash);
    $stmt->execute();
    $stmt->close();

    // Пересчитать квоты после загрузки файла
    allocateSpace($mysqli);
}

// Удаление файла
function deleteFile($mysqli, $fileId) {
    // Удаление файла из базы данных
    $stmt = $mysqli->prepare("DELETE FROM files WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->close();

    // Пересчитать квоты после удаления файла
    allocateSpace($mysqli);
}
?>
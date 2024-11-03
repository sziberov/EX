-- saved_objects

SELECT o.*
FROM objects AS o
LEFT JOIN settings AS s ON s.object_id = o.id AND s.`key` = 'awaiting_save'
WHERE o.type_id != 4  -- Объект не должен быть общим
  AND (s.value != 'true' OR s.value IS NULL)  -- Объект должен быть явно или неявно сохранён

-- public_objects

SELECT o.*
FROM objects AS o
LEFT JOIN settings AS s_0 ON s_0.object_id = o.id AND s_0.`key` = 'awaiting_save'
LEFT JOIN settings AS s_1 ON s_1.object_id = o.id AND s_1.`key` = 'hide_from_search'
LEFT JOIN settings AS s_2 ON s_2.object_id = o.user_id AND s_2.`key` = 'hide_from_search'
JOIN links AS l ON l.from_id = 1 AND l.to_id = o.id AND l.type_id = 1
JOIN settings AS s_3 ON s_3.link_id = l.id AND s_3.`key` = 'access_level_id' AND s_3.value BETWEEN 1 AND 5
WHERE o.type_id != 4  -- Объект не должен быть общим
  AND (s_0.value != 'true' OR s_0.value IS NULL)  -- Объект должен быть явно или неявно сохранён
  AND (s_1.value != 'true' OR s_1.value IS NULL)  -- Объект не должен быть скрыт от поиска
  AND (s_2.value != 'true' OR s_2.value IS NULL)  -- Автор не должен быть скрыт от поиска

-- uploads_stats

SELECT
	o_0.id AS user_id,
	COALESCE(SUM(original), 0) AS originals_count,
	COALESCE(SUM(original*size), 0) AS originals_size,
	COALESCE(SUM(NOT original), 0) AS duplicates_count,
	COALESCE(SUM((NOT original)*size), 0) AS duplicates_size
FROM objects AS o_0
JOIN (
	SELECT
		o_1.id,
		u_0.id = MIN(u_1.id) AS original,
		MAX(fs.upload_offset) AS size
	FROM objects AS o_1
	LEFT JOIN objects AS o_2 ON o_2.user_id = o_1.id
	LEFT JOIN uploads AS u_0 ON u_0.object_id = o_2.id
	LEFT JOIN uploads AS u_1 ON u_1.file_id = u_0.file_id
	LEFT JOIN fs_files AS fs ON fs.file_id = u_0.file_id
	WHERE o_1.type_id = 2
	GROUP BY o_1.id, u_0.id
) AS stats ON stats.id = o_0.id
WHERE o_0.type_id = 2
GROUP BY o_0.id

-- visits_stats

SELECT
	o.id AS object_id,
	COUNT(v.id) AS hits_count,
	COUNT(DISTINCT CASE WHEN v.ip_address IS NOT NULL THEN v.ip_address END) AS hosts_count,
	COUNT(DISTINCT CASE WHEN v.referrer_url IS NOT NULL AND v.referrer_url NOT LIKE '/%' THEN COALESCE(v.ip_address, '') END) AS guests_count
FROM objects AS o
LEFT JOIN visits AS v ON v.object_id = o.id
GROUP BY o.id

-- most_visited

SELECT
	o.id AS object_id,
	SUM(DATE(v.creation_time) = CURDATE()) AS today_count,
	SUM(DATE(v.creation_time) = CURDATE()-INTERVAL 1 DAY) AS yesterday_count,
	SUM(1) AS count
FROM objects AS o
JOIN visits AS v ON v.object_id = o.id
GROUP BY o.id

-- most_commented

SELECT
	o.id AS object_id,
	SUM(DATE(l.creation_time) = CURDATE()) AS today_count,
	SUM(DATE(l.creation_time) = CURDATE()-INTERVAL 1 DAY) AS yesterday_count,
	SUM(1) AS count
FROM objects AS o
JOIN links AS l ON l.to_id = o.id AND l.type_id = 5
GROUP BY o.id

-- most_recommended

SELECT
	o.id AS object_id,
	SUM(DATE(l.creation_time) = CURDATE()) AS today_count,
	SUM(DATE(l.creation_time) = CURDATE()-INTERVAL 1 DAY) AS yesterday_count,
	SUM(1) AS count
FROM objects AS o
JOIN links AS l ON l.from_id = o.id AND l.type_id = 6
GROUP BY o.id

-- most

SELECT
	o.id AS object_id,
	COALESCE(v.today_count, 0)+COALESCE(c.today_count, 0)+COALESCE(r.today_count, 0) AS today_count,
	COALESCE(v.yesterday_count, 0)+COALESCE(c.yesterday_count, 0)+COALESCE(r.yesterday_count, 0) AS yesterday_count,
	COALESCE(v.count, 0)+COALESCE(c.count, 0)+COALESCE(r.count, 0) AS count
FROM objects AS o
LEFT JOIN most_visited AS v ON v.object_id = o.id
LEFT JOIN most_commented AS c ON c.object_id = o.id
LEFT JOIN most_recommended AS r ON r.object_id = o.id
WHERE v.object_id IS NOT NULL
   OR c.object_id IS NOT NULL
   OR r.object_id IS NOT NULL
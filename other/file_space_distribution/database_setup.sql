-- Создание базы данных и таблиц
CREATE DATABASE file_sharing_service;

USE file_sharing_service;

-- Таблица пользователей
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    registration_ip VARCHAR(45) NOT NULL
);

-- Таблица файлов
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    file_name VARCHAR(255),
    file_size INT,
    file_hash VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Индексы для оптимизации запросов
CREATE INDEX idx_registration_ip ON users (registration_ip);
CREATE INDEX idx_user_id ON files (user_id);
CREATE INDEX idx_file_hash ON files (file_hash);
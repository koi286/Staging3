-- データベース作成
CREATE DATABASE IF NOT EXISTS Keiziban;
USE Keiziban;

-- users テーブル
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password CHAR(64) NOT NULL
);

-- posts テーブル（返信対応: parent_id）
DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post TEXT NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (parent_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- サンプルユーザー
INSERT INTO users (name, email, password) VALUES
('Kira', 'Kira@ramen.com', SHA2('password123', 256)),
('Egg', 'Egg@ramen.com', SHA2('password456', 256));

-- サンプル投稿
INSERT INTO posts (user_id, post) VALUES
(1, 'Test投稿'),
(2, 'Ramen is my life-wan!');

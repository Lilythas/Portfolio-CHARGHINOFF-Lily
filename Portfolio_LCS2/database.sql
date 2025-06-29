
CREATE DATABASE IF NOT EXISTS portfoliojed CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfoliojed;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user'
);

CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE user_skills (
    user_id INT,
    skill_id INT,
    level ENUM('débutant','intermédiaire','avancé','expert'),
    PRIMARY KEY(user_id, skill_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    link VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@example.com', '$2y$10$3gU6qpEDBFH4Mi6dCHCZzO0P0yDq2QerDCIlZXZkN6NuR1qLro8B6', 'admin'),
('Utilisateur', 'user@example.com', '$2y$10$3gU6qpEDBFH4Mi6dCHCZzO0P0yDq2QerDCIlZXZkN6NuR1qLro8B6', 'user');

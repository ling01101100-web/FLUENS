CREATE DATABASE IF NOT EXISTS pomodoro_todo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pomodoro_todo;

DROP TABLE IF EXISTS pomodoro_sessions, tasks, categories, users, roles;

CREATE TABLE roles (
  id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id       TINYINT UNSIGNED NOT NULL,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE categories (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  name    VARCHAR(40) NOT NULL,
  UNIQUE KEY uq_category_per_user (user_id, name),
  CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tasks (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  category_id   INT UNSIGNED NULL,
  title         VARCHAR(120) NOT NULL,
  est_pomodoros TINYINT UNSIGNED NOT NULL DEFAULT 1,
  status        ENUM('open','done') NOT NULL DEFAULT 'open',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tasks_user     FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
  CONSTRAINT fk_tasks_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE pomodoro_sessions (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id          INT UNSIGNED NOT NULL,
  task_id          INT UNSIGNED NULL,
  type             ENUM('focus','short_break','long_break') NOT NULL,
  duration_minutes TINYINT UNSIGNED NOT NULL,
  completed_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sessions_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (name) VALUES ('admin'), ('manager'), ('user');

INSERT INTO users (role_id, username, email, password_hash) VALUES
  (1, 'admin',   'admin@example.com',   '$2b$10$1HZROoG/x2V2ZP4.B7dqpeGRMzgCh9ZL5VtluRsst4J4xcg9pMadu'),
  (2, 'manager', 'manager@example.com', '$2b$10$1HZROoG/x2V2ZP4.B7dqpeGRMzgCh9ZL5VtluRsst4J4xcg9pMadu'),
  (3, 'alice',   'alice@example.com',   '$2b$10$1HZROoG/x2V2ZP4.B7dqpeGRMzgCh9ZL5VtluRsst4J4xcg9pMadu');

INSERT INTO categories (user_id, name) VALUES (3, 'Study'), (3, 'Project');
INSERT INTO tasks (user_id, category_id, title, est_pomodoros, status) VALUES
  (3, 1, 'Revise SQL joins',             2, 'open'),
  (3, 2, 'Build login page',             3, 'open'),
  (3, 2, 'Draw use case diagram',        1, 'done');
INSERT INTO pomodoro_sessions (user_id, task_id, type, duration_minutes) VALUES
  (3, 3, 'focus', 25), (3, 3, 'short_break', 5), (3, 2, 'focus', 25);

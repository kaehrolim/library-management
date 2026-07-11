-- ============================================================
-- Sistema de Gerenciamento de Biblioteca Escolar
-- Schema do banco de dados (MySQL)
-- ============================================================
-- Uso: crie o banco e importe este arquivo.
--   CREATE DATABASE library_management;
--   USE library_management;
--   SOURCE sql/schema.sql;
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Usuários (leitores e administradores)
-- ------------------------------------------------------------
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)  NOT NULL,
    email       VARCHAR(160)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,           -- hash gerado por password_hash()
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Livros do acervo
-- ------------------------------------------------------------
CREATE TABLE books (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(200) NOT NULL,
    author        VARCHAR(160) NOT NULL,
    release_date  DATE         NULL,
    total_copies  INT          NOT NULL DEFAULT 1,   -- quantidade total no acervo
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Empréstimos (relação usuário <-> livro)
-- returned_at NULL = empréstimo ativo (livro está com o usuário)
-- ------------------------------------------------------------
CREATE TABLE loans (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    book_id      INT NOT NULL,
    borrowed_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    returned_at  TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_loans_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_loans_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Seed: usuário administrador padrão
-- email: admin@biblioteca.local
-- senha: Admin@123   (troque após o primeiro login)
-- O hash abaixo foi gerado com password_hash('Admin@123', PASSWORD_DEFAULT)
-- ------------------------------------------------------------
INSERT INTO users (name, email, password, role) VALUES
('Administrador', 'admin@biblioteca.local',
 '$2b$10$DZra9yzXIwQOUxckYLVh9O09cflsVuEZ9DNtl/OSM5ljmmZ50FqyC', 'admin');

-- Livros de exemplo para o acervo não nascer vazio
INSERT INTO books (title, author, release_date, total_copies) VALUES
('Dom Casmurro', 'Machado de Assis', '1899-01-01', 3),
('O Cortiço', 'Aluísio Azevedo', '1890-01-01', 2),
('Vidas Secas', 'Graciliano Ramos', '1938-01-01', 2),
('Grande Sertão: Veredas', 'João Guimarães Rosa', '1956-01-01', 1);

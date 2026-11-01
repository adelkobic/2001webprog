CREATE DATABASE IF NOT EXISTS library;
USE library;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'patron') NOT NULL DEFAULT 'patron',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL,
    publisher VARCHAR(255) NOT NULL,
    publication_year INT NOT NULL,
    genre_id INT NOT NULL,
    total_copies INT NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (genre_id) REFERENCES genres(id)
);

CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_by INT NOT NULL,
    rented_at DATE NOT NULL,
    due_date DATE NOT NULL,
    returned_at DATE NULL,
    status ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY one_review_per_patron (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Passwords: admin123, staff123, patron123, patron123
INSERT INTO users (first_name, last_name, email, phone, password_hash, role) VALUES
('Admin', 'User', 'admin@library.local', '061000001', '$2b$10$YjZ0xPS/VVrsBdNHbSF6G.0fALDZwPPg0Fum3eS5.SA3rBEPZilYW', 'admin'),
('Sam', 'Staff', 'staff@library.local', '061000002', '$2b$10$qFf2pQk7Bfg9WHu2ZiHceOfLsJbo02pUdn6w.UeVaI0AgTb4qd0rS', 'staff'),
('Ana', 'Patron', 'patron@library.local', '061000003', '$2b$10$7IvMxYiVEml8X9ZF3WtOz.RVG2IL6tkbmXQaNmCmOrj.FSHV/gojK', 'patron'),
('Ben', 'Reader', 'patron2@library.local', '061000004', '$2b$10$7IvMxYiVEml8X9ZF3WtOz.RVG2IL6tkbmXQaNmCmOrj.FSHV/gojK', 'patron');

INSERT INTO genres (name) VALUES
('Fiction'),
('Fantasy'),
('Science');

INSERT INTO books (title, author, isbn, publisher, publication_year, genre_id, total_copies, description) VALUES
('Pride and Prejudice', 'Jane Austen', '9780141439518', 'Penguin', 2002, 1, 3, 'A novel about Elizabeth Bennet and Mr Darcy.'),
('1984', 'George Orwell', '9780451524935', 'Signet', 1961, 1, 2, 'A novel about a government that watches everyone.'),
('The Hobbit', 'J.R.R. Tolkien', '9780547928227', 'Houghton Mifflin', 2012, 2, 2, 'A fantasy story about Bilbo Baggins.'),
('A Brief History of Time', 'Stephen Hawking', '9780553380163', 'Bantam', 1998, 3, 1, 'A short book about the universe.');

INSERT INTO rentals (user_id, book_id, created_by, rented_at, due_date, returned_at, status) VALUES
(3, 3, 2, '2026-10-10', '2026-11-10', NULL, 'active'),
(3, 2, 2, '2026-09-01', '2026-09-15', '2026-09-14', 'returned'),
(4, 1, 2, '2026-10-01', '2026-10-20', NULL, 'active');

INSERT INTO reviews (user_id, book_id, rating, comment) VALUES
(3, 3, 5, 'I liked the adventure.'),
(4, 1, 4, 'A good story with a lot of dialogue.');

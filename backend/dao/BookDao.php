<?php

class BookDao extends BaseDao {
    private function selectSql() {
        return "SELECT b.id, b.title, b.author, b.isbn, b.publisher, b.publication_year,
                       b.genre_id, b.total_copies, b.description, g.name AS genre_name,
                       (b.total_copies - (
                            SELECT COUNT(*) FROM rentals r
                            WHERE r.book_id = b.id AND r.status IN ('active', 'overdue')
                       )) AS available_copies
                FROM books b
                JOIN genres g ON g.id = b.genre_id";
    }

    public function getAll() {
        return $this->query($this->selectSql() . ' ORDER BY b.title')->fetchAll();
    }

    public function search($search, $genreId) {
        $search = trim((string) $search);
        $like = '%' . $search . '%';
        $genreId = (int) $genreId;
        $sql = $this->selectSql() . "
                WHERE (? = '' OR b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)
                  AND (? = 0 OR b.genre_id = ?)
                ORDER BY b.title";
        return $this->query($sql, [$search, $like, $like, $like, $genreId, $genreId])->fetchAll();
    }

    public function getById($id) {
        $sql = $this->selectSql() . ' WHERE b.id = ?';
        return $this->query($sql, [$id])->fetch();
    }

    public function insert($title, $author, $isbn, $publisher, $year, $genreId, $copies, $description) {
        $sql = 'INSERT INTO books
                (title, author, isbn, publisher, publication_year, genre_id, total_copies, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $this->query($sql, [$title, $author, $isbn, $publisher, $year, $genreId, $copies, $description]);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, $title, $author, $isbn, $publisher, $year, $genreId, $copies, $description) {
        $sql = 'UPDATE books
                SET title = ?, author = ?, isbn = ?, publisher = ?, publication_year = ?,
                    genre_id = ?, total_copies = ?, description = ?
                WHERE id = ?';
        $this->query($sql, [$title, $author, $isbn, $publisher, $year, $genreId, $copies, $description, $id]);
    }

    public function delete($id) {
        $this->query('DELETE FROM books WHERE id = ?', [$id]);
    }

    public function countRentals($id) {
        $row = $this->query('SELECT COUNT(*) AS total FROM rentals WHERE book_id = ?', [$id])->fetch();
        return (int) $row['total'];
    }

    public function countReviews($id) {
        $row = $this->query('SELECT COUNT(*) AS total FROM reviews WHERE book_id = ?', [$id])->fetch();
        return (int) $row['total'];
    }
}

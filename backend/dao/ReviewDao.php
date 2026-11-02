<?php

class ReviewDao extends BaseDao {
    private function selectSql() {
        return 'SELECT rv.id, rv.user_id, rv.book_id, rv.rating, rv.comment, rv.created_at,
                       u.first_name, u.last_name, b.title AS book_title
                FROM reviews rv
                JOIN users u ON u.id = rv.user_id
                JOIN books b ON b.id = rv.book_id';
    }

    public function getAll() {
        return $this->query($this->selectSql() . ' ORDER BY rv.id DESC')->fetchAll();
    }

    public function getByBook($bookId) {
        $sql = $this->selectSql() . ' WHERE rv.book_id = ? ORDER BY rv.id DESC';
        return $this->query($sql, [$bookId])->fetchAll();
    }

    public function getById($id) {
        return $this->query($this->selectSql() . ' WHERE rv.id = ?', [$id])->fetch();
    }

    public function findByUserAndBook($userId, $bookId) {
        $sql = $this->selectSql() . ' WHERE rv.user_id = ? AND rv.book_id = ?';
        return $this->query($sql, [$userId, $bookId])->fetch();
    }

    public function insert($userId, $bookId, $rating, $comment) {
        $sql = 'INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)';
        $this->query($sql, [$userId, $bookId, $rating, $comment]);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, $rating, $comment) {
        $this->query('UPDATE reviews SET rating = ?, comment = ? WHERE id = ?', [$rating, $comment, $id]);
    }

    public function delete($id) {
        $this->query('DELETE FROM reviews WHERE id = ?', [$id]);
    }
}

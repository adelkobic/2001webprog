<?php

class RentalDao extends BaseDao {
    private function selectSql() {
        return "SELECT r.id, r.user_id, r.book_id, r.created_by, r.rented_at, r.due_date,
                       r.returned_at, r.status,
                       u.first_name AS patron_first_name, u.last_name AS patron_last_name,
                       b.title AS book_title,
                       s.first_name AS staff_first_name, s.last_name AS staff_last_name
                FROM rentals r
                JOIN users u ON u.id = r.user_id
                JOIN books b ON b.id = r.book_id
                JOIN users s ON s.id = r.created_by";
    }

    public function markOverdue() {
        $sql = "UPDATE rentals SET status = 'overdue'
                WHERE status = 'active' AND due_date < CURDATE()";
        $this->query($sql);
    }

    public function getAll() {
        return $this->query($this->selectSql() . ' ORDER BY r.id DESC')->fetchAll();
    }

    public function getByUser($userId) {
        $sql = $this->selectSql() . ' WHERE r.user_id = ? ORDER BY r.id DESC';
        return $this->query($sql, [$userId])->fetchAll();
    }

    public function getById($id) {
        return $this->query($this->selectSql() . ' WHERE r.id = ?', [$id])->fetch();
    }

    public function countActiveRentals($bookId) {
        $sql = "SELECT COUNT(*) AS total FROM rentals
                WHERE book_id = ? AND status IN ('active', 'overdue')";
        $row = $this->query($sql, [$bookId])->fetch();
        return (int) $row['total'];
    }

    public function insert($userId, $bookId, $createdBy, $rentedAt, $dueDate) {
        $sql = "INSERT INTO rentals (user_id, book_id, created_by, rented_at, due_date, status)
                VALUES (?, ?, ?, ?, ?, 'active')";
        $this->query($sql, [$userId, $bookId, $createdBy, $rentedAt, $dueDate]);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, $userId, $bookId, $createdBy, $rentedAt, $dueDate, $returnedAt, $status) {
        $sql = 'UPDATE rentals
                SET user_id = ?, book_id = ?, created_by = ?, rented_at = ?,
                    due_date = ?, returned_at = ?, status = ?
                WHERE id = ?';
        $this->query($sql, [$userId, $bookId, $createdBy, $rentedAt, $dueDate, $returnedAt, $status, $id]);
    }

    public function markReturned($id, $returnedAt) {
        $sql = "UPDATE rentals SET status = 'returned', returned_at = ? WHERE id = ?";
        $this->query($sql, [$returnedAt, $id]);
    }

    public function delete($id) {
        $this->query('DELETE FROM rentals WHERE id = ?', [$id]);
    }
}

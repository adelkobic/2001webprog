<?php

class UserDao extends BaseDao {
    public function getAll() {
        $sql = 'SELECT id, first_name, last_name, email, phone, role, created_at
                FROM users ORDER BY last_name, first_name';
        return $this->query($sql)->fetchAll();
    }

    public function getByRole($role) {
        $sql = 'SELECT id, first_name, last_name, email, phone, role, created_at
                FROM users WHERE role = ? ORDER BY last_name, first_name';
        return $this->query($sql, [$role])->fetchAll();
    }

    public function getById($id) {
        $sql = 'SELECT id, first_name, last_name, email, phone, role, created_at
                FROM users WHERE id = ?';
        return $this->query($sql, [$id])->fetch();
    }

    public function getByEmail($email) {
        return $this->query('SELECT * FROM users WHERE email = ?', [$email])->fetch();
    }

    public function insert($firstName, $lastName, $email, $phone, $passwordHash, $role) {
        $sql = 'INSERT INTO users (first_name, last_name, email, phone, password_hash, role)
                VALUES (?, ?, ?, ?, ?, ?)';
        $this->query($sql, [$firstName, $lastName, $email, $phone, $passwordHash, $role]);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, $firstName, $lastName, $email, $phone, $role, $passwordHash = null) {
        if ($passwordHash !== null) {
            $sql = 'UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, password_hash = ?
                    WHERE id = ?';
            $this->query($sql, [$firstName, $lastName, $email, $phone, $role, $passwordHash, $id]);
            return;
        }

        $sql = 'UPDATE users
                SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?
                WHERE id = ?';
        $this->query($sql, [$firstName, $lastName, $email, $phone, $role, $id]);
    }

    public function delete($id) {
        $this->query('DELETE FROM users WHERE id = ?', [$id]);
    }

    public function countRentals($id) {
        $sql = 'SELECT COUNT(*) AS total FROM rentals WHERE user_id = ? OR created_by = ?';
        $row = $this->query($sql, [$id, $id])->fetch();
        return (int) $row['total'];
    }

    public function countReviews($id) {
        $row = $this->query('SELECT COUNT(*) AS total FROM reviews WHERE user_id = ?', [$id])->fetch();
        return (int) $row['total'];
    }
}

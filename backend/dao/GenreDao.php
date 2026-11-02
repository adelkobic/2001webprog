<?php

class GenreDao extends BaseDao {
    public function getAll() {
        return $this->query('SELECT id, name FROM genres ORDER BY name')->fetchAll();
    }

    public function getById($id) {
        return $this->query('SELECT id, name FROM genres WHERE id = ?', [$id])->fetch();
    }

    public function insert($name) {
        $this->query('INSERT INTO genres (name) VALUES (?)', [$name]);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, $name) {
        $this->query('UPDATE genres SET name = ? WHERE id = ?', [$name, $id]);
    }

    public function delete($id) {
        $this->query('DELETE FROM genres WHERE id = ?', [$id]);
    }

    public function countBooks($id) {
        $row = $this->query('SELECT COUNT(*) AS total FROM books WHERE genre_id = ?', [$id])->fetch();
        return (int) $row['total'];
    }
}

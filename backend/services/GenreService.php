<?php

class GenreService {
    private $genres;

    public function __construct() {
        $this->genres = new GenreDao();
    }

    public function getAll() {
        return $this->formatList($this->genres->getAll());
    }

    public function getById($id) {
        $genre = $this->genres->getById($id);
        if (!$genre) throw new AppException('Genre not found.', 404);
        return $this->format($genre);
    }

    public function create($data) {
        Validator::required($data, ['name']);
        $name = Validator::text($data['name'], 'Name', 100);
        try {
            $id = $this->genres->insert($name);
        } catch (PDOException $error) {
            $this->rethrowUnlessDuplicateName($error);
            throw new AppException('That genre already exists.', 409);
        }
        return $this->getById($id);
    }

    public function update($id, $data) {
        $this->getById($id);
        Validator::required($data, ['name']);
        $name = Validator::text($data['name'], 'Name', 100);
        try {
            $this->genres->update($id, $name);
        } catch (PDOException $error) {
            $this->rethrowUnlessDuplicateName($error);
            throw new AppException('That genre already exists.', 409);
        }
        return $this->getById($id);
    }

    public function delete($id) {
        $this->getById($id);
        if ($this->genres->countBooks($id) > 0) {
            throw new AppException('This genre has books, so it cannot be deleted.');
        }
        $this->genres->delete($id);
    }

    private function rethrowUnlessDuplicateName(PDOException $error) {
        $sqlState = (string) $error->getCode();
        $driverCode = isset($error->errorInfo[1]) ? (int) $error->errorInfo[1] : 0;
        if ($sqlState !== '23000' || $driverCode !== 1062) {
            throw $error;
        }
    }

    private function formatList($rows) {
        $list = [];
        foreach ($rows as $row) $list[] = $this->format($row);
        return $list;
    }

    private function format($genre) {
        $genre['id'] = (int) $genre['id'];
        return $genre;
    }
}

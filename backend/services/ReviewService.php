<?php

class ReviewService {
    private $reviews;
    private $books;

    public function __construct() {
        $this->reviews = new ReviewDao();
        $this->books = new BookDao();
    }

    public function list($bookId = '') {
        if ($bookId) return $this->formatList($this->reviews->getByBook($bookId));
        return $this->formatList($this->reviews->getAll());
    }

    public function getById($id) {
        $review = $this->reviews->getById($id);
        if (!$review) throw new AppException('Review not found.', 404);
        return $this->format($review);
    }

    public function save($data, $user = null) {
        $clean = $this->check($data);
        $userId = $user ? (int) $user['id'] : (int) $data['user_id'];
        $existing = $this->reviews->findByUserAndBook($userId, $clean['book_id']);
        if ($existing) {
            $this->reviews->update($existing['id'], $clean['rating'], $clean['comment']);
            return $this->getById($existing['id']);
        }
        $id = $this->reviews->insert($userId, $clean['book_id'], $clean['rating'], $clean['comment']);
        return $this->getById($id);
    }

    public function update($id, $data, $user = null) {
        $review = $this->reviews->getById($id);
        if (!$review) throw new AppException('Review not found.', 404);
        if ($user && $user['role'] !== 'admin' && (int) $review['user_id'] !== (int) $user['id']) {
            throw new AppException('You cannot do that.', 403);
        }
        $clean = $this->check([
            'book_id' => $review['book_id'],
            'rating' => $data['rating'] ?? '',
            'comment' => $data['comment'] ?? '',
        ]);
        $this->reviews->update($id, $clean['rating'], $clean['comment']);
        return $this->getById($id);
    }

    public function delete($id, $user = null) {
        $review = $this->reviews->getById($id);
        if (!$review) throw new AppException('Review not found.', 404);
        if ($user && $user['role'] !== 'admin' && (int) $review['user_id'] !== (int) $user['id']) {
            throw new AppException('You cannot do that.', 403);
        }
        $this->reviews->delete($id);
    }

    private function check($data) {
        Validator::required($data, ['book_id', 'rating', 'comment']);
        if (!$this->books->getById($data['book_id'])) throw new AppException('Book not found.', 404);
        return [
            'book_id' => (int) $data['book_id'],
            'rating' => Validator::rating($data['rating']),
            'comment' => Validator::text($data['comment'], 'Comment', 500),
        ];
    }

    private function formatList($rows) {
        $list = [];
        foreach ($rows as $row) $list[] = $this->format($row);
        return $list;
    }

    private function format($review) {
        foreach (['id', 'user_id', 'book_id', 'rating'] as $field) {
            $review[$field] = (int) $review[$field];
        }
        return $review;
    }
}

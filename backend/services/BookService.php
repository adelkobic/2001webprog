<?php

class BookService {
    private $books;
    private $genres;
    private $rentals;

    public function __construct() {
        $this->books = new BookDao();
        $this->genres = new GenreDao();
        $this->rentals = new RentalDao();
    }

    public function search($search, $genreId) {
        $this->rentals->markOverdue();
        return $this->formatList($this->books->search($search, $genreId));
    }

    public function getById($id) {
        $this->rentals->markOverdue();
        $book = $this->books->getById($id);
        if (!$book) throw new AppException('Book not found.', 404);
        return $this->format($book);
    }

    public function create($data) {
        $clean = $this->check($data, null);
        $id = $this->books->insert(
            $clean['title'], $clean['author'], $clean['isbn'], $clean['publisher'],
            $clean['publication_year'], $clean['genre_id'], $clean['total_copies'],
            $clean['description']
        );
        return $this->getById($id);
    }

    public function update($id, $data) {
        $this->getById($id);
        $clean = $this->check($data, $id);
        $this->books->update(
            $id, $clean['title'], $clean['author'], $clean['isbn'], $clean['publisher'],
            $clean['publication_year'], $clean['genre_id'], $clean['total_copies'],
            $clean['description']
        );
        return $this->getById($id);
    }

    public function delete($id) {
        $this->getById($id);
        if ($this->rentals->countActiveRentals($id) > 0) {
            throw new AppException('This book is still rented out.');
        }
        if ($this->books->countRentals($id) > 0 || $this->books->countReviews($id) > 0) {
            throw new AppException('This book has rentals or reviews, so it cannot be deleted.');
        }
        $this->books->delete($id);
    }

    private function check($data, $bookId) {
        Validator::required($data, ['title', 'author', 'isbn', 'publisher', 'publication_year', 'genre_id', 'total_copies', 'description']);
        if (!$this->genres->getById($data['genre_id'])) {
            throw new AppException('Genre not found.', 404);
        }
        $copies = Validator::copies($data['total_copies']);
        if ($bookId !== null && $copies < $this->rentals->countActiveRentals($bookId)) {
            throw new AppException('Total copies cannot be lower than the number of books still out.');
        }
        return [
            'title' => Validator::text($data['title'], 'Title', 255),
            'author' => Validator::text($data['author'], 'Author', 255),
            'isbn' => Validator::isbn($data['isbn']),
            'publisher' => Validator::text($data['publisher'], 'Publisher', 255),
            'publication_year' => Validator::year($data['publication_year']),
            'genre_id' => (int) $data['genre_id'],
            'total_copies' => $copies,
            'description' => Validator::text($data['description'], 'Description', 1000),
        ];
    }

    private function formatList($rows) {
        $list = [];
        foreach ($rows as $row) $list[] = $this->format($row);
        return $list;
    }

    private function format($book) {
        foreach (['id', 'genre_id', 'publication_year', 'total_copies', 'available_copies'] as $field) {
            $book[$field] = (int) $book[$field];
        }
        return $book;
    }
}

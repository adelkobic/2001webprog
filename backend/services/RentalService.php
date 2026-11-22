<?php

class RentalService {
    private $rentals;
    private $users;
    private $books;

    public function __construct() {
        $this->rentals = new RentalDao();
        $this->users = new UserDao();
        $this->books = new BookDao();
    }

    public function listFor($user = null) {
        $this->rentals->markOverdue();
        if ($user && $user['role'] === 'patron') {
            return $this->formatList($this->rentals->getByUser($user['id']));
        }
        return $this->formatList($this->rentals->getAll());
    }

    public function getById($id, $user = null) {
        $this->rentals->markOverdue();
        $rental = $this->rentals->getById($id);
        if (!$rental) throw new AppException('Rental not found.', 404);
        if ($user && $user['role'] === 'patron' && (int) $rental['user_id'] !== (int) $user['id']) {
            throw new AppException('You cannot do that.', 403);
        }
        return $this->format($rental);
    }

    public function create($data, $currentUser) {
        Validator::required($data, ['user_id', 'book_id', 'due_date']);
        $due = Validator::dueDate($data['due_date']);
        $patron = $this->users->getById($data['user_id']);
        if (!$patron || $patron['role'] !== 'patron') throw new AppException('Choose a patron.');
        $book = $this->books->getById($data['book_id']);
        if (!$book) throw new AppException('Book not found.', 404);
        $this->rentals->markOverdue();
        if ($this->rentals->countActiveRentals($data['book_id']) >= (int) $book['total_copies']) {
            throw new AppException('No copies of this book are available.');
        }
        $createdBy = $currentUser ? (int) $currentUser['id'] : (int) $patron['id'];
        $id = $this->rentals->insert((int) $patron['id'], (int) $book['id'], $createdBy, date('Y-m-d'), $due);
        return $this->format($this->rentals->getById($id));
    }

    public function update($id, $data) {
        $existing = $this->rentals->getById($id);
        if (!$existing) throw new AppException('Rental not found.', 404);

        Validator::required($data, ['user_id', 'book_id', 'due_date', 'status']);
        $patron = $this->users->getById($data['user_id']);
        if (!$patron || $patron['role'] !== 'patron') {
            throw new AppException('Choose a patron.');
        }
        $book = $this->books->getById($data['book_id']);
        if (!$book) throw new AppException('Book not found.', 404);

        $dueDate = Validator::date($data['due_date'], 'Due date');
        $status = $data['status'];
        if (!in_array($status, ['active', 'overdue', 'returned'], true)) {
            throw new AppException('Rental status is not valid.');
        }

        $returnedAt = null;
        if ($status === 'returned') {
            $returnedAt = isset($data['returned_at']) && $data['returned_at'] !== ''
                ? Validator::date($data['returned_at'], 'Return date')
                : ($existing['returned_at'] ?: date('Y-m-d'));
        } else {
            if (!empty($data['returned_at'])) {
                throw new AppException('Only returned rentals can have a return date.');
            }
            $status = $dueDate < date('Y-m-d') ? 'overdue' : 'active';
        }

        if ($status !== 'returned') {
            $activeCount = $this->rentals->countActiveRentals($book['id'], $id);
            if ($activeCount >= (int) $book['total_copies']) {
                throw new AppException('No copies of this book are available.');
            }
        }

        $rentedAt = Validator::date($existing['rented_at'], 'Rental date');
        if ($dueDate < $rentedAt) {
            throw new AppException('Due date cannot be before the rental date.');
        }
        if ($returnedAt !== null && $returnedAt < $rentedAt) {
            throw new AppException('Return date cannot be before the rental date.');
        }

        $this->rentals->update(
            (int) $id,
            (int) $patron['id'],
            (int) $book['id'],
            (int) $existing['created_by'],
            $rentedAt,
            $dueDate,
            $returnedAt,
            $status
        );
        return $this->format($this->rentals->getById($id));
    }

    public function markReturned($id) {
        $this->rentals->markOverdue();
        $rental = $this->rentals->getById($id);
        if (!$rental) throw new AppException('Rental not found.', 404);
        if ($rental['status'] === 'returned') throw new AppException('This rental is already returned.');
        $this->rentals->markReturned($id, date('Y-m-d'));
        return $this->format($this->rentals->getById($id));
    }

    public function delete($id) {
        if (!$this->rentals->getById($id)) throw new AppException('Rental not found.', 404);
        $this->rentals->delete($id);
    }

    private function formatList($rows) {
        $list = [];
        foreach ($rows as $row) $list[] = $this->format($row);
        return $list;
    }

    private function format($rental) {
        foreach (['id', 'user_id', 'book_id', 'created_by'] as $field) {
            $rental[$field] = (int) $rental[$field];
        }
        return $rental;
    }
}

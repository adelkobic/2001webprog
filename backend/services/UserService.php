<?php

use Firebase\JWT\JWT;

class UserService {
    private $users;

    public function __construct() {
        $this->users = new UserDao();
    }

    public function register($data) {
        $clean = $this->checkPerson($data, true);
        if ($this->users->getByEmail($clean['email'])) {
            throw new AppException('That email is already used.');
        }
        $id = $this->users->insert(
            $clean['first_name'], $clean['last_name'], $clean['email'], $clean['phone'],
            password_hash($clean['password'], PASSWORD_DEFAULT), 'patron'
        );
        return $this->publicUser($this->users->getById($id));
    }

    public function login($data) {
        Validator::required($data, ['email', 'password']);
        Validator::email($data['email']);
        $user = $this->users->getByEmail(trim($data['email']));
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            throw new AppException('Email or password is wrong.', 401);
        }
        $public = $this->publicUser($user);
        $config = Flight::get('config');
        $secret = (string) $config['jwt_secret'];
        if (strlen($secret) < 32) {
            throw new AppException('The JWT secret in config.php must be at least 32 characters.');
        }
        $payload = [
            'id' => $public['id'],
            'role' => $public['role'],
            'email' => $public['email'],
            'first_name' => $public['first_name'],
            'last_name' => $public['last_name'],
            'iat' => time(),
            'exp' => time() + ((int) $config['jwt_hours'] * 3600),
        ];
        return ['token' => JWT::encode($payload, $secret, 'HS256'), 'user' => $public];
    }

    public function getAll($role = '') {
        if ($role) {
            Validator::role($role);
            return $this->formatList($this->users->getByRole($role));
        }
        return $this->formatList($this->users->getAll());
    }

    public function getById($id) {
        $user = $this->users->getById($id);
        if (!$user) throw new AppException('User not found.', 404);
        return $this->publicUser($user);
    }

    public function create($data) {
        $clean = $this->checkPerson($data, true);
        Validator::required($data, ['role']);
        $role = Validator::role($data['role']);
        if ($this->users->getByEmail($clean['email'])) {
            throw new AppException('That email is already used.');
        }
        $id = $this->users->insert(
            $clean['first_name'], $clean['last_name'], $clean['email'], $clean['phone'],
            password_hash($clean['password'], PASSWORD_DEFAULT), $role
        );
        return $this->publicUser($this->users->getById($id));
    }

    public function update($id, $data) {
        if (!$this->users->getById($id)) throw new AppException('User not found.', 404);
        $clean = $this->checkPerson($data, false);
        Validator::required($data, ['role']);
        $role = Validator::role($data['role']);
        $other = $this->users->getByEmail($clean['email']);
        if ($other && (int) $other['id'] !== (int) $id) {
            throw new AppException('That email is already used.');
        }
        $hash = null;
        if (isset($data['password']) && trim((string) $data['password']) !== '') {
            Validator::password($data['password']);
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->users->update($id, $clean['first_name'], $clean['last_name'], $clean['email'], $clean['phone'], $role, $hash);
        return $this->publicUser($this->users->getById($id));
    }

    public function delete($id, $currentUser = null) {
        if ($currentUser && (int) $currentUser['id'] === (int) $id) {
            throw new AppException('You cannot delete your own account.');
        }
        if (!$this->users->getById($id)) throw new AppException('User not found.', 404);
        if ($this->users->countRentals($id) > 0 || $this->users->countReviews($id) > 0) {
            throw new AppException('This user has rentals or reviews, so they cannot be deleted.');
        }
        $this->users->delete($id);
    }

    private function checkPerson($data, $passwordRequired) {
        Validator::required($data, ['first_name', 'last_name', 'email', 'phone']);
        if ($passwordRequired) Validator::required($data, ['password']);
        $email = trim($data['email']);
        Validator::email($email);
        Validator::phone($data['phone']);
        if ($passwordRequired) Validator::password($data['password']);
        return [
            'first_name' => Validator::text($data['first_name'], 'First name', 100),
            'last_name' => Validator::text($data['last_name'], 'Last name', 100),
            'email' => $email,
            'phone' => trim($data['phone']),
            'password' => $data['password'] ?? '',
        ];
    }

    private function formatList($rows) {
        $list = [];
        foreach ($rows as $row) $list[] = $this->publicUser($row);
        return $list;
    }

    private function publicUser($user) {
        unset($user['password_hash']);
        $user['id'] = (int) $user['id'];
        return $user;
    }
}

<?php

class RoleMiddleware {
    private $roles;

    public function __construct($roles) {
        $this->roles = $roles;
    }

    public function before($params) {
        $user = Flight::get('user');
        if (!$user || !in_array($user['role'] ?? '', $this->roles, true)) {
            throw new AppException('You do not have permission to do that.', 403);
        }
    }
}

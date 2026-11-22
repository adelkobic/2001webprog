<?php

role_group(['admin', 'staff'], function () {
    Flight::route('GET /users', function () {
        $query = query_params();
        $current = current_user();
        $role = $current['role'] === 'staff' ? 'patron' : ($query['role'] ?? '');
        send_json((new UserService())->getAll($role));
    });
    Flight::route('GET /users/@id', function ($id) {
        $user = (new UserService())->getById($id);
        $current = current_user();
        if (($current['role'] ?? '') === 'staff' && ($user['role'] ?? '') !== 'patron') {
            throw new AppException('You cannot do that.', 403);
        }
        send_json($user);
    });
});

role_group(['admin'], function () {
    Flight::route('POST /users', function () {
        send_json((new UserService())->create(request_body()), 201);
    });
    Flight::route('PUT /users/@id', function ($id) {
        send_json((new UserService())->update($id, request_body()));
    });
    Flight::route('DELETE /users/@id', function ($id) {
        (new UserService())->delete($id, current_user());
        send_json(['message' => 'User deleted.']);
    });
});

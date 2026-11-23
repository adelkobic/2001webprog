<?php

public_group(function () {
    Flight::route('GET /health', function () {
        send_json(['ok' => true]);
    });
    Flight::route('POST /auth/register', function () {
        send_json((new UserService())->register(request_body()), 201);
    });
    Flight::route('POST /auth/login', function () {
        send_json((new UserService())->login(request_body()));
    });
});

auth_group(function () {
    Flight::route('GET /auth/me', function () {
        send_json(current_user());
    });
});

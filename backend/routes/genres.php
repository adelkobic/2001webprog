<?php

auth_group(function () {
    Flight::route('GET /genres', function () {
        send_json((new GenreService())->getAll());
    });
    Flight::route('GET /genres/@id', function ($id) {
        send_json((new GenreService())->getById($id));
    });
});

role_group(['admin'], function () {
    Flight::route('POST /genres', function () {
        send_json((new GenreService())->create(request_body()), 201);
    });
    Flight::route('PUT /genres/@id', function ($id) {
        send_json((new GenreService())->update($id, request_body()));
    });
    Flight::route('DELETE /genres/@id', function ($id) {
        (new GenreService())->delete($id);
        send_json(['message' => 'Genre deleted.']);
    });
});

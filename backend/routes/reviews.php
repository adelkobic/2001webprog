<?php

auth_group(function () {
    Flight::route('GET /reviews', function () {
        $query = query_params();
        send_json((new ReviewService())->list($query['book_id'] ?? ''));
    });
    Flight::route('GET /reviews/@id', function ($id) {
        send_json((new ReviewService())->getById($id));
    });
});

role_group(['patron', 'admin'], function () {
    Flight::route('POST /reviews', function () {
        send_json((new ReviewService())->save(request_body(), current_user()), 201);
    });
    Flight::route('PUT /reviews/@id', function ($id) {
        send_json((new ReviewService())->update($id, request_body(), current_user()));
    });
    Flight::route('DELETE /reviews/@id', function ($id) {
        (new ReviewService())->delete($id, current_user());
        send_json(['message' => 'Review deleted.']);
    });
});

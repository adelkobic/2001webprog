<?php

auth_group(function () {
    Flight::route('GET /rentals', function () {
        send_json((new RentalService())->listFor(current_user()));
    });
    Flight::route('GET /rentals/@id', function ($id) {
        send_json((new RentalService())->getById($id, current_user()));
    });
});

role_group(['admin', 'staff'], function () {
    Flight::route('POST /rentals', function () {
        send_json((new RentalService())->create(request_body(), current_user()), 201);
    });
    Flight::route('PUT /rentals/@id', function ($id) {
        send_json((new RentalService())->update($id, request_body()));
    });
    Flight::route('PATCH /rentals/@id/return', function ($id) {
        send_json((new RentalService())->markReturned($id));
    });
});

role_group(['admin'], function () {
    Flight::route('DELETE /rentals/@id', function ($id) {
        (new RentalService())->delete($id);
        send_json(['message' => 'Rental deleted.']);
    });
});

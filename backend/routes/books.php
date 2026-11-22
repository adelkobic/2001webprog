<?php

public_group(function () {
    Flight::route('GET /books/cover', function () {
        $query = query_params();
        Validator::required($query, ['isbn']);
        $isbn = Validator::isbn($query['isbn']);
        send_json((new OpenLibraryService())->coverForIsbn($isbn));
    });
});

auth_group(function () {
    Flight::route('GET /books', function () {
        $query = query_params();
        send_json((new BookService())->search($query['search'] ?? '', $query['genre_id'] ?? 0));
    });
    Flight::route('GET /books/@id', function ($id) {
        send_json((new BookService())->getById($id));
    });
});

role_group(['admin'], function () {
    Flight::route('POST /books', function () {
        send_json((new BookService())->create(request_body()), 201);
    });
    Flight::route('PUT /books/@id', function ($id) {
        send_json((new BookService())->update($id, request_body()));
    });
    Flight::route('DELETE /books/@id', function ($id) {
        (new BookService())->delete($id);
        send_json(['message' => 'Book deleted.']);
    });
});

# Library project notes for AI

## About the app:

This is a library website where admins manage books, genres, and users. Admins and staff lend books to patrons. Patrons browse the book catalog, view their own rentals, and are able to leave reviews for books featured in the catalog.

## Tech stack

- Frontend: Single Page Application using HTML, CSS, and plain JavaScript. Bootstrap 5 is retrieved via CDN.
- Backend: PHP, FlightPHP, PDO, MySQL.
- Auth: JWT (firebase/php-jwt).
- Covers: Fetched from the Open Library API, via a given book's ISBN.

Do NOT add React, Angular, Vue, Laravel, or any other framework.

## Folder structure rules

- `frontend/` contains HTML, CSS, and JS only.
- `backend/` is PHP only. Do NOT use PHP to render HTML pages - keep everything separate.
- `frontend/views/` files are HTML only. They do not call the API.
- Only `frontend/js/services/` may use `fetch` to talk to the API.
- `frontend/js/router.js` may use `fetch` only to load an HTML view file.
- Routes call services. Services call DAOs. Only DAO files are allowed to run SQL.
- Use prepared statements. Never concatenate user input into SQL.
- Hash passwords with `password_hash` and check with `password_verify`.
- Secrets are to be kept in `backend/config/config.php`. This file to be ignored during commits. Update `config.example.php` instead.
- Database names are snake_case.

## Naming

- PHP classes: `BookDao`, `BookService`, one class per file.
- JS files: `bookService.js`, `catalogPage.js`.
- API paths: `/api/books`, `/api/rentals`, etc.
- Roles stored in the database: `admin`, `staff`, `patron`.

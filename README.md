# Library

Library Book Management website. Library patrons can register, browse the book catalog, see their loans, and write reviews for books. Staff and admins lend the books to the patrons.

The frontend is a single-page app whose pages change without a full reload.

The Milestone 1 ERD, Milestone 2 MySQL schema and DAO layer, and Milestone 3 FlightPHP services/routes/OpenAPI implementation are in place. See [docs/erd.md](docs/erd.md).

## Entities

- **users** — first name, last name, email, phone, password, and a role (admin, staff, or patron).
- **genres** — self-explanatory. Books retreive their genres from this list of genres.
- **books** — title, author, ISBN, publisher, publication year, genre, total copies & short description.
- **rentals** — patron, book, due date & status (active, returned, or overdue). Staff/Admins create rentals.
- **reviews** — a rating from 1 to 5 and a comment. One patron can review a book only once.

A genre can have many books. A patron has many rentals and many reviews. A single book has many rentals and many reviews.

Available copies are not stored. They are the total no. of copies minus rentals that are still active or overdue.

## Roles

- **Patron** can register, log in, browse the catalog, see their own rentals & review books.
- **Staff** can log in, browse the catalog, lend books to patrons, mark it returned, and browse all rentals. Admins create staff accounts.
- **Admin** can do everything staff can do, as well as add, edit, and delete books, genres, users, rentals, and reviews.

## Test accounts

Test login data is displayed in plaintext on the login page.

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@library.local | admin123 |
| Staff | staff@library.local | staff123 |
| Patron | patron@library.local | patron123 |
| Patron | patron2@library.local | patron123 |

## External API

Book covers come from Open Library through the backend endpoint:

`GET /api/books/cover?isbn={isbn}`

The catalog and book-detail page request covers through `frontend/js/services/openLibraryService.js`; that service calls the backend rather than contacting Open Library directly. The backend calls `https://openlibrary.org/api/books` using the book's ISBN and applies a five-second timeout. If metadata is unavailable or has no cover, it falls back to the ISBN-based `covers.openlibrary.org` image URL; if that image is also missing, the frontend shows the local placeholder. Successful and no-cover responses are cached for seven days. External API failures are written to `backend/logs/error.log`. Open Library does not require an API key.

The ISBN is sent directly, so cover lookup does not depend on the catalog book already being present in MySQL. If there is no cover or the external service cannot be reached, the catalog keeps the local placeholder and exposes the reason in the image tooltip.

## Set up the database

You need MySQL. Import [database/library.sql](database/library.sql) using MySQL Workbench or phpMyAdmin. The script creates the `library` database, five tables, and sample rows. Re-importing the script drops and recreates those tables.

Copy `backend/config/config.example.php` to `backend/config/config.php`, then set the MySQL host, database name, username, password, JWT secret, and frontend URL. The local `config.php` is ignored by Git and must not be committed.

The DAO classes use PDO and prepared statements. Services contain validation and application rules. Routes call services rather than DAOs directly.

## Run the API

You need PHP 8.1 or newer and Composer:

```
composer install
php -S localhost:8000 -t backend/public backend/public/router.php
```

Useful addresses:

- Health: http://localhost:8000/api/health
- OpenAPI YAML: http://localhost:8000/api/openapi.yaml
- Admin Swagger UI: http://localhost:5500/#/admin/swagger
- Book cover: http://localhost:8000/api/books/cover?isbn=9780547928227 (public read-only lookup)

The local frontend API origin is configured in `frontend/js/config.js`. Update it when using a different API host.

Start the frontend in a second terminal:

```
php -S localhost:5500 -t frontend
```

Open http://localhost:5500.

## Milestone status

Milestones 1-3 are implemented in this repository. Milestone 4 will add the fully connected frontend, complete logging middleware, and final deployed frontend/backend integration.

# Library

Library Book Management website. Library patrons can register, browse the book catalog, see their loans, and write reviews for books. Staff and admins lend the books to the patrons.

This milestone includes the static site. The pages change without a full reload. The sample data is in JSON files in `frontend/data/`. Nothing is saved to a server. Refreshing returns the lists back to those files.

The database is WIP. (See diagram at [docs/erd.md](docs/erd.md)).

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

Test login data is displayed in plaintext on the login page. Login functionality is currently not implemented.

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@library.local | admin123 |
| Staff | staff@library.local | staff123 |
| Patron | patron@library.local | patron123 |
| Patron | patron2@library.local | patron123 |

## External API

Book covers will come from Open Library, utilizing the ISBN. The catalog and the book page already leave a space for the cover. For now the image address is:

`https://covers.openlibrary.org/b/isbn/{isbn}-M.jpg`

If that image is missing, the page shows `frontend/assets/no-cover.png`. A later milestone should call the Open Library API from the backend, with a timeout and an error log.

## Run the pages

From this folder:

```
npx --yes serve frontend
```

Open the address it prints. The usual one is http://localhost:3000.

You can also open `frontend/index.html` with the Live Server extension. Opening the file directly from the disk will not load the JSON files.

## Later

The API, Swagger, and the live site are not part of this milestone. The `backend/` folders are left empty for now.

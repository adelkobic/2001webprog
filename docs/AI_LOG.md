# AI log

I used Cursor for this project.

## Milestone 1

Tool: Cursor.

What it wrote: the folders, the static pages, the hash router, the navbar, and the ERD.

Main prompt: build a simple library SPA with HTML, CSS, and JavaScript. 

Mistake 1: The pages called a PHP API. This milestone does not have a backend yet. I took those calls out. The sample data is in JSON files, and login checks a hardcoded password in the browser.

Mistake 2: Genre was just a text field on the book, so the diagram only had four entities. Genre is its own table, and the book form uses a dropdown.

## Milestone 2

Tool: GitHub Copilot.

What it helped with: the MySQL schema and sample data, the shared PDO base DAO, and DAOs for users, genres, books, rentals, and reviews.

Main prompt: implement database creation and DAO CRUD for the library app, following the reference implementation including the five-table schema and using prepared statements.

Mistake 1: The initial DAO pass did not provide a general rental update method. I noticed while checking the required CRUD operations and added `RentalDao::update`.

Mistake 2: The first local setup draft included a real `config.php`. That file must not be committed, so I removed it and kept only `config.example.php`; users create their local config from the template.

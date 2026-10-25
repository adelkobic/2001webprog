# AI log

I used Cursor for this project.

## Milestone 1

Tool: Cursor.

What it wrote: the folders, the static pages, the hash router, the navbar, and the ERD.

Main prompt: build a simple library SPA with HTML, CSS, and JavaScript. 

Mistake 1: The pages called a PHP API. This milestone does not have a backend yet. I took those calls out. The sample data is in JSON files, and login checks a hardcoded password in the browser.

Mistake 2: Genre was just a text field on the book, so the diagram only had four entities. Genre is its own table, and the book form uses a dropdown.

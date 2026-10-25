function showAdminBooksPage() {
    fillGenreSelect();
    drawBookTable();

    document.getElementById('book-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var data = {
            title: fieldValue('title'),
            author: fieldValue('author'),
            isbn: fieldValue('isbn'),
            publisher: fieldValue('publisher'),
            publication_year: Number(fieldValue('publication_year')),
            genre_id: Number(document.getElementById('genre_id').value),
            total_copies: Number(fieldValue('total_copies')),
            description: fieldValue('description')
        };
        if (!data.title || !data.author || !data.isbn || !data.publisher || !data.genre_id || !data.description || data.total_copies < 1) {
            showAlert('page-alert', 'Fill in every field. Copies must be at least 1.');
            return;
        }
        showAlert('page-alert', '');
        var id = document.getElementById('book-id').value;
        if (id) {
            var book = Store.find(Store.books, id);
            data.id = book.id;
            Object.assign(book, data);
        } else {
            data.id = Store.nextId(Store.books);
            Store.books.push(data);
        }
        resetBookForm();
        drawBookTable();
    });

    document.getElementById('cancel-book').addEventListener('click', resetBookForm);

    document.getElementById('book-table').addEventListener('click', function (event) {
        var editButton = event.target.closest('[data-edit-book]');
        var deleteButton = event.target.closest('[data-delete-book]');
        if (editButton) {
            fillBookForm(editButton.getAttribute('data-edit-book'));
        }
        if (deleteButton) {
            if (!window.confirm('Delete this book?')) {
                return;
            }
            Store.books = Store.books.filter(function (book) {
                return String(book.id) !== deleteButton.getAttribute('data-delete-book');
            });
            resetBookForm();
            drawBookTable();
        }
    });
}

function fillGenreSelect() {
    var html = '<option value="">Choose a genre</option>';
    Store.genres.forEach(function (genre) {
        html += '<option value="' + genre.id + '">' + escapeHtml(genre.name) + '</option>';
    });
    document.getElementById('genre_id').innerHTML = html;
}

function drawBookTable() {
    var html = '<table class="table bg-white"><thead><tr>';
    html += '<th>Title</th><th>Author</th><th>Genre</th><th>Copies</th><th></th>';
    html += '</tr></thead><tbody>';
    Store.books.forEach(function (book) {
        html += '<tr>';
        html += '<td>' + escapeHtml(book.title) + '</td>';
        html += '<td>' + escapeHtml(book.author) + '</td>';
        html += '<td>' + escapeHtml(Store.genreName(book.genre_id)) + '</td>';
        html += '<td>' + copiesLeft(book) + ' / ' + book.total_copies + '</td>';
        html += '<td><button class="btn btn-sm btn-outline-primary me-2" type="button" data-edit-book="' + book.id + '">Edit</button>';
        html += '<button class="btn btn-sm btn-outline-danger" type="button" data-delete-book="' + book.id + '">Delete</button></td>';
        html += '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('book-table').innerHTML = html;
}

function fillBookForm(id) {
    var book = Store.find(Store.books, id);
    document.getElementById('book-id').value = book.id;
    document.getElementById('title').value = book.title;
    document.getElementById('author').value = book.author;
    document.getElementById('isbn').value = book.isbn;
    document.getElementById('publisher').value = book.publisher;
    document.getElementById('publication_year').value = book.publication_year;
    document.getElementById('genre_id').value = book.genre_id;
    document.getElementById('total_copies').value = book.total_copies;
    document.getElementById('description').value = book.description;
    document.getElementById('save-book').textContent = 'Save changes';
    document.getElementById('cancel-book').classList.remove('d-none');
}

function resetBookForm() {
    document.getElementById('book-form').reset();
    document.getElementById('book-id').value = '';
    document.getElementById('save-book').textContent = 'Add book';
    document.getElementById('cancel-book').classList.add('d-none');
}

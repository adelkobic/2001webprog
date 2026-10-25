function showAdminGenresPage() {
    drawGenreTable();

    document.getElementById('genre-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var name = fieldValue('name');
        if (!name) {
            showAlert('page-alert', 'Enter a name.');
            return;
        }
        var duplicate = false;
        Store.genres.forEach(function (genre) {
            if (genre.name.toLowerCase() === name.toLowerCase() && String(genre.id) !== document.getElementById('genre-id').value) {
                duplicate = true;
            }
        });
        if (duplicate) {
            showAlert('page-alert', 'That genre already exists.');
            return;
        }
        showAlert('page-alert', '');
        var id = document.getElementById('genre-id').value;
        if (id) {
            Store.find(Store.genres, id).name = name;
        } else {
            Store.genres.push({ id: Store.nextId(Store.genres), name: name });
        }
        resetGenreForm();
        drawGenreTable();
    });

    document.getElementById('cancel-genre').addEventListener('click', resetGenreForm);

    document.getElementById('genre-table').addEventListener('click', function (event) {
        var editButton = event.target.closest('[data-edit-genre]');
        var deleteButton = event.target.closest('[data-delete-genre]');
        if (editButton) {
            document.getElementById('genre-id').value = editButton.getAttribute('data-edit-genre');
            document.getElementById('name').value = editButton.getAttribute('data-name');
            document.getElementById('save-genre').textContent = 'Save changes';
            document.getElementById('cancel-genre').classList.remove('d-none');
        }
        if (deleteButton) {
            var genreId = deleteButton.getAttribute('data-delete-genre');
            var used = Store.books.some(function (book) {
                return String(book.genre_id) === genreId;
            });
            if (used) {
                showAlert('page-alert', 'A book still uses this genre.');
                return;
            }
            if (!window.confirm('Delete this genre?')) {
                return;
            }
            Store.genres = Store.genres.filter(function (genre) {
                return String(genre.id) !== genreId;
            });
            resetGenreForm();
            drawGenreTable();
        }
    });
}

function drawGenreTable() {
    var html = '<table class="table bg-white"><thead><tr><th>Name</th><th></th></tr></thead><tbody>';
    Store.genres.forEach(function (genre) {
        html += '<tr><td>' + escapeHtml(genre.name) + '</td><td>';
        html += '<button class="btn btn-sm btn-outline-primary me-2" type="button" data-edit-genre="' + genre.id + '" data-name="' + escapeHtml(genre.name) + '">Edit</button>';
        html += '<button class="btn btn-sm btn-outline-danger" type="button" data-delete-genre="' + genre.id + '">Delete</button>';
        html += '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById('genre-table').innerHTML = html;
}

function resetGenreForm() {
    document.getElementById('genre-form').reset();
    document.getElementById('genre-id').value = '';
    document.getElementById('save-genre').textContent = 'Add genre';
    document.getElementById('cancel-genre').classList.add('d-none');
}

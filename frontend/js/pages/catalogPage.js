function showCatalogPage() {
    var select = document.getElementById('genre-filter');
    var html = '<option value="">All genres</option>';
    Store.genres.forEach(function (genre) {
        html += '<option value="' + genre.id + '">' + escapeHtml(genre.name) + '</option>';
    });
    select.innerHTML = html;
    drawBooks();

    document.getElementById('search-form').addEventListener('submit', function (event) {
        event.preventDefault();
        drawBooks();
    });
}

function drawBooks() {
    var search = fieldValue('search').toLowerCase();
    var genreId = document.getElementById('genre-filter').value;
    var books = Store.books.filter(function (book) {
        var text = (book.title + ' ' + book.author + ' ' + book.isbn).toLowerCase();
        if (search && text.indexOf(search) === -1) {
            return false;
        }
        if (genreId && String(book.genre_id) !== genreId) {
            return false;
        }
        return true;
    });

    var box = document.getElementById('book-list');
    if (!books.length) {
        box.innerHTML = '<p>No books found.</p>';
        return;
    }

    var html = '<div class="row">';
    books.forEach(function (book) {
        html += '<div class="col-md-6 col-lg-4 mb-3"><div class="card h-100">';
        html += '<img class="book-cover" id="cover-' + book.id + '" src="assets/no-cover.png" alt="Cover of ' + escapeHtml(book.title) + '">';
        html += '<div class="card-body">';
        html += '<h2 class="h5">' + escapeHtml(book.title) + '</h2>';
        html += '<p class="mb-1">' + escapeHtml(book.author) + '</p>';
        html += '<p class="mb-1 text-muted">' + escapeHtml(Store.genreName(book.genre_id)) + '</p>';
        html += '<p class="mb-2">' + copiesLeft(book) + ' of ' + book.total_copies + ' available</p>';
        html += '<a class="btn btn-outline-primary btn-sm" href="#/books/' + book.id + '">Details</a>';
        html += '</div></div></div>';
    });
    html += '</div>';
    box.innerHTML = html;

    books.forEach(function (book) {
        showCoverWhenReady(book);
    });
}

function showCoverWhenReady(book) {
    var image = document.getElementById('cover-' + book.id);
    var isbn = String(book.isbn || '').replace(/[^0-9Xx]/g, '');
    if (!image || !isbn) {
        return;
    }
    image.onerror = function () {
        image.onerror = null;
        image.src = 'assets/no-cover.png';
    };
    image.src = 'https://covers.openlibrary.org/b/isbn/' + encodeURIComponent(isbn) + '-M.jpg?default=false';
}

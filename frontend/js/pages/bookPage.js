function showBookPage(bookId) {
    var book = Store.find(Store.books, bookId);
    var user = Store.currentUser();
    if (!book) {
        showAlert('page-alert', 'Book not found.');
        return;
    }

    var html = '<div class="row"><div class="col-md-4 mb-3">';
    html += '<img class="detail-cover" id="cover-' + book.id + '" src="assets/no-cover.png" alt="Cover of ' + escapeHtml(book.title) + '">';
    html += '<p class="text-muted small mt-2">Cover from Open Library, using the ISBN.</p>';
    html += '</div><div class="col-md-8">';
    html += '<h1 class="h3">' + escapeHtml(book.title) + '</h1>';
    html += '<p>' + escapeHtml(book.author) + '</p>';
    html += '<p>ISBN: ' + escapeHtml(book.isbn) + '</p>';
    html += '<p>Publisher: ' + escapeHtml(book.publisher) + ' (' + book.publication_year + ')</p>';
    html += '<p>Genre: ' + escapeHtml(Store.genreName(book.genre_id)) + '</p>';
    html += '<p>' + copiesLeft(book) + ' of ' + book.total_copies + ' available</p>';
    html += '<p>' + escapeHtml(book.description) + '</p>';
    html += '</div></div>';
    document.getElementById('book-detail').innerHTML = html;
    showCoverWhenReady(book);

    if (user && user.role === 'patron') {
        document.getElementById('review-form').classList.remove('d-none');
    }
    drawReviews(bookId);

    document.getElementById('review-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var rating = Number(fieldValue('rating'));
        var comment = fieldValue('comment');
        if (rating < 1 || rating > 5 || !comment) {
            showAlert('review-error', 'Give a rating from 1 to 5 and a comment.');
            return;
        }
        showAlert('review-error', '');
        var existing = null;
        Store.reviews.forEach(function (review) {
            if (review.user_id === user.id && String(review.book_id) === String(bookId)) {
                existing = review;
            }
        });
        if (existing) {
            existing.rating = rating;
            existing.comment = comment;
        } else {
            Store.reviews.push({
                id: Store.nextId(Store.reviews),
                user_id: user.id,
                book_id: Number(bookId),
                rating: rating,
                comment: comment
            });
        }
        drawReviews(bookId);
    });
}

function drawReviews(bookId) {
    var user = Store.currentUser();
    var reviews = Store.reviews.filter(function (review) {
        return String(review.book_id) === String(bookId);
    });
    var box = document.getElementById('review-list');
    if (!reviews.length) {
        box.innerHTML = '<p>No reviews yet.</p>';
        return;
    }
    var html = '';
    reviews.forEach(function (review) {
        html += '<div class="border-bottom py-2">';
        html += '<strong>' + escapeHtml(Store.personName(review.user_id)) + '</strong>';
        html += ' <span>' + review.rating + '/5</span>';
        html += '<p class="mb-1">' + escapeHtml(review.comment) + '</p>';
        if (user && (user.role === 'admin' || user.id === review.user_id)) {
            html += '<button class="btn btn-outline-danger btn-sm" type="button" data-delete-review="' + review.id + '">Delete</button>';
        }
        html += '</div>';
        if (user && user.id === review.user_id) {
            document.getElementById('rating').value = review.rating;
            document.getElementById('comment').value = review.comment;
        }
    });
    box.innerHTML = html;

    box.onclick = function (event) {
        var button = event.target.closest('[data-delete-review]');
        if (!button) {
            return;
        }
        if (!window.confirm('Delete this review?')) {
            return;
        }
        Store.reviews = Store.reviews.filter(function (review) {
            return String(review.id) !== button.getAttribute('data-delete-review');
        });
        drawReviews(bookId);
    };
}

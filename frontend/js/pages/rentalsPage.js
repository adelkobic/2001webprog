function showRentalsPage() {
    document.getElementById('due_date').min = todayText();
    fillRentalChoices();
    drawRentalTable();

    document.getElementById('rental-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var userId = Number(document.getElementById('user_id').value);
        var bookId = Number(document.getElementById('book_id').value);
        var dueDate = fieldValue('due_date');
        var book = Store.find(Store.books, bookId);
        if (!userId || !bookId || !dueDate) {
            showAlert('page-alert', 'Choose a patron, a book, and a due date.');
            return;
        }
        if (dueDate < todayText()) {
            showAlert('page-alert', 'The due date cannot be in the past.');
            return;
        }
        if (!book || copiesLeft(book) < 1) {
            showAlert('page-alert', 'No copies of that book are available.');
            return;
        }
        showAlert('page-alert', '');
        var staff = Store.currentUser();
        Store.rentals.push({
            id: Store.nextId(Store.rentals),
            user_id: userId,
            book_id: bookId,
            created_by: staff.id,
            rented_at: todayText(),
            due_date: dueDate,
            returned_at: null,
            status: 'active'
        });
        document.getElementById('rental-form').reset();
        document.getElementById('due_date').min = todayText();
        fillRentalChoices();
        drawRentalTable();
    });

    document.getElementById('rental-table').addEventListener('click', function (event) {
        var returnButton = event.target.closest('[data-return]');
        var deleteButton = event.target.closest('[data-delete-rental]');
        if (returnButton) {
            var rental = Store.find(Store.rentals, returnButton.getAttribute('data-return'));
            rental.status = 'returned';
            rental.returned_at = todayText();
            fillRentalChoices();
            drawRentalTable();
        }
        if (deleteButton) {
            if (!window.confirm('Delete this rental?')) {
                return;
            }
            Store.rentals = Store.rentals.filter(function (rental) {
                return String(rental.id) !== deleteButton.getAttribute('data-delete-rental');
            });
            fillRentalChoices();
            drawRentalTable();
        }
    });
}

function fillRentalChoices() {
    var patrons = '<option value="">Choose a patron</option>';
    Store.users.forEach(function (user) {
        if (user.role === 'patron') {
            patrons += '<option value="' + user.id + '">' + escapeHtml(user.first_name + ' ' + user.last_name) + '</option>';
        }
    });
    document.getElementById('user_id').innerHTML = patrons;

    var books = '<option value="">Choose a book</option>';
    Store.books.forEach(function (book) {
        var left = copiesLeft(book);
        if (left > 0) {
            books += '<option value="' + book.id + '">' + escapeHtml(book.title) + ' (' + left + ' left)</option>';
        }
    });
    document.getElementById('book_id').innerHTML = books;
}

function drawRentalTable() {
    document.getElementById('rental-table').innerHTML = rentalTable(Store.rentals, true);
}

function showMyRentalsPage() {
    var user = Store.currentUser();
    var rentals = Store.rentals.filter(function (rental) {
        return rental.user_id === user.id;
    });
    document.getElementById('rental-table').innerHTML = rentalTable(rentals, false);
}

function rentalTable(rentals, withActions) {
    if (!rentals.length) {
        return '<p>No rentals.</p>';
    }
    var user = Store.currentUser();
    var html = '<table class="table bg-white"><thead><tr>';
    html += '<th>Book</th><th>Patron</th><th>Due</th><th>Status</th>';
    if (withActions) {
        html += '<th></th>';
    }
    html += '</tr></thead><tbody>';
    rentals.forEach(function (rental) {
        html += '<tr>';
        html += '<td>' + escapeHtml(Store.bookTitle(rental.book_id)) + '</td>';
        html += '<td>' + escapeHtml(Store.personName(rental.user_id)) + '</td>';
        html += '<td>' + escapeHtml(rental.due_date) + '</td>';
        html += '<td>' + statusBadge(rentalStatus(rental)) + '</td>';
        if (withActions) {
            html += '<td>';
            if (rental.status !== 'returned') {
                html += '<button class="btn btn-sm btn-success me-2" type="button" data-return="' + rental.id + '">Return</button>';
            }
            if (user && user.role === 'admin') {
                html += '<button class="btn btn-sm btn-outline-danger" type="button" data-delete-rental="' + rental.id + '">Delete</button>';
            }
            html += '</td>';
        }
        html += '</tr>';
    });
    html += '</tbody></table>';
    return html;
}

var Store = {
    users: [],
    genres: [],
    books: [],
    rentals: [],
    reviews: [],

    load: function () {
        var self = this;
        return Promise.all([
            this.read('data/users.json'),
            this.read('data/genres.json'),
            this.read('data/books.json'),
            this.read('data/rentals.json'),
            this.read('data/reviews.json')
        ]).then(function (rows) {
            self.users = rows[0];
            self.genres = rows[1];
            self.books = rows[2];
            self.rentals = rows[3];
            self.reviews = rows[4];
        });
    },

    read: function (path) {
        return fetch(path).then(function (response) {
            if (!response.ok) {
                throw new Error('Could not load ' + path);
            }
            return response.json();
        });
    },

    currentUser: function () {
        var text = sessionStorage.getItem('user');
        return text ? JSON.parse(text) : null;
    },

    login: function (email, password) {
        var found = null;
        this.users.forEach(function (user) {
            if (user.email === email && user.password === password) {
                found = user;
            }
        });
        if (!found) {
            return null;
        }
        var copy = {
            id: found.id,
            first_name: found.first_name,
            last_name: found.last_name,
            email: found.email,
            role: found.role
        };
        sessionStorage.setItem('user', JSON.stringify(copy));
        return copy;
    },

    logout: function () {
        sessionStorage.removeItem('user');
    },

    nextId: function (list) {
        var max = 0;
        list.forEach(function (item) {
            if (item.id > max) {
                max = item.id;
            }
        });
        return max + 1;
    },

    find: function (list, id) {
        var found = null;
        list.forEach(function (item) {
            if (String(item.id) === String(id)) {
                found = item;
            }
        });
        return found;
    },

    genreName: function (id) {
        var genre = this.find(this.genres, id);
        return genre ? genre.name : '';
    },

    personName: function (id) {
        var user = this.find(this.users, id);
        return user ? user.first_name + ' ' + user.last_name : '';
    },

    bookTitle: function (id) {
        var book = this.find(this.books, id);
        return book ? book.title : '';
    }
};

function rentalStatus(rental) {
    if (rental.status === 'active' && rental.due_date < todayText()) {
        return 'overdue';
    }
    return rental.status;
}

function copiesLeft(book) {
    var out = 0;
    Store.rentals.forEach(function (rental) {
        var status = rentalStatus(rental);
        if (rental.book_id === book.id && (status === 'active' || status === 'overdue')) {
            out += 1;
        }
    });
    return book.total_copies - out;
}

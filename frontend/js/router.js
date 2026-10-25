var Router = {
    routes: {
        '/login': { view: 'views/login.html', show: showLoginPage, guest: true },
        '/register': { view: 'views/register.html', show: showRegisterPage, guest: true },
        '/catalog': { view: 'views/catalog.html', show: showCatalogPage, roles: ['admin', 'staff', 'patron'] },
        '/my-rentals': { view: 'views/my-rentals.html', show: showMyRentalsPage, roles: ['admin', 'staff', 'patron'] },
        '/admin/books': { view: 'views/admin-books.html', show: showAdminBooksPage, roles: ['admin'] },
        '/admin/genres': { view: 'views/admin-genres.html', show: showAdminGenresPage, roles: ['admin'] },
        '/admin/users': { view: 'views/admin-users.html', show: showAdminUsersPage, roles: ['admin'] },
        '/rentals': { view: 'views/rentals.html', show: showRentalsPage, roles: ['admin', 'staff'] }
    },

    start: function () {
        var self = this;
        window.addEventListener('hashchange', function () {
            self.go();
        });
        if (!window.location.hash) {
            window.location.hash = Store.currentUser() ? '#/catalog' : '#/login';
            return;
        }
        this.go();
    },

    go: function () {
        var path = window.location.hash.replace('#', '') || '/login';
        var bookMatch = path.match(/^\/books\/(\d+)$/);
        var route;
        var bookId = null;

        if (bookMatch) {
            bookId = bookMatch[1];
            route = {
                view: 'views/book.html',
                show: function () {
                    showBookPage(bookId);
                },
                roles: ['admin', 'staff', 'patron']
            };
        } else {
            route = this.routes[path];
        }

        var app = document.getElementById('app');
        var user = Store.currentUser();
        updateNav();

        if (!route) {
            app.innerHTML = '<p>Page not found.</p>';
            return;
        }
        if (route.guest && user) {
            window.location.hash = '#/catalog';
            return;
        }
        if (!route.guest && !user) {
            window.location.hash = '#/login';
            return;
        }
        if (route.roles && user && route.roles.indexOf(user.role) === -1) {
            app.innerHTML = '<div class="alert alert-warning">You cannot open this page.</div>';
            return;
        }

        fetch(route.view)
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                app.innerHTML = html;
                route.show();
            })
            .catch(function () {
                app.innerHTML = '<p>Could not load this page.</p>';
            });
    }
};

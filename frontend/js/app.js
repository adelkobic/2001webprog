function updateNav() {
    var user = Store.currentUser();
    var items = document.querySelectorAll('[data-roles]');
    items.forEach(function (item) {
        var roles = item.getAttribute('data-roles').split(' ');
        var show = false;
        if (!user && roles.indexOf('guest') !== -1) {
            show = true;
        }
        if (user && roles.indexOf('user') !== -1) {
            show = true;
        }
        if (user && roles.indexOf(user.role) !== -1) {
            show = true;
        }
        item.classList.toggle('d-none', !show);
    });

    var label = document.getElementById('user-label');
    if (user) {
        label.textContent = user.first_name + ' ' + user.last_name + ' (' + user.role + ')';
    } else {
        label.textContent = '';
    }
}

document.getElementById('logout-btn').addEventListener('click', function () {
    Store.logout();
    if (window.location.hash === '#/login') {
        Router.go();
    } else {
        window.location.hash = '#/login';
    }
});

Store.load().then(function () {
    updateNav();
    Router.start();
}).catch(function () {
    document.getElementById('app').innerHTML = '<p>Could not load the sample data. Open this site from a local server, not as a file on disk.</p>';
});

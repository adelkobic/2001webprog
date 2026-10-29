function showLoginPage() {
    document.getElementById('login-form').addEventListener('submit', function (event) {
        event.preventDefault();
        showAlert('form-success', '');
        var user = Store.login(fieldValue('email'), document.getElementById('password').value);
        if (!user) {
            showAlert('form-error', 'Wrong email or password.');
            return;
        }
        window.location.hash = '#/catalog';
    });
}

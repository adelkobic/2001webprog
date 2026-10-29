function showRegisterPage() {
    document.getElementById('register-form').addEventListener('submit', function (event) {
        event.preventDefault();
        showAlert('form-error', '');
        showAlert('form-success', '');
        var email = fieldValue('email');
        var taken = false;
        Store.users.forEach(function (user) {
            if (user.email === email) {
                taken = true;
            }
        });
        if (taken) {
            showAlert('form-error', 'That email is already used.');
            return;
        }
        Store.users.push({
            id: Store.nextId(Store.users),
            first_name: fieldValue('first_name'),
            last_name: fieldValue('last_name'),
            email: email,
            phone: fieldValue('phone'),
            password: document.getElementById('password').value,
            role: 'patron'
        });
        document.getElementById('register-form').reset();
        showAlert('form-success', 'Account created. You can log in now.');
    });
}

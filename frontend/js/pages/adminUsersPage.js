function showAdminUsersPage() {
    drawUserTable();

    document.getElementById('user-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var id = document.getElementById('user-id').value;
        var data = {
            first_name: fieldValue('first_name'),
            last_name: fieldValue('last_name'),
            email: fieldValue('email'),
            phone: fieldValue('phone'),
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };
        if (!data.first_name || !data.last_name || !data.email || !data.phone || !data.role) {
            showAlert('page-alert', 'Fill in every field.');
            return;
        }
        if (!id && data.password.length < 6) {
            showAlert('page-alert', 'Password must be at least 6 characters.');
            return;
        }
        var taken = false;
        Store.users.forEach(function (user) {
            if (user.email === data.email && String(user.id) !== id) {
                taken = true;
            }
        });
        if (taken) {
            showAlert('page-alert', 'That email is already used.');
            return;
        }
        showAlert('page-alert', '');
        if (id) {
            var user = Store.find(Store.users, id);
            user.first_name = data.first_name;
            user.last_name = data.last_name;
            user.email = data.email;
            user.phone = data.phone;
            user.role = data.role;
            if (data.password) {
                user.password = data.password;
            }
        } else {
            data.id = Store.nextId(Store.users);
            Store.users.push(data);
        }
        resetUserForm();
        drawUserTable();
    });

    document.getElementById('cancel-user').addEventListener('click', resetUserForm);

    document.getElementById('user-table').addEventListener('click', function (event) {
        var editButton = event.target.closest('[data-edit-user]');
        var deleteButton = event.target.closest('[data-delete-user]');
        if (editButton) {
            var user = Store.find(Store.users, editButton.getAttribute('data-edit-user'));
            document.getElementById('user-id').value = user.id;
            document.getElementById('first_name').value = user.first_name;
            document.getElementById('last_name').value = user.last_name;
            document.getElementById('email').value = user.email;
            document.getElementById('phone').value = user.phone;
            document.getElementById('role').value = user.role;
            document.getElementById('password').value = '';
            document.getElementById('save-user').textContent = 'Save changes';
            document.getElementById('cancel-user').classList.remove('d-none');
        }
        if (deleteButton) {
            var userId = deleteButton.getAttribute('data-delete-user');
            if (String(Store.currentUser().id) === userId) {
                showAlert('page-alert', 'You cannot delete your own account.');
                return;
            }
            if (!window.confirm('Delete this user?')) {
                return;
            }
            Store.users = Store.users.filter(function (user) {
                return String(user.id) !== userId;
            });
            resetUserForm();
            drawUserTable();
        }
    });
}

function drawUserTable() {
    var html = '<table class="table bg-white"><thead><tr>';
    html += '<th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th></th>';
    html += '</tr></thead><tbody>';
    Store.users.forEach(function (user) {
        html += '<tr>';
        html += '<td>' + escapeHtml(user.first_name + ' ' + user.last_name) + '</td>';
        html += '<td>' + escapeHtml(user.email) + '</td>';
        html += '<td>' + escapeHtml(user.phone) + '</td>';
        html += '<td>' + escapeHtml(user.role) + '</td>';
        html += '<td><button class="btn btn-sm btn-outline-primary me-2" type="button" data-edit-user="' + user.id + '">Edit</button>';
        html += '<button class="btn btn-sm btn-outline-danger" type="button" data-delete-user="' + user.id + '">Delete</button></td>';
        html += '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('user-table').innerHTML = html;
}

function resetUserForm() {
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('save-user').textContent = 'Add user';
    document.getElementById('cancel-user').classList.add('d-none');
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function showAlert(id, message) {
    var box = document.getElementById(id);
    if (!box) {
        return;
    }
    if (!message) {
        box.classList.add('d-none');
        box.textContent = '';
        return;
    }
    box.classList.remove('d-none');
    box.textContent = message;
}

function fieldValue(id) {
    return document.getElementById(id).value.trim();
}

function todayText() {
    var today = new Date();
    var month = String(today.getMonth() + 1);
    var day = String(today.getDate());
    if (month.length === 1) {
        month = '0' + month;
    }
    if (day.length === 1) {
        day = '0' + day;
    }
    return today.getFullYear() + '-' + month + '-' + day;
}

function statusBadge(status) {
    var color = 'secondary';
    if (status === 'active') {
        color = 'success';
    } else if (status === 'overdue') {
        color = 'danger';
    }
    return '<span class="badge text-bg-' + color + '">' + escapeHtml(status) + '</span>';
}


function markAll(val) {
    document.querySelectorAll(`input[type=radio][value="${val}"]`).forEach(r => r.checked = true);
}

/* PATS Admin JS */
// Auto-dismiss alerts after 6 seconds
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('.alert.alert-success, .alert.alert-info').forEach(function (el) {
            var alert = bootstrap.Alert.getOrCreateInstance(el);
            if (alert) alert.close();
        });
    }, 6000);

    // Confirm on dangerous actions
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });
});

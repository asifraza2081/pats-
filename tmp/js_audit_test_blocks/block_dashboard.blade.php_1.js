
        // Global Confirmation Helper for high-stakes actions
        window.confirmAction = function(message, type = 'warning') {
            const colors = { 'warning': '#f59e0b', 'danger': '#ef4444', 'primary': '#3b82f6' };
            return confirm(message); // Using native confirm for now, styled ones can be added if requested
        };

        // Auto-initialize tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    
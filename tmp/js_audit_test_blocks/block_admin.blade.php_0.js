
    // Auto-dismiss success alerts after 4s
    setTimeout(() => {
        document.querySelectorAll('.alert-success').forEach(el => {
            new bootstrap.Alert(el).close();
        });
    }, 4000);

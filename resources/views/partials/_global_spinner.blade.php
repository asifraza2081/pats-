<div id="global-submit-spinner" class="d-none">
    <div class="spinner-overlay">
        <div class="spinner-content text-center">
            <div class="spinner-border text-pats-primary mb-3" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
            <h2 class="text-dark fw-black mb-1">Processing Request</h2>
            <p class="text-muted fw-bold">Please wait, do not refresh or close this page.</p>
        </div>
    </div>
</div>

<style>
#global-submit-spinner .spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-in-out;
}

#global-submit-spinner .spinner-content {
    background: white;
    padding: 3rem 4rem;
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(0,0,0,0.05);
    transform: scale(0.95);
    animation: scaleUp 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('submit', function(e) {
        const form = e.target;
        // Don't show spinner for forms with 'no-spinner' class, target='_blank', or GET method search forms
        if (form.classList.contains('no-spinner') || 
            form.getAttribute('target') === '_blank' || 
            (form.method && form.method.toUpperCase() === 'GET')) {
            return;
        }

        // Check if the form is valid (if it uses HTML5 validation)
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return; // Browser will block it anyway, don't show spinner
        }

        const spinner = document.getElementById('global-submit-spinner');
        if (spinner) {
            // Slight delay so very fast submits don't flash it unnecessarily
            setTimeout(() => {
                spinner.classList.remove('d-none');
            }, 100);
        }
    });

    // Handle back/forward cache restoration hiding the spinner
    window.addEventListener('pageshow', function(event) {
        const spinner = document.getElementById('global-submit-spinner');
        if (spinner && !spinner.classList.contains('d-none')) {
            spinner.classList.add('d-none');
        }
    });
});
</script>

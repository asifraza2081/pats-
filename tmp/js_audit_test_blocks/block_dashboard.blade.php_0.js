
        toastr.options = { "positionClass": "toast-bottom-right", "progressBar": true };
        ) toastr.success(""blade_val""); ) toastr.error(""blade_val""); // Auto-initialize all .tom-select inputs globally (if not already handled)
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.tom-select').forEach((el) => {
                if (!el.tomselect) {
                    new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" } });
                }
            });
        });
    
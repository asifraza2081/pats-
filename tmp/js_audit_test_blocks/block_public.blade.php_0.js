
        const navbar = document.getElementById('navbar');
        window.onscroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        const currentTheme = localStorage.getItem('pats-theme') || 'light';
        document.body.setAttribute('data-bs-theme', currentTheme);
    
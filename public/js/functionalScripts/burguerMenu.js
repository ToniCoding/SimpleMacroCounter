const hamburger = document.querySelector('.hamburger-menu');
const navMobile = document.querySelector('.nav-mobile');
const overlay = document.createElement('div');
overlay.className = 'menu-overlay';

document.body.appendChild(overlay);

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMobile.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = navMobile.classList.contains('active') ? 'hidden' : '';
});

overlay.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMobile.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
});

document.querySelectorAll('.nav-mobile a').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMobile.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navMobile.classList.contains('active')) {
        hamburger.classList.remove('active');
        navMobile.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;

    document.querySelectorAll('.has-dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('.nav-category-title');
        const list = dropdown.querySelector('.nav-list');

        if (!trigger || !list) return;

        const links = list.querySelectorAll('a');
        links.forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;
            if (linkPath === currentPath) {
                link.classList.add('selected');
            }
        });

        const toggleMenu = () => {
            const isOpen = dropdown.classList.contains('is-open');

            document.querySelectorAll('.has-dropdown').forEach(item => {
                item.classList.remove('is-open');
                const title = item.querySelector('.nav-category-title');
                if (title) {
                    title.setAttribute('aria-expanded', 'false');
                    title.classList.remove('selected');
                }
            });

            if (!isOpen) {
                const dropdownRect = dropdown.getBoundingClientRect();
                const listWidth = list.scrollWidth || 180;
                const dropdownCenter = dropdownRect.left + (dropdownRect.width / 2);

                if (dropdownCenter + (listWidth / 2) > window.innerWidth - 10) {
                    dropdown.classList.add('submenu-right');
                } else {
                    dropdown.classList.remove('submenu-right');
                }

                dropdown.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                trigger.classList.add('selected');
            }
        };

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            toggleMenu();
        });

        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMenu();
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.has-dropdown')) {
            document.querySelectorAll('.has-dropdown').forEach(item => {
                item.classList.remove('is-open');
                const title = item.querySelector('.nav-category-title');
                if (title) {
                    title.setAttribute('aria-expanded', 'false');
                    title.classList.remove('selected');
                }
            });
        }
    });
});

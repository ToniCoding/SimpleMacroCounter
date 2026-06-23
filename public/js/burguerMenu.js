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

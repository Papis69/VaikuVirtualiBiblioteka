/* Vaikų Virtuali Biblioteka - JavaScript */

document.addEventListener('DOMContentLoaded', function () {

    // Auto-hide flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Add animation to cards on scroll
    const cards = document.querySelectorAll('.card, .feature-item, .admin-nav-card');
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(function (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Active nav link highlight
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(function (link) {
        if (link.getAttribute('href') === currentPath) {
            link.style.background = 'rgba(255,255,255,0.25)';
        }
    });
});

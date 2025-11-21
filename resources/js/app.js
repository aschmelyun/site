// Animate receipt on page load
window.addEventListener('DOMContentLoaded', () => {
    
    // Email obfuscation - reveal on click
    const emailLink = document.getElementById('email-link');
    if (emailLink) {
        emailLink.addEventListener('click', (e) => {
            e.preventDefault();
            const user = 'me';
            const domain = 'aschmelyun.com';
            const email = user + '@' + domain;
            emailLink.href = 'mailto:' + email;
            emailLink.textContent = email;
        });
    }
});

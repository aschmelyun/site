// Animate receipt on page load
window.addEventListener('DOMContentLoaded', () => {
    const receipt = document.getElementById('receipt');

    if (receipt) {
        setTimeout(() => {
            receipt.style.transition = 'bottom 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)';
            receipt.style.bottom = '0';
        }, 500);
    }
});

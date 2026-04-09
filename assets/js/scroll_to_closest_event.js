document.addEventListener('DOMContentLoaded', () => {
    const closestEvent = document.getElementById('closest-event');
    if (closestEvent) {
        closestEvent.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

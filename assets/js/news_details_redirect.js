const eventDivs = document.querySelectorAll('.event');

eventDivs.forEach(div => {
    div.addEventListener('click', (event) => {
        const eventId = div.getAttribute('event-id');
        if (eventId) {
            const url = `/?page=news_details&id=${eventId}`;

            if (event.ctrlKey || event.metaKey || event.button === 1) {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }
    });

    div.addEventListener('mousedown', (event) => {
        if (event.button === 1) {
            event.preventDefault();
        }
    });
});
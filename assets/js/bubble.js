// Constants
const BUBBLE_COUNT = 20;
const body = document.querySelector('body');

// Create bubbles
for (let i = 0; i < BUBBLE_COUNT; i++) {
    
    // Create bubble
    const bubble = document.createElement('div');
    bubble.classList.add('bubble');
    if (randomIntFromRange(0, 1) === 0) {
        bubble.classList.add('blue-bubble');
    } else {
        bubble.classList.add('red-bubble');
    }
    body.appendChild(bubble);

    // Set bubble position
    bubble.style.left = `${randomIntFromRange(0, 100)}vw`;
    bubble.style.top = `${randomIntFromRange(0, body.clientHeight - 100)}px`;

}

// Generate random number
function randomIntFromRange(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}
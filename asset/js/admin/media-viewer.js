document.addEventListener('DOMContentLoaded', e => {

const mediaSelect = document.getElementById('media-select');
const panzoomContainer = document.getElementById('panzoom-container');
const panzoomElem = document.getElementById('panzoom');
const panzoomImg = document.getElementById('panzoom-img');
const zoomInButton = document.getElementById('panzoom-zoom-in');
const zoomOutButton = document.getElementById('panzoom-zoom-out');
const rotateLeftButton = document.getElementById('panzoom-rotate-left');
const rotateRightButton = document.getElementById('panzoom-rotate-right');
const resetButton = document.getElementById('panzoom-reset');

let panzoom;
let rotateDeg = 0;

initMediaViewer();

// Handle media change.
mediaSelect.addEventListener('change', e => {
    panzoomImg.src = e.target.value;
    resetPanzoom();
});
// Handle the scroll wheel.
panzoomContainer.addEventListener('wheel', panzoom.zoomWithWheel);
// Handle the zoom in button.
zoomInButton.addEventListener('click', panzoom.zoomIn);
// Handle the zoom out button.
zoomOutButton.addEventListener('click', panzoom.zoomOut);
// Handle the reset button.
resetButton.addEventListener('click', resetPanzoom);
// Handle the rotate left button.
rotateLeftButton.addEventListener('click', e => {
    rotateDeg = rotateDeg - 90;
    panzoomImg.style.transition = 'transform 0.25s';
    panzoomImg.style.transform = `rotate(${rotateDeg}deg)`;
});
// Handle the rotate right button.
rotateRightButton.addEventListener('click', e => {
    rotateDeg = rotateDeg + 90;
    panzoomImg.style.transition = 'transform 0.25s';
    panzoomImg.style.transform = `rotate(${rotateDeg}deg)`;
});


// Initialize the media viewer.
function initMediaViewer() {
    panzoom = Panzoom(panzoomElem, {});
}
// Reset panzoom.
function resetPanzoom() {
    panzoom.reset();
    rotateDeg = 0;
    // Must set transition to none to prevent the image from unwinding when
    // rotating back to 0deg.
    panzoomImg.style.transition = 'none';
    panzoomImg.style.transform = 'none';
}

});

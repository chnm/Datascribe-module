document.addEventListener('DOMContentLoaded', e => {

// localStorage.clear();

const mediaSelect            = document.getElementById('media-select');
const mediaPageInput         = document.getElementById('media-page');
const previousButton         = document.getElementById('media-previous');
const nextButton             = document.getElementById('media-next');
const panzoomButtons         = document.getElementById('panzoom-buttons');
const panzoomContainer       = document.getElementById('panzoom-container');
const iiifContainer          = document.getElementById('iiif-container');
const panzoomElem            = document.getElementById('panzoom');
const panzoomImg             = document.getElementById('panzoom-img');
const zoomInButton           = document.getElementById('panzoom-zoom-in');
const zoomOutButton          = document.getElementById('panzoom-zoom-out');
const rotateLeftButton       = document.getElementById('panzoom-rotate-left');
const rotateRightButton      = document.getElementById('panzoom-rotate-right');
const resetButton            = document.getElementById('panzoom-reset');
const fullscreenButton       = document.getElementById('fullscreen');
const deleteButton           = document.getElementById('delete-button');
const horizontalLayoutButton = document.getElementById('horizontal-layout');
const verticalLayoutButton   = document.getElementById('vertical-layout');

let panzoom;
let iiifViewer;
let state;
let rotateDeg;

initMediaViewer();

// Handle media change.
mediaSelect.addEventListener('change', e => {
    gotoPage(mediaSelect.selectedIndex + 1);
});
// Handle page input enter key.
mediaPageInput.addEventListener('keydown', e => {
    if (13 === e.keyCode) {
        gotoPage(mediaPageInput.value);
    }
});
// Handle page input change.
mediaPageInput.addEventListener('change', e => {
    gotoPage(mediaPageInput.value);
});
// Handle the previous button.
previousButton.addEventListener('click', e => {
    gotoPage(mediaSelect.selectedIndex);
});
// Handle the next button.
nextButton.addEventListener('click', e => {
    gotoPage(mediaSelect.selectedIndex + 2);
});
// Handle panzoom click to focus.
panzoomContainer.addEventListener('click', e => {
    panzoomContainer.focus();
});
// Handle panning by arrow keys.
panzoomContainer.addEventListener('keydown', e => {
    switch (e.code) {
        case 'ArrowUp':
            panzoom.pan(0, -2, {relative: true});
            break;
        case 'ArrowDown':
            panzoom.pan(0, 2, {relative: true});
            break;
        case 'ArrowLeft':
            panzoom.pan(-2, 0, {relative: true});
            break;
        case 'ArrowRight':
            panzoom.pan(2, 0, {relative: true});
            break;
        default:
            return;
    }
    e.preventDefault();
});
// Handle the scroll wheel.
panzoomContainer.addEventListener('wheel', panzoom.zoomWithWheel);
// Handle the zoom in button.
zoomInButton.addEventListener('click', panzoom.zoomIn);
// Handle the zoom out button.
zoomOutButton.addEventListener('click', panzoom.zoomOut);
// Handle the reset button.
resetButton.addEventListener('click', e => {
    panzoom.reset();
    resetRotate();
    // Delete the current image's state.
    delete state.viewer[mediaSelect.value];
    delete state.rotate[mediaSelect.value];
    saveState();
});
// Handle the rotate left button.
rotateLeftButton.addEventListener('click', e => {
    rotateDeg = rotateDeg - 90;
    panzoomImg.style.transition = 'transform 0.25s';
    panzoomImg.style.transform = `rotate(${rotateDeg}deg)`;
    state.rotate[mediaSelect.value] = rotateDeg;
    saveState();
});
// Handle the rotate right button.
rotateRightButton.addEventListener('click', e => {
    rotateDeg = rotateDeg + 90;
    panzoomImg.style.transition = 'transform 0.25s';
    panzoomImg.style.transform = `rotate(${rotateDeg}deg)`;
    state.rotate[mediaSelect.value] = rotateDeg;
    saveState();
});
// Handle the fullscreen (focus) button.
fullscreenButton.addEventListener('click', e => {
    const body = document.querySelector('body');
    if (body.classList.contains('fullscreen')) {
        disableFullscreen();
    } else {
        enableFullscreen();
    }
    state.fullscreen[mediaSelect.value] = body.classList.contains('fullscreen');
    saveState();
});
// Handle the horizontal layout button.
horizontalLayoutButton.addEventListener('click', e => {
    enableHorizontalLayout();
    state.layout[mediaSelect.value] = 'horizontal';
    saveState();
});
// Handle the vertical layout button.
verticalLayoutButton.addEventListener('click', e => {
    enableVerticalLayout();
    state.layout[mediaSelect.value] = 'vertical';
    saveState();
});
// Set panzoom state on change.
panzoomElem.addEventListener('panzoomchange', (event) => {
    if ('iiif' === getSelectedMedia().dataset.mediaRenderer) {
        return;
    }
    state.viewer[mediaSelect.value] = event.detail;
    saveState();
});
// Set the state in local storage on form submit.
document.getElementById('record-form').addEventListener('submit', e => {
    saveState();
});

// Initialize the media viewer.
function initMediaViewer() {
    rotateDeg = 0
    // Initialize the Panzoom image viewer.
    panzoom = Panzoom(panzoomElem, {});
    // Initialize the IIIF OpenSeadragon image viewer.
    iiifViewer = OpenSeadragon({
        id: 'iiif-container',
        prefixUrl: iiifContainer.dataset.prefixUrl,
    });
    iiifViewer.addHandler('open', function() {
        if (!state.viewer[mediaSelect.value]) {
            return;
        }
        const bounds = new OpenSeadragon.Rect(
            state.viewer[mediaSelect.value].x,
            state.viewer[mediaSelect.value].y,
            state.viewer[mediaSelect.value].width,
            state.viewer[mediaSelect.value].height,
            state.viewer[mediaSelect.value].degrees,
        );
        iiifViewer.viewport.fitBounds(bounds, true);
    });
    iiifViewer.addHandler('viewport-change', function(e) {
        state.viewer[mediaSelect.value] = iiifViewer.viewport.getBounds();
        saveState();
    });

    if (1 < mediaSelect.options.length) {
        // There is more than one page.
        mediaPageInput.disabled = false;
        nextButton.disabled = false;
    }
    // Get the state from local storage and go to the saved page, if any.
    state = JSON.parse(localStorage.getItem('datascribe_media_viewer_state'));
    if (null === state) {
        state = {
            id: null,
            viewer: {},
            rotate: {},
            fullscreen: {},
            layout: {}
        };
        saveState();
    }
    if (state.id) {
        let option = mediaSelect.querySelector(`[value="${state.id}"]`);
        if (option) {
            option.selected = true;
        } else {
            state.id = null;
            saveState();
        }
    }
    gotoPage(mediaSelect.selectedIndex + 1)
}
// Go to a page.
function gotoPage(page) {
    if (mediaSelect.options.length < page || 1 > page) {
        // The page is invalid. Reset the pagination.
        page = 1;
    }
    if (1 === page) {
        previousButton.disabled = true;
    }
    if (1 < page) {
        previousButton.disabled = false;
    }
    if (mediaSelect.options.length === page) {
        nextButton.disabled = true;
    }
    if (mediaSelect.options.length > page) {
        nextButton.disabled = false;
    }
    mediaSelect.selectedIndex = page - 1;
    mediaPageInput.value = page;
    state.id = getSelectedMedia().value;
    saveState();
    applyState();
}
// Reset rotation.
function resetRotate() {
    rotateDeg = 0;
    panzoomImg.style.transition = 'none';
    panzoomImg.style.transform = 'none';
}
// Enable fullscreen.
function enableFullscreen() {
    document.querySelector('body').classList.add('fullscreen');
    document.querySelector('.sidebar').style.display = 'none';
    fullscreenButton.textContent = Omeka.jsTranslate('Disable focus mode');
    if (deleteButton) {
        deleteButton.style.display = 'none';
    }
}
// Disable fullscreen.
function disableFullscreen() {
    document.querySelector('body').classList.remove('fullscreen');
    document.querySelector('.sidebar').style.display = '';
    fullscreenButton.textContent = Omeka.jsTranslate('Enable focus mode');
    if (deleteButton) {
        deleteButton.style.display = '';
    }
}
// Enable horizontal layout.
function enableHorizontalLayout() {
    const currentRow = document.querySelector('.current-row');
    verticalLayoutButton.classList.remove('active');
    verticalLayoutButton.disabled = false;
    horizontalLayoutButton.classList.add('active');
    horizontalLayoutButton.disabled = true;
    currentRow.classList.add('horizontal');
    currentRow.classList.remove('vertical');
}
// Enable vertical layout.
function enableVerticalLayout() {
    const currentRow = document.querySelector('.current-row');
    verticalLayoutButton.classList.add('active');
    verticalLayoutButton.disabled = true;
    horizontalLayoutButton.classList.remove('active');
    horizontalLayoutButton.disabled = false;
    currentRow.classList.remove('horizontal');
    currentRow.classList.add('vertical');
}
// Apply viewer state for the current media selection.
function applyState() {
    let viewerState = state.viewer[mediaSelect.value];
    let rotateState = state.rotate[mediaSelect.value];
    let fullscreenState = state.fullscreen[mediaSelect.value];
    let layoutState = state.layout[mediaSelect.value];

    const selectedMedia = getSelectedMedia();
    if ('iiif' === selectedMedia.dataset.mediaRenderer) {
        // Apply state to the IIIF viewer
        iiifContainer.style.display = 'block';
        panzoomButtons.style.display = 'none';
        panzoomContainer.style.display = 'none';
        iiifViewer.open(JSON.parse(selectedMedia.dataset.mediaData));
    } else {
        iiifContainer.style.display = 'none';
        panzoomButtons.style.display = 'block';
        panzoomContainer.style.display = 'block';
        // Apply state to the image viewer.
        panzoomImg.src = selectedMedia.dataset.mediaSrc;
        if (viewerState) {
            panzoom.zoom(viewerState.scale);
            // Must use setTimeout() due to async nature of Panzoom.
            // @see https://github.com/timmywil/panzoom#a-note-on-the-async-nature-of-panzoom
            setTimeout(() => panzoom.pan(viewerState.x, viewerState.y))
        } else {
            panzoom.reset();
        }
        if (rotateState) {
            rotateDeg = rotateState;
            // Must set transition to none to prevent the image from unwinding when
            // rotating back to 0deg.
            panzoomImg.style.transition = 'none';
            panzoomImg.style.transform = `rotate(${rotateState}deg)`;
        } else {
            resetRotate();
        }
    }
    if (fullscreenState) {
        enableFullscreen();
    } else {
        disableFullscreen();
    }
    if ('vertical' === layoutState) {
        enableVerticalLayout();
    } else {
        enableHorizontalLayout();
    }
}
// Get the selected media option.
function getSelectedMedia() {
    return mediaSelect.options[mediaSelect.selectedIndex];
}
// Save the state to local storage.
function saveState() {
    localStorage.setItem('datascribe_media_viewer_state', JSON.stringify(state));
}

});

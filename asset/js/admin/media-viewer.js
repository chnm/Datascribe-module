$(document).ready(function() {

const mediaViewerDiv = $('.media-viewer');
const mediaRenderDiv = $('.media-render');
const mediaRenderImage = $('.media-render img');
const mediaSelect = $('.media-select select');
const mediaSelectNumber = $('.media-select input[type="number"]');
const mediaPrevButton = $('.media-select .previous.button');
const mediaNextButton = $('.media-select .next.button');

/**
 * Initialize the media viewer.
 */
function initMediaViewer() {
    applyPanzoom(mediaRenderDiv);
    const mediaViewerState = JSON.parse(localStorage.getItem('datascribe_media_viewer'));
    if (null === mediaViewerState) {
        // The media viewer has no saved state.
        return;
    }
    const mediaIds = mediaViewerDiv.data('mediaIds');
    if (!mediaIds.includes(mediaViewerState.mediaId)) {
        // The saved media does not belong to this item.
        return;
    }
    // Reestablish the media viewer's saved state.
    mediaViewerDiv.attr('data-media-id-selected', mediaViewerState.mediaId);
    if (mediaSelect.length) {
        mediaSelect.children(`option[data-media-id="${mediaViewerState.mediaId}"]`).prop('selected', true);
        mediaSelect.trigger('change');
    }
    mediaRenderDiv.css('transform', mediaViewerState.transform);
    mediaRenderImage.css('transform', mediaViewerState.imgTransform);
    if (true === mediaViewerState.fullScreen) {
        $('.full-screen').trigger('click');
    }
    if ('vertical' === mediaViewerState.layout) {
        $('.layout button[name="vertical"]').trigger('click');
    }
}
/**
 * Apply pan/zoom functionality to media viewer.
 */
function applyPanzoom(element) {
    const container = element.parent();
    if (!container.hasClass('image')) {
        return;
    }
    $panzoom = element.panzoom({
        $zoomIn: container.find(".zoom-in"),
        $zoomOut: container.find(".zoom-out"),
        $reset: container.find(".reset"),
        maxScale: 20
    });
    container.on('mousewheel.focal', function(e) {
        e.preventDefault();
        const delta = e.delta || e.originalEvent.wheelDelta;
        const zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
        $panzoom.panzoom('zoom', zoomOut, {
            increment: 0.1,
            animate: false,
        focal: e
        });
    });
}
/**
 * Set image rotation.
 */
function setRotation(obj, direction) {
    let angle = 0;
    const matrix = obj.css("-webkit-transform")
        || obj.css("-moz-transform")
        || obj.css("-ms-transform")
        || obj.css("-o-transform")
        || obj.css("transform");
    if (matrix !== 'none') {
        const values = matrix.split('(')[1].split(')')[0].split(',');
        const a = values[0];
        const b = values[1];
        angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    }
    const currentRotation = (angle < 0) ? angle + 360 : angle;
    const newRotation = (direction == 'left') ? currentRotation - 90 : currentRotation + 90;
    obj.css('transform', 'rotate(' + newRotation + 'deg)');
}
/**
 * Replace the image.
 */
function replaceImage(mediaUrl, mediaText, mediaIndex, mediaSelectorType) {
    mediaRenderImage.attr('src', mediaUrl);
    mediaRenderImage.attr('title', mediaText);
    $('.reset').trigger('click');
    if (mediaSelectorType !== 'select') {
        mediaSelect.val(mediaUrl);
    }
    if ((mediaSelectorType !== 'number') && (mediaSelectNumber.val() !== mediaIndex)) {
        mediaSelectNumber.val(mediaIndex);
    }
    if (mediaSelectNumber.val() == 1) {
        mediaPrevButton.addClass('inactive').attr('disabled', true);
    } else {
        mediaPrevButton.removeClass('inactive').attr('disabled', false);
    }
    if (mediaSelectNumber.val() == mediaSelect.children('option').length) {
        mediaNextButton.addClass('inactive').attr('disabled', true);
    } else {
        mediaNextButton.removeClass('inactive').attr('disabled', false);
    }
    mediaViewerDiv.attr('data-media-id-selected', mediaSelect.find(':selected').data('mediaId'));
}
/**
 * Activate media pagination number.
 */
function activateMediaPaginationNumber(mediaNumber) {
    const mediaIndex = mediaNumber.val();
    const mediaOption = $('.media-select select option:nth-child(' + mediaIndex + ')');
    replaceImage(mediaOption.val(), mediaOption.text(), 'number');
}

// Handle form submission.
$('#record-form').on('submit', function(e) {
    const localStorageItems = {
        mediaId:      mediaViewerDiv.data('mediaIdSelected'),
        transform:    mediaRenderDiv.css('transform'),
        imgTransform: mediaRenderImage.css('transform'),
        fullScreen:   $('body').hasClass('fullscreen'),
        layout:       $('.layout button.active').attr('name'),
    };
    localStorage.setItem('datascribe_media_viewer', JSON.stringify(localStorageItems));
});
// Handle the left image rotation control.
$('.panzoom-container').on('click', '.rotate-left', function(e) {
    e.preventDefault();
    const panzoomImg = $(this).parents('.panzoom-container').find('img');
    setRotation(panzoomImg, 'left');
});
// Handle the right image rotation control.
$('.panzoom-container').on('click', '.rotate-right', function(e) {
    e.preventDefault();
    const panzoomImg = $(this).parents('.panzoom-container').find('img');
    setRotation(panzoomImg, 'right');
});
// Handle the reset image control.
$('.panzoom-container').on('click', '.reset', function(e) {
    e.preventDefault();
    const panzoomImg = $(this).parents('.panzoom-container').find('img');
    panzoomImg.css('transform', 'none');
});
// Handle the layout controls.
$('.layout button').on('click', function(e) {
    const currentFocus = $(':focus');
    $('.layout button').toggleClass('active');
    $('.current-row').toggleClass('horizontal').toggleClass('vertical');
    $('.layout button:disabled').removeAttr('disabled');
    $('.layout button.active').attr('disabled', true);
    currentFocus.focus();
});
// Handle the full-screen control.
$('.full-screen').on('click', function(e) {
    $('body').toggleClass('fullscreen');
    $('.sidebar').toggle();
});
// Handle the media selection control.
mediaSelect.on('change', function() {
    const mediaUrl = $(this).val();
    const mediaText = $(this).text();
    const mediaIndex = $(this).find(':selected').index() + 1;
    replaceImage(mediaUrl, mediaText, mediaIndex, 'select');
});
// Handle the media number selection control.
mediaSelectNumber.on('keypress', function(e) {
    if (e.keyCode == 13) {
        activateMediaPaginationNumber($(this));
    }
});
// Handle the media number selection control.
mediaSelectNumber.on('change', function() {
    activateMediaPaginationNumber($(this));
});
// Handle the previous media selection control.
mediaPrevButton.click(function() {
    const oldMediaIndex = $('.media-select option:selected').index();
    const newMediaOption = $('.media-select select option:nth-child(' + oldMediaIndex + ')');
    if (mediaSelectNumber.val() !== oldMediaIndex) {
        mediaSelectNumber.val(oldMediaIndex);
    }
    replaceImage(newMediaOption.val(), newMediaOption.text(), oldMediaIndex, 'button');
});
// Handle the next media selection control.
mediaNextButton.click(function() {
    const oldMediaIndex = $('.media-select option:selected').index();
    const newMediaIndex = oldMediaIndex + 2;
    const newMediaOption = $('.media-select select option:nth-child(' + newMediaIndex + ')');
    replaceImage(newMediaOption.val(), newMediaOption.text(), newMediaIndex, 'button');
});

initMediaViewer();

});

$(document).ready(function() {

$('.set-null-checkbox input[type="checkbox"]').on('change', function(e) {
    var thisCheckbox = $(this);
    var setNullCheckbox = thisCheckbox.closest('fieldset').toggleClass('set-to-null');
});

$('.previous-next-toggle').click(function() {
    var container = $(this).parents('.tablesaw-swipe-wrapper');
    container.toggleClass('collapsed');
});

// Handle record position change control.
var directionSelect = $('#position_change_direction');
var recordIdSelect = $('#position_change_record_id');
recordIdSelect.hide();
directionSelect.on('change', function(e) {
    if ('' === directionSelect.val()) {
        recordIdSelect.hide();
    } else {
        recordIdSelect.show();
    }
});

});

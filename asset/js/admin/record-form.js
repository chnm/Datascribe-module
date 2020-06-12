$(document).ready(function() {

$('.set-null-checkbox input[type="checkbox"]').on('change', function(e) {
    var thisCheckbox = $(this);
    var setNullCheckbox = thisCheckbox.closest('fieldset').toggleClass('set-to-null');
});

$('.previous-next-toggle').click(function() {
    var container = $(this).parents('.tablesaw-swipe-wrapper');
    container.toggleClass('collapsed');  
});

});

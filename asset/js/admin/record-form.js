$(document).ready(function() {

$('.set-null-checkbox input[type="checkbox"]').on('change', function(e) {
    var thisCheckbox = $(this);
    var setNullCheckbox = thisCheckbox.closest('fieldset').toggleClass('set-to-null');
});

});

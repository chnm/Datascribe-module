$(document).ready(function() {

$('.set-null-button').on('click', function(e) {
    var thisButton = $(this);
    var setNullCheckbox = thisButton.closest('fieldset').find('.common-elements .set-null-input');
    var setNullValue = ('0' === setNullCheckbox.val()) ? '1' : '0';
    setNullCheckbox.val(setNullValue);
});

});

$(document).ready(function() {

// Iterate all DataScribe checkboxes and initialize them.
$('.datascribe-checkbox').each(function(i) {
    let thisContainer = $(this);

    let valueCheckbox = thisContainer.children('.datascribe-checkbox-value');
    let nullCheckbox = thisContainer.closest('fieldset').find('input.datascribe-checkbox-null');

    // Uncheck the null checkbox when the user changes the value checkbox.
    valueCheckbox.on('change', function(e) {
        nullCheckbox.prop('checked', false);
    });

    // Uncheck the value checkbox when the user checks the null checkbox.
    nullCheckbox.on('change', function(e) {
        if (this.checked) {
            valueCheckbox.prop('checked', false);
        }
    });
});

});

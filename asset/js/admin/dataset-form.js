$(document).ready(function() {

CKEDITOR.inline(document.getElementById('o-module-datascribe-guidelines'));

var fields = $('#form-builder-fields');
var addFieldControls = function(field) {
    field.prepend(fields.data('field-actions'));
}

// Add field controls to existing fields.
fields.children('fieldset').each(function(i) {
    var field = $(this);
    addFieldControls(field);
});

// Hande the add field control.
$('#data-types').on('click', '.option', function(e) {
    e.preventDefault();
    var name = $(this).data('name');
    var template = $(`span.data-type-template[data-name="${name}"]`).data('template');
    var uniqueKey = Math.random().toString(36).substr(2);
    template = template.replace(/__INDEX__/g, uniqueKey);
    var field = $($.parseHTML(template));
    addFieldControls(field);
    fields.append(field);
    field[0].scrollIntoView();
});
// Handle the field is primary controls.
fields.on('change', 'input[name$="[o-module-datascribe:is_primary]"]', function(e) {
    var thisCheckbox = $(this);
    var wasUnchecked = thisCheckbox.prop('checked');
    fields.find('input[name$="[o-module-datascribe:is_primary]"]').prop('checked', false);
    if (wasUnchecked) {
        thisCheckbox.prop('checked', true);
    }
});
// Handle the field disable controls.
fields.on('click', 'button.field-disable', function(e) {
    var field = $(this).closest('fieldset');
    field.find(':input').prop('disabled', true);
    field.find('button.field-disable, button.field-enable').toggleClass('active');
    field.find('button.field-enable').prop('disabled', false);
});
// Handle the field enable controls.
fields.on('click', 'button.field-enable', function(e) {
    var field = $(this).closest('fieldset');
    field.find(':input').prop('disabled', false);
    field.find('button.field-disable, button.field-enable').toggleClass('active');
});
// Handle the field position decrement controls.
fields.on('click', 'button.field-position-decrement', function(e) {
    var field = $(this).closest('fieldset');
    field.prev().insertAfter(field);
});
// Handle the field position increment controls.
fields.on('click', 'button.field-position-increment', function(e) {
    var field = $(this).closest('fieldset');
    field.next().insertBefore(field);
});

});

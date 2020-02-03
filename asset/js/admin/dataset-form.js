$(document).ready(function() {

CKEDITOR.inline(document.getElementById('o-module-datascribe-guidelines'));

var fieldsData = $('#form-builder-fields');
var fieldsContainer = $('#form-builder-fields > fieldset');
var newFieldIndex = 1;

var addFieldControls = function(field) {
    field.prepend(fieldsData.data('field-actions'));
}

// Prepare fields on initial load.
$('#form-builder-fields > fieldset > fieldset').each(function(i) {
    var field = $(this);
    addFieldControls(field); // add field controls
    field.toggleClass('closed'); // fields are closed by default
});

// Hande the add field control.
$('#data-types').on('click', '.option', function(e) {
    e.preventDefault();
    var name = $(this).data('name');
    var template = $(`span.data-type-template[data-name="${name}"]`).data('template');
    var uniqueKey = Math.random().toString(36).substr(2);
    template = template.replace(/__INDEX__/g, uniqueKey);
    var field = $($.parseHTML(template));
    field.addClass('open');
    var fieldLabelSpan = field.find('legend span.field-name');
    fieldLabelSpan.text(fieldLabelSpan.text() + ` - ${newFieldIndex++}`);
    addFieldControls(field);
    fieldsContainer.append(field);
    field[0].scrollIntoView();
});
// Handle the field is primary controls.
fieldsContainer.on('change', 'input[name$="[is_primary]"]', function(e) {
    var thisCheckbox = $(this);
    var wasUnchecked = thisCheckbox.prop('checked');
    fieldsContainer.find('input[name$="[is_primary]"]').prop('checked', false);
    if (wasUnchecked) {
        thisCheckbox.prop('checked', true);
    }
});
// Handle the field disable controls.
fieldsContainer.on('click', 'button.field-disable', function(e) {
    var field = $(this).closest('fieldset');
    field.find(':input').prop('disabled', true);
    field.find('button.field-disable, button.field-enable').toggleClass('active');
    field.find('button.field-enable').prop('disabled', false);
    field.toggleClass('deleted');
    if (!field.hasClass('closed')) {
      field.addClass('closed').removeClass('open');
      field.find('button.field-collapse, button.field-expand').toggleClass('active');
    }
});
// Handle the field enable controls.
fieldsContainer.on('click', 'button.field-enable', function(e) {
    var field = $(this).closest('fieldset');
    field.find(':input').prop('disabled', false);
    field.find('button.field-disable, button.field-enable, button.field-collapse, button.field-expand').toggleClass('active');
    field.toggleClass('deleted');
    field.removeClass('closed').addClass('open');
});
// Handle the field position decrement controls.
fieldsContainer.on('click', 'button.field-position-decrement', function(e) {
    var field = $(this).closest('fieldset');
    field.prev().insertAfter(field);
});
// Handle the field position increment controls.
fieldsContainer.on('click', 'button.field-position-increment', function(e) {
    var field = $(this).closest('fieldset');
    field.next().insertBefore(field);
});
// Handle the field options expand/collapse controls.
fieldsContainer.on('click', 'button.field-collapse, button.field-expand', function() {
    var field = $(this).closest('fieldset');
    field.find('.field-collapse, .field-expand').toggleClass('active');
    field.toggleClass('closed open');
});
});

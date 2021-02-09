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
    field.find('legend').first().append(fieldsData.data('field-flags'));
    field.toggleClass('closed'); // fields are closed by default
    if (field.find('[type="checkbox"][name$="[is_required]"]').attr('checked') == 'checked') {
      field.find('.required-flag').removeClass('inactive');
    }
    if (field.find('[type="checkbox"][name$="[is_primary]"]').attr('checked') == 'checked') {
      field.find('.primary-flag').removeClass('inactive');
    }
});

// Open fields that contain error messages.
$('#form-builder-fields ul.messages').each(function(i) {
    var field = $(this).closest('fieldset.dataset-field');
    field.toggleClass('closed open');
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
    field.find('legend').first().append(fieldsData.data('field-flags'));
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
// Handle required field toggle
fieldsContainer.on('change', '[type="checkbox"][name$="[is_required]"]', function() {
    var field = $(this).closest('fieldset');
    field.find('.required-flag').toggleClass('inactive');
});
// Handle required field toggle
fieldsContainer.on('change', '[type="checkbox"][name$="[is_primary]"]', function() {
    var field = $(this).closest('fieldset');
    var fieldPrimaryFlag = field.find('.primary-flag');
    if (fieldPrimaryFlag.hasClass('inactive')) {
        $('.primary-flag').addClass('inactive');
        fieldPrimaryFlag.removeClass('inactive');      
    } else {
        fieldPrimaryFlag.addClass('inactive');
    }
});

new Sortable($('.dataset-fields')[0], {
  handle: '.sortable-handle',
  animation: 150,
  forceFallback: true
});

// Send form data via one variable to avoid reaching PHP's max_input_vars limit.
const datasetForm = document.getElementById('dataset-form');
datasetForm.addEventListener('submit', e => {
    e.preventDefault();
    const form = document.createElement('form');
    const input = document.createElement('input');
    const formData = Object.fromEntries(new FormData(datasetForm));
    formData[e.submitter.name] = true;
    form.method = 'post';
    input.name = 'data';
    input.value = JSON.stringify(formData);
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});

});

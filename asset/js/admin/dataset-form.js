$(document).ready(function() {

CKEDITOR.inline(document.getElementById('o-module-datascribe-guidelines'));

// Handle the add new field control.
$('#data-types').on('click', '.option', function(e) {
    e.preventDefault();
    var name = $(this).data('name');
    var template = $(`span.data-type-template[data-name="${name}"]`).data('template');
    var uniqueKey = Math.random().toString(36).substr(2);
    template = template.replace(/__INDEX__/g, uniqueKey);
    $('#form-builder').append(template);
});

// Handle the is_primary control.
$('#form-builder').on('change', 'input[name$="[o-module-datascribe:is_primary]"]', function(e) {
    $('#form-builder').find('input[name$="[o-module-datascribe:is_primary]"]').prop('checked', false);
    $(this).prop('checked', true);
});

});

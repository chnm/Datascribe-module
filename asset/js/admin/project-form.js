$(document).ready(function() {

var template = $($('#user-row-template').data('template'));

// Add a row to the users table.
var addUserRow = function(user) {
    $('#project-users-table').show();
    var userRow = template.clone();
    userRow.find('.user-id').val(user['o:id']);
    userRow.find('.user-name').text(user['o:name']);
    userRow.find('.user-email').text(user['o:email']);
    $('#project-users-table tbody').append(userRow);
};

// Add existing users to the users table.
$.each($('#users').data('users'), function(index, user) {
    addUserRow(user);
});

// Add a new user to the users table.
$('#new-users').find('.selector-child').on('click', function(e) {
    var user = $(this).data('user');
    var existingUser = $('#project-users-table').find('input.user-id[value="' + user['o:id'] + '"]');
    if (!existingUser.length) {
        // Do not add existing users.
        addUserRow(user);
    }
});

$('#project-users-table').on('click', '.o-icon-delete, .o-icon-undo', function(e) {
    e.preventDefault();
    var thisIcon = $(this);
    thisIcon.parents('tr').toggleClass('delete');
});
$('#project-users-table').on('click', '.o-icon-delete', function(e) {
    e.preventDefault();
    var thisIcon = $(this);
    var row = thisIcon.parents('tr');
    thisIcon.hide();
    row.find('.o-icon-undo').show();
    row.find('input[type="hidden"]').prop('disabled', true);
});
$('#project-users-table').on('click', '.o-icon-undo', function(e) {
    var thisIcon = $(this);
    var row = thisIcon.parents('tr');
    thisIcon.hide();
    row.find('.o-icon-delete').show();
    row.find('input[type="hidden"]').prop('disabled', false);
});

});

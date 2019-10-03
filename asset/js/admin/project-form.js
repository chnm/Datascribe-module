$(document).ready(function() {

var template = $('#user-row-template').data('template');
var userIndex = 0;

// Add a row to the users table.
var addUserRow = function(user) {
    $('#project-users-table').show();
    var userRow = $(template.replace(/__INDEX__/g, userIndex));
    userIndex++;
    userRow.find('.user-name').text(user['o:user']['o:name']);
    userRow.find('.user-email').text(user['o:user']['o:email']);
    userRow.find('.user-id').val(user['o:user']['o:id']);
    userRow.find('.user-role').val(user['o-module-datascribe:role']);
    $('#project-users-table tbody').append(userRow);
};

// Add existing users to the users table.
$.each($('#users').data('users'), function(index, user) {
    console.log(user);
    addUserRow(user);
});

// Add a new user to the users table.
$('#new-users').find('.selector-child').on('click', function(e) {
    var oUser = $(this).data('user');
    var user = $('#project-users-table').find('input.user-id[value="' + oUser['o:id'] + '"]');
    // Do not add an existing user.
    if (!user.length) {
        var user = {
            'o:user': oUser,
            'o-module-datascribe:role': 'transcriber'
        };
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

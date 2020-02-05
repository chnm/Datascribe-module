$(document).ready(function() {

$('.sidebar.always-open').on('click', '.sidebar-drawer', function() {
    var drawerButton = $(this);
    var sidebar = drawerButton.parents('.sidebar');
    $('#content').toggleClass('expanded');
    sidebar.toggleClass('expanded').toggleClass('collapsed');
    if (drawerButton.hasClass('collapse')) {
        drawerButton.removeClass('collapse').addClass('expand').attr('aria-label', Omeka.jsTranslate('Expand'));
    } else {
        drawerButton.removeClass('expand').addClass('collapse').attr('aria-label', Omeka.jsTranslate('Collapse'));
    }
});

});

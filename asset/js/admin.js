$(document).ready(function() {

// Handle page action menu.
$(document).on('o:expanded', '#page-action-menu a.collapse', function() {
    var button = $(this);
    $(document).on('mouseup.page-actions', function(e) {
        var pageActionMenu = $('#page-action-menu ul');
        if (pageActionMenu.is(e.target)) {
            return;
        }
        if (!button.is(e.target)) {
            button.click();
        }
        $(document).off('mouseup.page-actions');
    });
});

// Close all sidebars before opening another.
$('a.sidebar-content').on('click', function(e) {
    var sidebars = $('.sidebar').each(function() {
        Omeka.closeSidebar($(this));
    });
});

// Handle the sidebar drawers.
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

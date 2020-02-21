var Datascribe = {
    enableTableNavArrows: function() {
        window.addEventListener('keydown', function (event) {
            if (event.defaultPrevented) {
                return;
            }
            switch (event.key) {
                case 'Left':
                case 'ArrowLeft':
                    $('a.btn.left').click();
                    break;
                case 'Right':
                case 'ArrowRight':
                    $('a.btn.right').click();
                    break;
                default:
                    return;
            }
            event.preventDefault();
        }, true);
    }
};

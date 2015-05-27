(function($) {
    function closePopupHelp() {
        $('.popup-help-container').removeClass('active');

        $('#help-modal-background').remove();
        $('#popup-help-message').fadeOut(500, function() {
            $(this).children().remove();
        });
    }

    $('.popup-help-button').click(function() {
        var button = $(this);

        if (button.parent().hasClass('active')) {
            // hide popup and remove active class
            closePopupHelp();
        }
        else {
            // show popup and add active class
            var options = {
                data: { partial: button.parent().data('partial') }
            };
            $.request('PopupHelp::onHelp', options);
        }
        return false;
    });

    $('#popup-help-message').on('ajaxUpdate', function(e) {
        $('body').append('<div id="help-modal-background"></div>');
        $('#help-modal-background').css('position','fixed')
            .hide()
            .css('top',0)
            .css('left', 0)
            .css('bottom', 0)
            .css('right', 0)
            .css('z-index', 599)
            .fadeTo('fast',.5)
            .click(function() {
                closePopupHelp();
            });       
        $(this).fadeIn(100);
        $(this).parent().addClass('active');
    });

})(jQuery);
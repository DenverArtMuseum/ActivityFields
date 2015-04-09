(function($) {
    $(document).ready(function() {
        $('input.timepicker').timeEntry({
            show24Hours: true,
            timeSteps: [1, 15, 0]
        });
    });
})(jQuery);

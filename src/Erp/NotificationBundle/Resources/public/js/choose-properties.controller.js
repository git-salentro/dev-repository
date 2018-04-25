(function ($) {
    var checkboxes = $('form[name="erp_notification_user_notification"] tbody input[name="idx[]"]');
    var allElementsInput = $('form[name="erp_notification_user_notification"] input[name="all_elements"]');

    allElementsInput.click(function (e) {
            checkboxes.prop('checked', $(this).prop('checked'));
    });

    if (checkboxes.filter(':checked').length === checkboxes.length) {
        allElementsInput.prop('checked', 'checked');
    }
})(jQuery);
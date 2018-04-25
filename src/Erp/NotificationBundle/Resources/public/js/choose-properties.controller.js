(function ($) {
    var checkboxes = $('form[name="erp_notification_user_notification"] tbody input[name="idx[]"]');
    var button = $('form[name="erp_notification_user_notification"] #erp_notification_user_notification_submit');

    $('form[name="erp_notification_user_notification"] input[name="all_elements"]').click(function (e) {
            checkboxes.prop('checked', $(this).prop('checked'));
            toggleButtonState();
    });

    function toggleButtonState() {
        if (checkboxes.filter(':checked').length) {
            button.removeAttr('disabled');
        } else {
            button.attr('disabled', 'disabled');
        }
    }

    toggleButtonState();
    checkboxes.click(toggleButtonState);
})(jQuery);
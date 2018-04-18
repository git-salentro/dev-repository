(function ($) {
    $('form[name="erp_notification_user_notification"] input[name="all_elements"]').click(function (e) {
        var checkboxes = $(this)
            .closest('thead')
            .next()
            .find('input[name="idx[]"]')
            .prop('checked', $(this).prop('checked'));
    });
})(jQuery);
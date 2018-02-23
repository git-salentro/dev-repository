var LateRentPaymentController = function () {
    this.allowRentPaymentSelector = '[name="erp_user_user_late_rent_payment[allowRentPayment]"]';
    this.allowRentPaymentForm = '[name="erp_user_user_late_rent_payment"]';
};

LateRentPaymentController.prototype.listenAllowFullAmountPayment = function () {
    var element = $(this.allowRentPaymentSelector);
    var form = $(this.allowRentPaymentForm);
    var url = form.attr('action');

    element.change(function () {
        if (!$(this).is(':checked')) {
            form.append("<input type='hidden' name='" + form.attr('name') + "' />");
        }

        var data = form.serialize();
        form.find('input[type="hidden"][name="' + form.attr('name') + '"]').remove();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {}
        });
    });
};

LateRentPaymentController.prototype.run = function() {
    this.listenAllowFullAmountPayment();
};

$(function() {
    var controller = new LateRentPaymentController();
    controller.run();
});

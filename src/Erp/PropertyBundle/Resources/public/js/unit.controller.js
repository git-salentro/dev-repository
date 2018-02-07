var UnitController =  function () {
    this.totalPriceSelector = $('#total-price');
    this.countSelector = $('#unit-count');
    this.existingUnitCountSelector = $('#existing-unit-count');
};

UnitController.prototype.listenCount = function () {
    var that = this;
    var settings = JSON.parse($('#settings').val());

    that.countSelector.keyup(function () {
        var count = parseInt($(this).val()) + parseInt(that.existingUnitCountSelector.val());
        var amount = 0;
        $.each(settings, function (k, setting) {
            for (var i = setting['min']; i <= count; i++) {
                amount += setting['amount'];

                if (i === setting['max']) {
                    break;
                }

            }
        });

        that.totalPriceSelector.html(amount);
    });
};

UnitController.prototype.run = function() {
    this.listenCount();
};

$(function () {
    var controller = new UnitController();
    controller.run();
});

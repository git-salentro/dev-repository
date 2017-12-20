var UnitController =  function () {
    this.initialUnitPriceSelector = $('#initial-unit-price');
    this.additionalUnitPriceSelector = $('#additional-unit-price');
    this.totalPriceSelector = $('#total-price');
    this.currentYearPriceSelector = $('#current-year-price');
    this.countSelector = $('#unit-count');
    this.totalPrice = parseInt(this.totalPriceSelector.html());
};

UnitController.prototype.listenCount = function () {
    var that = this;
    that.countSelector.keyup(function () {
        var totalPrice = that.totalPrice;
        var additionalUnitPrice = parseInt(that.additionalUnitPriceSelector.html());
        var unitCount = parseInt(that.countSelector.val());

        if (that.currentYearPriceSelector.length) {
            totalPrice += unitCount * additionalUnitPrice;
        } else {
            var initialUnitPrice = parseInt(that.initialUnitPriceSelector.html());
            totalPrice += initialUnitPrice + unitCount * additionalUnitPrice;
        }
        that.totalPriceSelector.html(totalPrice);
    });
};

UnitController.prototype.run = function() {
    this.listenCount();
};

$( function () {
    var controller = new UnitController();
    controller.run();
});

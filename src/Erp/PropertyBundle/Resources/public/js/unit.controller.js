var UnitController =  function () {
    this.initialUnitPriceSelector = $('#initial-unit-price');
    this.additionalUnitPriceSelector = $('#additional-unit-price');
    this.totalPriceSelector = $('#total-price');
    this.currentYearPriceSelector = $('#current-year-price');
    this.countSelector = $('#unit-count');
    this.totalPrice = parseInt(this.totalPriceSelector.html());
};

var PricingStrategy = function (type) {
    this.strategy = this.strategies[type];
};

PricingStrategy.prototype.strategies = {
    firstUnit: function (initialUnitPrice, unitCount, additionalUnitPrice) {
        return initialUnitPrice + (unitCount - 1) * additionalUnitPrice;
    },
    moreThanOne: function (initialUnitPrice, unitCount, additionalUnitPrice) {
        return initialUnitPrice + unitCount * additionalUnitPrice;
    }
};

PricingStrategy.prototype.calculate = function (initialUnitPrice, unitCount, additionalUnitPrice) {
    return this.strategy(initialUnitPrice, unitCount, additionalUnitPrice);
};

UnitController.prototype.listenCount = function () {
    var that = this;
    that.countSelector.keyup(function () {
        var totalPrice = that.totalPrice;
        var additionalUnitPrice = parseInt(that.additionalUnitPriceSelector.html());
        var unitCount = parseInt(that.countSelector.val());
        var strategy = null;

        if (that.currentYearPriceSelector.length) {
            if (unitCount >= 30) {
                //TODO Refactor it
                additionalUnitPrice = 15;
            }
            strategy = new PricingStrategy('moreThanOne');
            totalPrice = strategy.calculate(totalPrice, unitCount, additionalUnitPrice);
        } else {
            var initialUnitPrice = parseInt(that.initialUnitPriceSelector.html());
            if (unitCount >= 30) {
                //TODO Refactor it
                additionalUnitPrice = 15;
            }
            strategy = new PricingStrategy('firstUnit');
            totalPrice = strategy.calculate(initialUnitPrice, unitCount, additionalUnitPrice);

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

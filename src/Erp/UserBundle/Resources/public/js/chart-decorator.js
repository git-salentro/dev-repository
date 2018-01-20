(function () {
    var updateElement = Chart.controllers.bar.prototype.updateElement;
    Chart.controllers.bar.prototype.updateElement = function (rectangle, index, reset) {
        updateElement.call(this, rectangle, index, reset);
        var me = this;
        var chart = me.chart;

        if (chart.data.datasets[me.index].xType !== 'undefined'
            && chart.data.datasets[me.index].xValues !== 'undefined')
        {
            rectangle._xType = chart.data.datasets[me.index].xType;
            rectangle._xValue = chart.data.datasets[me.index].xValues[index];
        }
    };
})();
//TODO Customize tooltips like Xero.com
(function ($) {
    var ctx = $('#cashflows-chart');
    var labels = ctx.data('labels'),
        cashIn = ctx.data('cash-in'),
        cashOut = ctx.data('cash-out');
    var chart = new Chart(ctx[0], {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: cashIn,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1,
                xValues: [1,2,3,4,5,6],
                xType: 'cache-in'
            }, {
                data: cashOut,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                xValues: [1,2,3,4,5,6],
                xType: 'cache-out'
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                xAxes:[{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    display:false
                }]
            }
        }
    });
})(jQuery);

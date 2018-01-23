(function ($) {
    var ctx = $('#transactions-chart'),
        labels = ctx.data('labels'),
        transactions = ctx.data('transactions'),
        listingUrl = ctx.data('listing-url'),
        xValues = ctx.data('intervals');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: transactions,
                backgroundColor:  'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1,
                xValues: xValues
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

    ctx.click(function(e) {
        var elements = chart.getElementAtEvent(e);
        if (!elements.length) {
            return;
        }

        var month = elements[0]._xValue;
        window.open(listingUrl + '?filter[interval]=' + month, '_blank');
    });
})(jQuery);
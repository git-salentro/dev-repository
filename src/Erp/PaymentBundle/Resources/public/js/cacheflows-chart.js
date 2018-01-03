//TODO Customize tooltips like Xero.com
(function ($) {
    var ctx = $('#cashflows-chart');
    var labels = ctx.data('labels'),
        cashIn = ctx.data('cache-in'),
        cashOut = ctx.data('cache-out');
    var chart = new Chart(ctx[0], {
        type: 'bar',
        data: {
            labels: ["July", "August", "September", "October", "November", "December"],
            datasets: [{
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor:  'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1
            }, {
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
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

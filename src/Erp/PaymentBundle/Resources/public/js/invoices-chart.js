(function ($) {
    var ctx = document.getElementById("invoices-chart");
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["July", "August", "September", "October", "November", "December"],
            datasets: [{
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor:  'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
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
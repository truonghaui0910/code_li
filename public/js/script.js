var notifyTitle = "Thông Báo";
var darkMode = localStorage.getItem("luxiDarkMode");
if(darkMode==null){
    darkMode = "true";
}
if (darkMode == "true") {
    $('#wrapper').removeClass('theme--light');
    $('#wrapper').addClass('theme--dark');
} else {
    $('#wrapper').removeClass('theme--dark');
    $('#wrapper').addClass('theme--light');
}
$(".btn-change-theme").click(function () {
    $(".btn-change-theme").toggleClass("theme-dark");
    if ($(".btn-change-theme").hasClass("theme-dark")) {
        //dark
        localStorage.setItem('luxiDarkMode', "true");
        $('#wrapper').removeClass('theme--light');
        $('#wrapper').addClass('theme--dark');
    } else {
        //light
        localStorage.setItem('luxiDarkMode', "false");
        $('#wrapper').removeClass('theme--dark');
        $('#wrapper').addClass('theme--light');
    }
});
drawLineCharts = function (chartId, labelView, datasets) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelView,
            datasets: datasets
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, ',', '.');
                        if (typeof data.datasets[tooltipItem.datasetIndex].wake !== 'undefined') {
                            label += ' ' + data.datasets[tooltipItem.datasetIndex].wake[tooltipItem.index];
                        }
                        return label;
                    }, footer: function (tooltipItem, data) {
                        //hiển thị tooltips xuống dòng dữ liệu kiểu ["string1","string2","string3"]
                        if (typeof data.datasets[tooltipItem[0].datasetIndex].footer !== 'undefined') {
                            return  data.datasets[tooltipItem[0].datasetIndex].footer[tooltipItem[0].index];
                        }
                    }
                },
                footerFontStyle: 'normal',
                footerFontSize: 11

            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: false,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }, gridLines: {
                            display: true
                        }
                    }], xAxes: [{
//                        type: 'time',
                        distribution: 'series',
//                        time: {
//                            unit: 'day',
//                            displayFormats: {
//                                day: 'YYYY/MM/DD'
//                            }
//                        },
                        display: true,
                        gridLines: {
                            display: false
                        }
                    }]
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0
                }
            }
        }
    });
};

drawLineChartMini = function (chartId, descritpion, labels, datas) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: descritpion,
                    data: datas,
                    fill: false,
                    backgroundColor: '#2fa5cb',
                    borderColor: '#2fa5cb',
                    borderWidth: 1
                }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                yAxes: [{
                        ticks: {
                            display: false
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }],
                xAxes: [{
                        barPercentage: 1,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: false //this will remove only the label
                        }
                    }]
            },
            legend: {
                display: false
            },
            tooltips: {
                enabled: false
            },
            bezierCurve: false, //remove curves from your plot
            scaleShowLabels: false, //remove labels
            tooltipEvents: [], //remove trigger from tooltips so they will'nt be show
            pointDot: false, //remove the points markers
            scaleShowGridLines: true //set to false to remove the grids background
        }
    });

    myChart.canvas.parentNode.style.height = '60px';
    myChart.canvas.parentNode.style.width = '170px';
};

function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function getTimestamp(time) {
    if (time === 0) {
        var date = new Date();
    } else {
        var date = new Date(time);
    }
    var year = date.getFullYear();
    var month = ("0" + (date.getMonth() + 1)).substr(-2);
    var day = ("0" + date.getDate()).substr(-2);
    var hour = ("0" + date.getHours()).substr(-2);
    var minutes = ("0" + date.getMinutes()).substr(-2);
    var seconds = ("0" + date.getSeconds()).substr(-2);
    return month + "/" + day + "/" + year;
}

notify = function (message, link, icon) {
    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    } else {
        var notification = new Notification('AUTOLIVE', {
            icon: icon,
            body: message
        });
        notification.onclick = function () {
            window.open(link);
        };
    }
}
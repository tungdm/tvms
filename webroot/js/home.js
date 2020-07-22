var lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    title: {
        display: true,
        text: 'Biểu đồ số lượng Lao động và Đơn hàng trong năm ' + moment().year()
    },
    tooltips: {
        mode: 'index',
        intersect: false,
    },
    hover: {
        mode: 'nearest',
        intersect: true
    },
    scales: {
        xAxes: [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: 'Tháng'
            }
        }],
        yAxes: [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: 'Số lượng'
            }
        }]
    },
    animation: {
        onComplete: function () {
            isChartRendered = true
        }
    }
}
var chartColors = ['rgb(255, 99, 132)', 'rgb(255, 159, 64)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(54, 162, 235)', 'rgb(153, 102, 255)'];
var isChartRendered = false;

$(document).ready(function () {
    renderOrderChart();
    renderPiechart(northPopulation, 'northPopulation', 4, '#north-total');
    renderPiechart(middlePopulation, 'middlePopulation', 4, '#middle-total');
    renderPiechart(southPopulation, 'southPopulation', 4, '#south-total');
});

function showPassedStudent() {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/pages/get-passed-students',
        data: {},
        success: function (resp) {
            if (resp.status == 'success') {
                // fill data
                var source = $("#newly-passed-template").html();
                var template = Handlebars.compile(source);
                var html = template(resp.data);
                $('#newly-passed-container').html(html);
                // show modal
                $('#newly-passed-modal').modal('toggle');
            } else {
                var notice = new PNotify({
                    title: '<strong>' + resp.flash.title + '</strong>',
                    text: resp.flash.message,
                    type: resp.flash.type,
                    styling: 'bootstrap3',
                    icon: resp.flash.icon,
                    cornerclass: 'ui-pnotify-sharp',
                    buttons: {
                        closer: false,
                        sticker: false
                    }
                });
                notice.get().click(function () {
                    notice.remove();
                });
            }
        },
        complete: function () {
            ajaxing = false;
        }
    });
}

function renderOrderChart() {
    var labels = [];
    var studentData = [];
    var orderData = [];
    for (key in totalData) {
        labels.push(key);
        studentData.push(totalData[key].student);
        orderData.push(totalData[key].order);
    }

    var config = {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Lao động',
                    backgroundColor: chartColors[0],
                    borderColor: chartColors[0],
                    data: studentData,
                    fill: false,
                },
                {
                    label: 'Đơn hàng',
                    backgroundColor: chartColors[4],
                    borderColor: chartColors[4],
                    data: orderData,
                    fill: false,
                }
            ]
        },
        options: lineChartOptions
    };

    var ctx = document.getElementById('line-chart').getContext('2d');
    ctx.height = 300;
    window.myLine = new Chart(ctx, config);
}

function renderPiechart(population, pieChartEleId, max, totalId) {
    var ctx = document.getElementById(pieChartEleId).getContext('2d');
    if (population.length == 0) {
        var width = $('#' + pieChartEleId).closest('.chart-responsive')[0].clientWidth;
        var height = $('#' + pieChartEleId).closest('.chart-responsive')[0].clientHeight;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = "30px normal 'Helvetica Nueue'";
        ctx.fillText("Không có dữ liệu", width / 2, height / 2);
    } else {
        var datas = [];
        var backgroundColors = [];
        var labels = [];
        var total = 0;
        population.forEach(function (ele, index) {
            if (index <= max) {
                var label = ele._matchingData.Cities.name
                if (index == max) {
                    label = "Tỉnh thành khác"
                }
                data = {
                    value: ele.count,
                    color: chartColors[index],
                    highlight: chartColors[index],
                    label: label
                };
                datas.push(ele.count);
                backgroundColors.push(chartColors[index]);
                labels.push(label);
            } else {
                datas[max] = datas[max] + ele.count;
            }
            total += ele.count;
        });
        var config = {
            type: 'pie',
            data: {
                datasets: [
                    {
                        data: datas,
                        backgroundColor: backgroundColors
                    }
                ],
                labels: labels
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        title: function (tooltipItem, data) {
                            return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function (tooltipItem, data) {
                            return data['datasets'][0]['data'][tooltipItem['index']] + ' lao động';
                        },

                    }
                },
                responsive: true
            }
        };
        window.myPie = new Chart(ctx, config);

        var totalStr = '(' + total + ' lao động)';
        $(totalId).html(totalStr);
    }
}

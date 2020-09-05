var perData = {};
perData.feeCounter = 0;
var chartColors = ['rgb(255, 99, 132)', 'rgb(255, 159, 64)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(54, 162, 235)', 'rgb(153, 102, 255)'];
// init handlebars
if ($('#fee-template')[0]) {
    var feeTemplate = Handlebars.compile($('#fee-template').html());
}
$(document).ready(function () {
    perData.feeCounter = $('#installment-fees-container > tr').length;
    // render chart
    if ($('#installments-chart')[0]) {
        renderInstallmentChart();
    }

    $('.admin-company-chart').change(function (e) {
        $.ajax({
            type: 'GET',
            url: DOMAIN_NAME + `/installments/report/${this.value}`,
            data: {
                adminCompanyId: this.value
            },
            success: function (resp) {
                report = resp.report;
                renderInstallmentChart();
            }
        });
    })

    $('.submit-installment-btn').click(function () {
        var validateResult = $('#add-installment-form').parsley().validate();
        if (validateResult) {
            $('#add-installment-form')[0].submit();
        }
    });
})


function extractYear(year) {
    data = {};
    data.managementFee = [];
    data.airTicketFee = [];
    data.trainingFee = [];
    data.otherFees = [];
    data.totalVN = [];
    data.totalJP = [];
    [1, 2, 3, 4].forEach(quarter => {
        let mf = 0, atf = 0, tf = 0, ofs = 0, tvn = 0, tjp = 0;
        if (quarter in report[year]) {
            mf = report[year][quarter]['managementFee'];
            atf = report[year][quarter]['airTicketFee'];
            tf = report[year][quarter]['trainingFee'];
            ofs = report[year][quarter]['otherFees'];
            tvn = report[year][quarter]['totalVN'];
            tjp = report[year][quarter]['totalJP'];
        }
        data.managementFee.push(mf);
        data.airTicketFee.push(atf);
        data.trainingFee.push(tf);
        data.otherFees.push(ofs);
        data.totalVN.push(tvn);
        data.totalJP.push(tjp);
    });
    return data
}

function renderInstallmentChart() {
    var currentTime = new Date();
    var currYear = currentTime.getFullYear();
    var prevYear = currYear - 1;
    var currYearData = extractYear(currYear), prevYearData = extractYear(prevYear);
    var chartData = {
        labels: ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'],
        datasets: [
            {
                label: `Tổng tiền vào TK ${prevYear}`,
                type: 'line',
                borderColor: chartColors[4],
                backgroundColor: chartColors[4],
                borderWidth: 2,
                fill: false,
                data: prevYearData.totalVN,
                yAxisID: 'y-axis-1',
                stack: prevYear
            },
            {
                label: 'Phí quản lý',
                backgroundColor: chartColors[0],
                data: prevYearData.managementFee,
                yAxisID: 'y-axis-2',
                stack: prevYear
            },
            {
                label: 'Vé máy bay',
                backgroundColor: chartColors[1],
                data: prevYearData.airTicketFee,
                yAxisID: 'y-axis-2',
                stack: prevYear
            },
            {
                label: 'Phí đào tạo',
                backgroundColor: chartColors[2],
                data: prevYearData.trainingFee,
                yAxisID: 'y-axis-2',
                stack: prevYear
            },
            {
                label: 'Khoản khác',
                backgroundColor: chartColors[3],
                data: prevYearData.otherFees,
                yAxisID: 'y-axis-2',
                stack: prevYear
            },
            {
                label: `Tổng tiền vào TK ${currYear}`,
                type: 'line',
                borderColor: chartColors[5],
                backgroundColor: chartColors[5],
                borderWidth: 2,
                fill: false,
                data: currYearData.totalVN,
                yAxisID: 'y-axis-1',
                stack: currYear
            },
            {
                label: 'Phí quản lý',
                backgroundColor: chartColors[0],
                data: currYearData.managementFee,
                yAxisID: 'y-axis-2',
                stack: currYear
            },
            {
                label: 'Vé máy bay',
                backgroundColor: chartColors[1],
                data: currYearData.airTicketFee,
                yAxisID: 'y-axis-2',
                stack: currYear
            },
            {
                label: 'Phí đào tạo',
                backgroundColor: chartColors[2],
                data: currYearData.trainingFee,
                yAxisID: 'y-axis-2',
                stack: currYear
            },
            {
                label: 'Khoản khác',
                backgroundColor: chartColors[3],
                data: currYearData.otherFees,
                yAxisID: 'y-axis-2',
                stack: currYear
            },
        ]
    };
    var ctx = document.getElementById('installments-chart').getContext('2d');
    // ctx.height = 300;
    if (!window.myBar) {
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                title: {
                    display: true,
                    text: `Biểu đồ quản lý phí ${prevYear}-${currYear}`
                },
                legend: {
                    labels: {
                        filter: function (item, chart) {
                            return item.datasetIndex >= 5 || item.datasetIndex == 0;
                        }
                    },
                    onClick: newLegendClickHandler
                },
                tooltips: {
                    // mode: 'index',
                    mode: 'x',
                    // position: 'average',
                    intersect: false,
                    // intersect: true,
                    callbacks: {
                        title: (tooltipItems, data) => {
                            let firstItem = tooltipItems[0];
                            if (tooltipItems.length == 4) {
                                return `${firstItem.xLabel} ${data.datasets[firstItem.datasetIndex].stack}`;
                            }
                            return firstItem.xLabel;
                        },
                        label: (tooltipItem, data) => {
                            let formatedNum = numberWithCommas(tooltipItem.yLabel.toString());
                            if (tooltipItem.datasetIndex == 0 || tooltipItem.datasetIndex == 5) {
                                return `${data.datasets[tooltipItem.datasetIndex].label}: ${formatedNum}₫`;
                            }
                            return `${data.datasets[tooltipItem.datasetIndex].label}: ${formatedNum}¥`;
                        },
                        footer: (tooltipItems, data) => {
                            if (tooltipItems.length == 4) {
                                let total = tooltipItems.reduce((a, e) => e.datasetIndex != 0 ? a + parseInt(e.yLabel) : a, 0);
                                return `Tổng cộng: ${numberWithCommas(total.toString())}¥`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [
                        {
                            stacked: true,
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Đợt thu phí'
                            }
                        }
                    ],
                    yAxes: [
                        {
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                            gridLines: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                callback: function (value, index, values) {
                                    return `${numberWithCommas(value.toString())}₫`;
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Việt Nam Đồng'
                            }
                        }, {
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',
                            stacked: true,
                            // grid line settings
                            gridLines: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                beginAtZero: true,
                                callback: function (value, index, values) {
                                    return `${numberWithCommas(value.toString())}¥`;
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Yên Nhật'
                            }
                        }
                    ]
                }
            }
        });
    } else {
        window.myBar.data = chartData;
        window.myBar.update();
    }
}

var defaultLegendClickHandler = Chart.defaults.global.legend.onClick;
var newLegendClickHandler = function (e, legendItem) {
    var index = legendItem.datasetIndex;
    let ci = this.chart;
    if (index == 0 || index == 5) {
        var meta = ci.getDatasetMeta(index);
        meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
    } else {
        [
            ci.getDatasetMeta(index),
            ci.getDatasetMeta(index - 5)
        ].forEach(function (meta) {
            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
        });
    }
    ci.update();
};

function resetAddFeesForm() {
    $('#add-fees-form')[0].reset();
    $('#modal-guild').val(null).trigger('change');
    $('#modal-status').val(null).trigger('change');
    $('#add-fees-form').parsley().reset();
}
function showAddFeesModal() {
    // reset form in modal
    resetAddFeesForm();

    $('#add-fees-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-fees-btn" onclick="addFees()">Hoàn tất</button>').insertBefore('#close-modal-btn');

    // show modal
    $('#add-fees-modal').modal('toggle');
}

function addFees() {
    // validate form
    var validateResult = $('#add-fees-form').parsley().validate();
    if (validateResult) {
        var feeHtml = createFeeTemplate(perData.feeCounter);
        $('#installment-fees-container').prepend(feeHtml);
        // summary
        calSum()
        // close modal
        $('#add-fees-modal').modal('toggle');
        resetAddFeesForm();
        perData.feeCounter++;
    }
}

function createFeeTemplate(counter) {
    var managementFeeVal = parseInt($('#add-management-fee').val());
    var airTicketFeeVal = parseInt($('#add-air-ticket-fee').val());
    var trainingFeeVal = parseInt($('#add-training-fee').val());
    var otherFeesVal = parseInt($('#add-other-fees').val());

    var totalJpVal = managementFeeVal + airTicketFeeVal + trainingFeeVal + otherFeesVal;
    var totalJpTxt = numberWithCommas(totalJpVal.toString());

    var totalVnVal = $('#add-total-vn').val();
    var totalVnTxt = (totalVnVal !== null && totalVnVal !== "") ? numberWithCommas(totalVnVal) : '';

    var feeHtml = feeTemplate({
        'counter': counter,

        'guild': `installment_fees[${counter}][guild_id]`,
        'guildTxt': $('#modal-guild option:selected').html(),
        'guildVal': $('#modal-guild').val(),

        'managementFee': `installment_fees[${counter}][management_fee]`,
        'managementFeeTxt': $('#add-management-fee-txt').val(),
        'managementFeeVal': managementFeeVal,

        'airTicketFee': `installment_fees[${counter}][air_ticket_fee]`,
        'airTicketFeeTxt': $('#add-air-ticket-fee-txt').val(),
        'airTicketFeeVal': airTicketFeeVal,

        'trainingFee': `installment_fees[${counter}][training_fee]`,
        'trainingFeeTxt': $('#add-training-fee-txt').val(),
        'trainingFeeVal': trainingFeeVal,

        'otherFees': `installment_fees[${counter}][other_fees]`,
        'otherFeesTxt': $('#add-other-fees-txt').val(),
        'otherFeesVal': otherFeesVal,

        'totalVn': `installment_fees[${counter}][total_vn]`,
        'totalVnTxt': totalVnTxt,
        'totalVnVal': totalVnVal,

        'totalJp': `installment_fees[${counter}][total_jp]`,
        'totalJpTxt': totalJpTxt,
        'totalJpVal': totalJpVal,

        'invoiceDate': `installment_fees[${counter}][invoice_date]`,
        'invoiceDateTxt': $('#add-invoice-date').val(),
        'invoiceDateVal': $('#add-invoice-date').val(),

        'revMoneyDate': `installment_fees[${counter}][receiving_money_date]`,
        'revMoneyDateTxt': $('#add-receiving-money-date').val(),
        'revMoneyDateVal': $('#add-receiving-money-date').val(),

        'status': `installment_fees[${counter}][status]`,
        'statusTxt': $('#modal-status option:selected').html(),
        'statusVal': $('#modal-status').val(),

        'notes': `installment_fees[${counter}][notes]`,
        'notesTxt': $('#add-notes').val().replace(/\r?\n/g, '<br>'),
        'notesVal': $('#add-notes').val()
    });
    return feeHtml;
}

function showEditFeesModal(ele) {
    resetAddFeesForm();

    var $row = $(ele).closest('.row-fee');
    $('#modal-guild').val($row.find('.guild').val()).trigger('change');

    $('#add-management-fee-txt').val(numberWithCommas($row.find('.management-fee').val()));
    $('#add-management-fee').val($row.find('.management-fee').val());

    $('#add-air-ticket-fee-txt').val(numberWithCommas($row.find('.air-ticket-fee').val()));
    $('#add-air-ticket-fee').val($row.find('.air-ticket-fee').val());

    $('#add-training-fee-txt').val(numberWithCommas($row.find('.training-fee').val()));
    $('#add-training-fee').val($row.find('.training-fee').val());

    $('#add-other-fees-txt').val(numberWithCommas($row.find('.other-fees').val()));
    $('#add-other-fees').val($row.find('.other-fees').val());

    $('#add-total-vn-txt').val(numberWithCommas($row.find('.total-vn').val()));
    $('#add-total-vn').val($row.find('.total-vn').val());

    $('#add-invoice-date').val($row.find('.invoice-date').val());

    $('#add-receiving-money-date').val($row.find('.rev-money-date').val());

    $('#modal-status').val($row.find('.status').val()).trigger('change');

    $('#add-notes').val($row.find('.notes').val());

    var rowIdArr = $row.attr('id').split('-');
    // replace add-btn with edit-btn
    $('#add-fees-btn').remove();
    $(`<button type="button" class="btn btn-success" id="add-fees-btn" onclick="editFees(${rowIdArr[rowIdArr.length - 1]})">Hoàn tất</button>`).insertBefore('#close-modal-btn');

    // show modal
    $('#add-fees-modal').modal('toggle');
}

function editFees(rowNum) {
    var validateResult = $('#add-fees-form').parsley().validate();
    if (validateResult) {
        var feeHtml = createFeeTemplate(rowNum);
        $(`#row-fee-${rowNum}`).replaceWith(feeHtml);
        // summary
        calSum();
        // hide modal
        $('#add-fees-modal').modal('toggle');
        resetAddFeesForm();
    }
}

function removeFees(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa phí quản lý nghiệp đoàn',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                // send ajax delete request to server
                var rowIdArr = $(delEl).closest('.row-fee').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                var id = $(`#installment-fee-id-${rowId}`).find('input').val()
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + `/installments/deleteFees/${id}`,
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteFeeRow(delEl, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteFeeRow(delEl);
    }
}

function deleteFeeRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-fee').remove();
    if (hiddenId) {
        // case: remove hidden id field of record exists in database
        $(`#installment-fee-id-${hiddenId}`).remove();
    }
    perData.feeCounter--;
    var idFields = $('.installment-fee-id').find('input');
    var $container = $('#installment-fees-container');
    var rows = $('#installment-fees-container > tr');
    var inputFields = $container.find('.form-control');

    for (var i = 0; i < rows.length; i++) {
        rows[i].id = `row-fee-${i}`;
        if (hiddenId) {
            $('.installment-fee-id')[i].id = `#installment-fee-id-${i}`;
            idFields[i].name = `installment_fees[${i}][id]`;
        }
    }

    for (var i = 0; i < inputFields.length; i++) {
        inputFields[i].name = inputFields[i].name.replace(/(?<=\[)\d+(?=\])/g, Math.floor(i / 11));
    }
    calSum();
}

function calSum() {
    var sumJp = sumVn = smf = satf = stf = sof = 0;
    var rows = $('#installment-fees-container > .row-fee');
    for (var i = 0; i < rows.length; i++) {
        totalVn = $(rows[i]).find('.total-vn').val();
        totalJp = $(rows[i]).find('.total-jp').val();
        _mf = $(rows[i]).find('.management-fee').val();
        _atf = $(rows[i]).find('.air-ticket-fee').val();
        _tf = $(rows[i]).find('.training-fee').val();
        _of = $(rows[i]).find('.other-fees').val();

        sumJp += (totalJp !== null && totalJp !== "") ? parseInt(totalJp) : 0;
        sumVn += (totalVn !== null && totalVn !== "") ? parseInt(totalVn) : 0;
        smf += (_mf !== null && _mf !== "") ? parseInt(_mf) : 0;
        satf += (_atf !== null && _atf !== "") ? parseInt(_atf) : 0;
        stf += (_tf !== null && _tf !== "") ? parseInt(_tf) : 0;
        sof += (_of !== null && _of !== "") ? parseInt(_of) : 0;
    }
    $('#summary-total-jp').html(numberWithCommas(sumJp.toString()));
    $('#summary-total-vn').html(numberWithCommas(sumVn.toString()));

    $('#summary-management-fee').html(`- Phí quản lý: ${numberWithCommas(smf.toString())}`);
    $('#summary-air-ticket-fee').html(`- Vé máy bay: ${numberWithCommas(satf.toString())}`);
    $('#summary-training-fee').html(`- Phí đào tạo: ${numberWithCommas(stf.toString())}`);
    $('#summary-other-fees').html(`- Khoản khác: ${numberWithCommas(sof.toString())}`);
}
var perData = {};
perData.familyCounter = 0;
perData.eduCounter = 0;
perData.expCounter = 0;
perData.langCounter = 0;
perData.physCounter = 0;
var initGraph;
var croppedIdGlobal, ratio;

var testDate = '';
if (typeof iqtests !== 'undefined' && iqtests.length > 0 && iqtests[0].test_date !== null) {
    testDate = moment(iqtests[0].test_date).format('YYYY-MM-DD')
}

var stName = '';
if (typeof studentName !== 'undefined') {
    stName = studentName;
}


function generateChartOptions(title, maximum) {
    return {
        responsive: true,
        title: {
            display: true,
            text: title,
            fontSize: 14
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
                    display: false,
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: false,
                },
                ticks: {
                    beginAtZero: true,
                    max: maximum
                }
            }]
        },
        animation: {
            onComplete: function () {
                isChartRendered = true
            }
        }
    };
}
// init handlebars
if ($('#family-template')[0]) {
    var family_template = Handlebars.compile($('#family-template').html());
}
if ($('#edu-template')[0]) {
    var edu_template = Handlebars.compile($('#edu-template').html());
}
if ($('#exp-template')[0]) {
    var exp_template = Handlebars.compile($('#exp-template').html());
}
if ($('#exp-template')[0]) {
    var lang_template = Handlebars.compile($('#lang-template').html());
}

$(document).ready(function () {
    // init dynamic data
    perData.familyCounter = $('#family-container > tr').length;
    perData.eduCounter = $('#edu-container > tr').length;
    perData.expCounter = $('#exp-container > tr').length;
    perData.langCounter = $('#lang-container > tr').length;
    perData.physCounter = $('#phys-container > tr').length;
    // init select2 status
    $('select[name="status"]').find('option').each(function () {
        if ($(this).attr('value') == "1") {
            $(this).prop('disabled', true);
        }
    });
    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            size: 'small'
        });
    });
    var changeCheckbox = document.getElementsByClassName('js-check-change');
    for (var i = 0; i < changeCheckbox.length; i++) {
        changeCheckbox[i].onchange = function () {
            if (this.checked) {
                $('input[name="' + this.name + '"]').val('1');
            } else {
                $('input[name="' + this.name + '"]').val('0');
            }
        };
    }

    $('#student-tabs').tabCollapse();

    if ($('#iq-vn-line-chart').length) {
        // iq chart
        var chartId = 'iq-vn-line-chart';
        var title = ['Biểu đồ điểm kiểm tra IQ * ' + studentNameVN + ' * ' + testDate, 'Tổng điểm: ' + iqtests[0].total];
        var labels = [
            'Câu1', 'Câu2', 'Câu3', 'Câu4', 'Câu5', 'Câu6', 'Câu7', 'Câu8', 'Câu9', 'Câu10',
            'Câu11', 'Câu12', 'Câu13', 'Câu14', 'Câu15', 'Câu16', 'Câu17', 'Câu18', 'Câu19', 'Câu20',
            'Câu21', 'Câu22', 'Câu23', 'Câu24'
        ];
        var datasets = [
            {
                label: 'Điểm',
                backgroundColor: 'rgb(54, 162, 235)',
                borderColor: 'rgb(54, 162, 235)',
                data: [
                    iqtests[0].q1, iqtests[0].q2, iqtests[0].q3, iqtests[0].q4, iqtests[0].q5, iqtests[0].q6, iqtests[0].q7, iqtests[0].q8,
                    iqtests[0].q9, iqtests[0].q10, iqtests[0].q11, iqtests[0].q12, iqtests[0].q13, iqtests[0].q14, iqtests[0].q15, iqtests[0].q16,
                    iqtests[0].q17, iqtests[0].q18, iqtests[0].q19, iqtests[0].q20, iqtests[0].q12, iqtests[0].q22, iqtests[0].q23, iqtests[0].q24,
                ],
                fill: false,
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointHoverBackgroundColor: '#fff'
            },
        ];
        renderLineChart(chartId, title, labels, datasets, 16);
    }

    if ($('#iq-jp-line-chart').length) {
        // iq chart
        var chartId = 'iq-jp-line-chart';
        var title = ['クレペリン検査 * ' + stName + ' * ' + testDate, '合計: ' + iqtests[0].total];
        var labels = [
            '文1', '文2', '文3', '文4', '文5', '文6', '文7', '文8', '文9', '文10',
            '文11', '文12', '文13', '文14', '文15', '文16', '文17', '文18', '文19', '文20',
            '文21', '文22', '文23', '文24'
        ];
        var datasets = [
            {
                label: '点',
                backgroundColor: 'rgb(54, 162, 235)',
                borderColor: 'rgb(54, 162, 235)',
                data: [
                    iqtests[0].q1, iqtests[0].q2, iqtests[0].q3, iqtests[0].q4, iqtests[0].q5, iqtests[0].q6, iqtests[0].q7, iqtests[0].q8,
                    iqtests[0].q9, iqtests[0].q10, iqtests[0].q11, iqtests[0].q12, iqtests[0].q13, iqtests[0].q14, iqtests[0].q15, iqtests[0].q16,
                    iqtests[0].q17, iqtests[0].q18, iqtests[0].q19, iqtests[0].q20, iqtests[0].q12, iqtests[0].q22, iqtests[0].q23, iqtests[0].q24,
                ],
                fill: false,
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointHoverBackgroundColor: '#fff'
            },
        ];
        renderLineChart(chartId, title, labels, datasets, 16);
    }

    if ($('#jtest-score-line-chart').length && $('#total-radar-chart').length && jtestScore.length) {
        var chartId = ['jtest-score-line-chart', 'total-radar-chart'];
        var title = ['Biểu đồ điểm thi tiếng Nhật', 'Biểu đồ đánh giá năng lực'];
        initChart(jtestScore, chartId, title)
    }

    var focusTab = window.location.hash;
    if (focusTab) {
        $('#student-tabs a[href="' + focusTab + '"]').tab('show');
    }

    $('.iqtest_score').change(function () {
        var total = 0;
        $('.iqtest_score').each(function () {
            total += parseInt($(this).val());
        });
        $('#iqtest_total').val(total);
    });

    $('.select-city').change(function (e) {
        var token = getToken(this);
        if (this.value == null || this.value == '') {
            $('#addresses-' + token + '-district-id').empty().append('<option value=""></option>');
            $('#addresses-' + token + '-district-id').prop('disabled', true);
        } else {
            $.ajax({
                type: 'GET',
                url: DOMAIN_NAME + '/students/getDistrict',
                data: {
                    city: this.value
                },
                success: function (resp) {
                    var processedOptions = $.map(resp, function (obj, index) {
                        return { id: index, text: obj };
                    });

                    // init select2-district with response data
                    if ($('#addresses-' + token + '-district-id').hasClass('select2-hidden-accessible')) {
                        $('#addresses-' + token + '-district-id').select2('destroy').empty().append('<option value=""></option>');
                    }

                    // enable select2
                    $('#addresses-' + token + '-district-id').prop('disabled', false);

                    // reset validation
                    $('#addresses-' + token + '-district-id').parsley().reset();
                    $('#addresses-' + token + '-district-id').select2({
                        placeholder: 'Xin hãy chọn giá trị',
                        data: processedOptions,
                        allowClear: true,
                        theme: "bootstrap",
                        language: {
                            noResults: function () {
                                return "Không tìm thấy kết quả";
                            }
                        }
                    });
                }
            });
        }

        // clear select2-ward, street input data
        $('#addresses-' + token + '-ward-id').empty().append('<option value=""></option>');
        $('#addresses-' + token + '-ward-id').prop('disabled', true);
        $('#addresses-' + token + '-street').val('');
    });

    $('.select-district').change(function (e) {
        var token = getToken(this);
        // re-validate input
        $('#addresses-' + token + '-district-id').parsley().validate();
        if ($('#addresses-' + token + '-district-id').hasClass('parsley-success')) {
            $('#select2-addresses-' + token + '-district-id').removeClass('parsley-error');
        } else if ($('#addresses-' + token + '-district-id').hasClass('parsley-error')) {
            $('#select2-addresses-' + token + '-district-id').addClass('parsley-error');
        }

        if (this.value == null || this.value == '') {
            $('#addresses-' + token + '-ward-id').empty().append('<option value=""></option>');
            $('#addresses-' + token + '-ward-id').prop('disabled', true);
        } else {
            // Send ajax get ward options
            $.ajax({
                type: 'GET',
                url: DOMAIN_NAME + '/students/getWard',
                data: {
                    district: this.value
                },
                success: function (resp) {
                    var processedOptions = $.map(resp, function (obj, index) {
                        return { id: index, text: obj };
                    });

                    // init select2 with response data
                    if ($('#addresses-' + token + '-ward-id').hasClass('select2-hidden-accessible')) {
                        $('#addresses-' + token + '-ward-id').select2('destroy').empty().append('<option value=""></option>');
                    }

                    // enable select2
                    $('#addresses-' + token + '-ward-id').prop('disabled', false);

                    // reset validation
                    $('#addresses-' + token + '-ward-id').parsley().reset();
                    $('#addresses-' + token + '-ward-id').select2({
                        placeholder: 'Xin hãy chọn giá trị',
                        data: processedOptions,
                        allowClear: true,
                        theme: "bootstrap",
                        language: {
                            noResults: function () {
                                return "Không tìm thấy kết quả";
                            }
                        }
                    });
                }
            });
        }
        // clear street input data
        $('#addresses-' + token + '-street').val('');
    });

    $('.select-ward').change(function (e) {
        var token = getToken(this);
        // re-validate input
        $('#addresses-' + token + '-ward-id').parsley().validate();
        if ($('#addresses-' + token + '-ward-id').hasClass('parsley-success')) {
            $('#select2-addresses-' + token + '-ward-id').removeClass('parsley-error');
        } else if ($('#addresses-' + token + '-ward-id').hasClass('parsley-error')) {
            $('#select2-addresses-' + token + '-ward-id').addClass('parsley-error');
        }
        // clear street input data
        $('#addresses-' + token + '-street').val('');
    });

    // $('.js-switch').click(function (e) {
    //     if ($(this)[0].checked) {
    //         $(this).closest('tr').find('select').prop('disabled', false);
    //     } else {
    //         // clear select value
    //         $(this).closest('tr').find('select').val(null).trigger('change');
    //         $(this).closest('tr').find('select').prop('disabled', true);
    //     }
    // });

    // initSelect2AjaxSearch('std-order-name', DOMAIN_NAME + '/orders/search-order', 'Tìm kiếm đơn hàng');
    // initSelect2AjaxSearch('std-company-name', DOMAIN_NAME + '/companies/search-company', 'Tìm kiếm công ty');
    // initSelect2AjaxSearch('std-guild-name', DOMAIN_NAME + '/guilds/search-guild', 'Tìm kiếm nghiệp đoàn');
    // initSelect2AjaxSearch('std-class-name', DOMAIN_NAME + '/jclasses/search-class', 'Tìm kiếm lớp học');

    $('select[name="is_lived_in_japan"]').change(function () {
        if ($(this).val() == 'Y') {
            // show time input
            $('.time-lived-jp').removeClass('hidden');
        } else {
            // remove data
            $('.time-lived-jp').addClass('hidden');
            $('#lived-from').val('');
            $('#lived-to').val('');
        }
    });

    $('.create-student-btn').click(function () {
        var validateResult = $('#create-student-form').parsley().validate();

        // check parsley error exists
        for (var i = 0; i < 2; i++) {
            if ($('#addresses-' + i + '-district-id').hasClass('parsley-error')) {
                $('#select2-addresses-' + i + '-district-id').addClass('parsley-error');
            }
            if ($('#addresses-' + i + '-ward-id').hasClass('parsley-error')) {
                $('#select2-addresses-' + i + '-ward-id').addClass('parsley-error');
            }
        }

        if (validateResult) {
            // submit form
            $('#create-student-form').submit();
        } else {
            var closestTab = $('.parsley-error')[0].closest('.root-tab-pane');

            if (closestTab) {
                var firstEleErrorId = closestTab.id;
                // focus to first tab-pane contain error
                $('#student-tabs a[href="#' + firstEleErrorId + '"]').tab('show');

                var tmpTab = $('.parsley-error')[0].closest('.tab-pane');
                if (tmpTab && (tmpTab.id === 'household' || tmpTab.id === 'current-address')) {
                    $('#address-tabs a[href="#' + tmpTab.id + '"]').tab('show');
                }
            } else {
                closestTab = $('.parsley-error')[0].closest('.panel-collapse');
                var firstEleErrorId = closestTab.id;
                // focus to first tab-collapse contain error
                $('#' + firstEleErrorId).collapse('show');
                $('#student-tabs-accordion .panel-collapse').not('#' + firstEleErrorId).collapse('hide');
            }

            setTimeout(function () {
                $('.parsley-error')[0].focus();
            }, 500);
        }
    });

    $('.zoom-able').click(function () {
        $('.cropper-container').remove();
        $('#avatar').attr('src', $(this).attr('src'));
        $('#avatar').removeClass('cropper-hidden');
        $('#crop-btn').addClass('hidden');
        $('#cropper-modal').modal('toggle');
    });
})

function showAddStudentModal() {
    // reset form
    $('#add-candidate-form')[0].reset();
    $('#addresses-0-city-id').val(null).trigger('change');
    $('#gender').val(null).trigger('change');
    $('#educational-level').val(null).trigger('change');
    $('#add-candidate-form').parsley().reset();

    // remove hidden address id
    $('#addresses-0-id').remove();
    // change modal title
    $('#add-candidate-modal').find('.modal-title').html('THÊM MỚI LỊCH HẸN');
    // change to add button
    $('#add-candidate-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-candidate-btn" onclick="addCandidate()">Hoàn tất</button>').insertBefore('#add-candidate-close-btn');
    // formChanged = false;
    $('#add-candidate-modal').modal('toggle');
}

function viewSCandidate(candidateId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-student-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/students/getStudent',
        data: {
            id: candidateId
        },
        success: function (resp) {
            if (resp.status == 'success') {
                var phoneNum = str2Phone(resp.data.phone);
                if (phoneNum == '') {
                    phoneNum = 'N/A';
                }
                // fill data
                $('#view-candidate-name').html(resp.data.fullname);
                $('#view-candidate-gender').html(resp.gender);
                $('#view-candidate-phone').html(phoneNum);
                $('#view-candidate-appointment-date').html(convertDate(resp.appointment_date));
                $('#view-candidate-birthday').html(convertDate(resp.birthday));
                $('#view-candidate-address').html(resp.data.addresses[0].city.name);
                $('#view-candidate-edu-level').html(resp.edu_level);
                $('#view-candidate-exempt').html(resp.exempt);

                if (resp.data.created_by_user) {
                    $('#view-candidate-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-candidate-created-by').html('N/A');
                }
                $('#view-candidate-created').html(resp.created);
                if (resp.data.modified_by) {
                    $('.modified').removeClass('hidden');
                    $('#view-candidate-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-candidate-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }
                if (resp.data.note.length == 0) {
                    $('#view-candidate-note').html('N/A');
                } else {
                    $('#view-candidate-note').html((resp.data.note).replace(/\r?\n/g, '<br/>'));
                }

                $('#view-candidate-modal').modal('toggle');
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
            $('#list-student-overlay').addClass('hidden');
        }
    });
}

function addCandidate() {
    // formChanged = false;
    var validateResult = $('#add-candidate-form').parsley().validate();
    if (validateResult) {
        $('#add-candidate-form').submit()
    }
}

function showEditStudentModal(candidateId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-student-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/students/getStudent',
        data: {
            id: candidateId
        },
        success: function (resp) {
            if (resp.status == 'success') {
                // create hidden id for address
                $('#add-candidate-form').append('<input type="hidden" id="addresses-0-id" name="addresses[0][id]" value="' + resp.data.addresses[0].id + '" />');
                // fill form
                $('#fullname').val(resp.data.fullname);
                $('#gender').val(resp.data.gender).trigger('change');
                $('#phone').val(resp.data.phone);
                $('input[name="appointment_date"]').val(convertDate(resp.data.appointment_date)).trigger('change');
                $('input[name="birthday"]').val(convertDate(resp.data.birthday)).trigger('change');
                $('#addresses-0-city-id').val(resp.data.addresses[0].city_id).trigger('change');
                $('#educational-level').val(resp.data.educational_level).trigger('change');
                $('#exempt').val(resp.data.exempt).trigger('change');
                $('#note').val(resp.data.note);

                // change modal title
                $('#add-candidate-modal').find('.modal-title').html('CẬP NHẬT LỊCH HẸN');
                // change to edit button
                $('#add-candidate-btn').remove();
                $('<button type="button" class="btn btn-success" id="add-candidate-btn" onclick="editCandidate(' + resp.data.id + ')">Hoàn tất</button>').insertBefore('#add-candidate-close-btn');

                $('#add-candidate-modal').modal('toggle');
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
            $('#list-student-overlay').addClass('hidden');
        }
    });
}

function editCandidate(id) {
    if (ajaxing) {
        // still requesting
        return;
    }

    //validate form
    var validateResult = $('#add-candidate-form').parsley().validate();
    if (validateResult) {
        ajaxing = true;
        $.ajax({
            type: 'POST',
            url: DOMAIN_NAME + '/students/edit/' + id,
            data: $('#add-candidate-form').serialize(),
            success: function (resp) {
                if (resp.status == 'success') {
                    window.location = resp.redirect;
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
}

function setTimeLived() {
    // validate form
    var validateResult = $('#set-lived-japan-form').parsley().validate();
    if (validateResult) {
        var timeInterval = $('#jp-from-date').val() + ' ～ ' + $('#jp-to-date').val();
        $('.time-lived-jp').removeClass('hidden');
        $('.time-lived').append(timeInterval);

        // set value
        $('#lived-from').val($('#jp-from-date').val());
        $('#lived-to').val($('#jp-to-date').val());

        // close modal
        $('#lived-japan-modal').modal('toggle');
    }

    // reset form
    $('#set-lived-japan-form')[0].reset();
}

function getToken(thisEle) {
    var token = 0;
    if (thisEle.closest('.tab-pane').id === 'current-address') {
        token = 1;
    }
    return token;
}

// Family Manager
function createMemberTemplate(counter) {
    var member_html = family_template({
        'row': counter + 1,
        'counter': counter,

        'fullname': 'families[' + counter + '][fullname]',
        'fullnameVal': $('#modal-fullname').val(),

        'birthday': 'families[' + counter + '][birthday]',
        'birthdayVal': $('#modal-birthday').val(),

        'relationship': 'families[' + counter + '][relationship]',
        'relationshipText': $('#modal-relationship option:selected').html(),

        'job': 'families[' + counter + '][job_id]',
        'jobText': $('#modal-job option:selected').html(),

        'address': 'families[' + counter + '][address]',
        'addressVal': $('#modal-address').val(),

        'livingAt': 'families[' + counter + '][living_at]',

        'bankNum': 'families[' + counter + '][bank_num]',
        'bankNumVal': $('#modal-bank-num').val(),

        'bankName': 'families[' + counter + '][bank_name]',

        'bankBranch': 'families[' + counter + '][bank_branch]',
        'bankBranchVal': $('#modal-bank-branch').val(),

        'cmndNum': 'families[' + counter + '][cmnd_num]',
        'cmndNumVal': $('#modal-cmnd-num').val(),

        'phone': 'families[' + counter + '][phone]',
        'phoneVal': $('#modal-phone').val(),
    });
    return member_html;
}

function showAddMemberModal() {
    // renew add-btn
    $('#add-member-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-member-btn" onclick="addMember()">Hoàn tất</button>').insertBefore('#close-modal-btn');
    // reset form in modal
    resetFamilyModal();
    // show modal
    $('#add-member-modal').modal('toggle');
}

function addMember() {
    // validate form
    var validateResult = $('#add-member-form').parsley().validate();
    if (validateResult) {
        var member_html = createMemberTemplate(perData.familyCounter);

        $('#family-container').append(member_html);

        // set value for select box
        $('select[name="families[' + perData.familyCounter + '][relationship]"]').val($('#modal-relationship').val());
        $('select[name="families[' + perData.familyCounter + '][job_id]"]').val($('#modal-job').val());
        $('select[name="families[' + perData.familyCounter + '][bank_name]"]').val($('#modal-bank-name').val());
        $('select[name="families[' + perData.familyCounter + '][living_at]"]').val($('#modal-living-at').val());

        // close modal
        $('#add-member-modal').modal('toggle');

        // reset form in modal
        resetFamilyModal();

        perData.familyCounter++;
    }
}

function showEditMemberModal(ele) {
    // fill data to modal form
    $('#modal-fullname').val($(ele).closest('.row-member').find('.fullname').val());
    $('#modal-birthday').val($(ele).closest('.row-member').find('.birthday').val()).trigger('change');
    $('#modal-relationship').val($(ele).closest('.row-member').find('.relationship').val()).trigger('change');
    $('#modal-job').val($(ele).closest('.row-member').find('.job_id').val()).trigger('change');
    $('#modal-address').val($(ele).closest('.row-member').find('.address').val()).trigger('change');
    $('#modal-living-at').val($(ele).closest('.row-member').find('.living_at').val()).trigger('change');
    $('#modal-bank-num').val($(ele).closest('.row-member').find('.bank_num').val());
    $('#modal-cmnd-num').val($(ele).closest('.row-member').find('.cmnd_num').val());

    $('#modal-bank-name').val($(ele).closest('.row-member').find('.bank_name').val()).trigger('change');;
    $('#modal-bank-branch').val($(ele).closest('.row-member').find('.bank_branch').val());

    $('#modal-phone').val($(ele).closest('.row-member').find('.phone').val());
    var rowIdArr = $(ele).closest('.row-member').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-member-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-member-btn" onclick="editMember(' + rowIdArr[rowIdArr.length - 1] + ')">Hoàn tất</button>').insertBefore('#close-modal-btn');

    $('#add-member-form').parsley().reset();

    // show modal
    $('#add-member-modal').modal('toggle');
}

function editMember(rowId) {
    //validate form
    var validateResult = $('#add-member-form').parsley().validate();
    if (validateResult) {
        var member_html = createMemberTemplate(rowId);

        $('#row-member-' + rowId).replaceWith(member_html);

        // set value for select box
        $('select[name="families[' + rowId + '][relationship]"]').val($('#modal-relationship').val());
        $('select[name="families[' + rowId + '][job_id]"]').val($('#modal-job').val());
        $('select[name="families[' + rowId + '][bank_name]"]').val($('#modal-bank-name').val());
        $('select[name="families[' + rowId + '][living_at]"]').val($('#modal-living-at').val());

        // close modal
        $('#add-member-modal').modal('toggle');

        // reset form in modal
        resetFamilyModal();
    }
}

function removeMember(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa thành viên gia đình',
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
                var rowIdArr = $(delEl).closest('.row-member').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteFamilyMember',
                    data: {
                        'id': $('#member-' + rowId + '-id').find('input').val()
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteMemberRow(delEl, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteMemberRow(delEl);
    }
}

function deleteMemberRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-member').remove();
    if (hiddenId) {
        // case: remove hidden id fiedl of record exists in database
        $('#member-' + hiddenId + '-id').remove();
    }
    perData.familyCounter--;

    var trows = $('#family-container > tr');
    var idField = $('.member-id').find('input');
    var inputField = $('#family-container').find('input');
    var selectField = $('#family-container').find('select');
    var sttField = $('#family-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-member-' + i;
        if (hiddenId) {
            $('.member-id')[i].id = 'member-' + i + '-id';
            idField[i].name = 'families[' + i + '][id]';
        }
    }

    for (var i = 0; i < inputField.length; i++) {
        var classArr = inputField[i].className.split(' ');
        inputField[i].name = 'families[' + Math.floor(i / 7) + '][' + classArr[classArr.length - 1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        var classArr = selectField[i].className.split(' ');
        selectField[i].name = 'families[' + Math.floor(i / 4) + '][' + classArr[classArr.length - 1] + ']';
    }
}

function resetFamilyModal() {
    $('#add-member-form')[0].reset();
    $('#modal-relationship').val(null).trigger('change');
    $('#modal-living-at').val(null).trigger('change');
    $('#modal-job').val(null).trigger('change');
    $('#add-member-form').parsley().reset();
}
// for read-only
function showMemberModal(ele) {
    $('.modal-fullname').html($(ele).closest('.row-member').find('.family-fullname').html());
    $('.modal-birthday').html($(ele).closest('.row-member').find('.family-birthday').html());
    $('.modal-relationship').html($(ele).closest('.row-member').find('.family-relationship').html());
    $('.modal-job').html($(ele).closest('.row-member').find('.family-job-name').html());
    $('.modal-address').html($(ele).closest('.row-member').find('.family-address').html());
    $('.modal-bank-num').html($(ele).closest('.row-member').find('.family-bank-num').html());
    $('.modal-bank-name').html($(ele).closest('.row-member').find('.family-bank-name').html());
    $('.modal-bank-branch').html($(ele).closest('.row-member').find('.family-bank-branch').html());
    $('.modal-cmnd-num').html($(ele).closest('.row-member').find('.family-cmnd-num').html());
    $('.modal-phone').html($(ele).closest('.row-member').find('.family-phone').html());
    // show modal
    $('#member-modal').modal('toggle');
}
// Education Manager
function createEduHisTemplate(counter) {
    var certificateVal = '';
    if ($('#edu-certificate').val()) {
        certificateVal = moment($('#edu-certificate').val(), 'MM-YYYY').format('YYYY-MM');
    }
    var edu_html = edu_template({
        'row': counter + 1,
        'counter': counter,

        'fromdate': 'educations[' + counter + '][from_date]',
        'fromdateTxt': $('#edu-from-date').val(),
        'fromdateVal': moment($('#edu-from-date').val(), 'MM-YYYY').format('YYYY-MM'),

        'todate': 'educations[' + counter + '][to_date]',
        'todateTxt': $('#edu-to-date').val(),
        'todateVal': moment($('#edu-to-date').val(), 'MM-YYYY').format('YYYY-MM'),

        'graduate': 'educations[' + counter + '][graduate]',
        'graduateVal': $('#edu-graduate').val(),

        'degree': 'educations[' + counter + '][degree]',
        'degreeText': $('#modal-edu-level option:selected').html(),

        'school': 'educations[' + counter + '][school]',
        'schoolVal': $('#edu-school').val(),

        'address': 'educations[' + counter + '][address]',
        'addressVal': $('#edu-address').val(),

        'specialized': 'educations[' + counter + '][specialized]',
        'specializedVal': $('#edu-specialized').val(),

        'specializedJP': 'educations[' + counter + '][specialized_jp]',
        'specializedJPVal': $('#edu-specialized-jp').val(),

        'certificate': 'educations[' + counter + '][certificate]',
        'certificateVal': certificateVal,
    });
    return edu_html;
}

function showAddEduHisModal() {
    // renew add-btn
    $('#add-edu-his-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-edu-his-btn" onclick="addEduHis()">Hoàn tất</button>').insertBefore('#close-edu-modal-btn');
    // reset form in modal
    resetEduHisModal();
    // show modal
    $('#add-edu-his-modal').modal('toggle');
}

function showEditEduHisModal(ele) {
    // fill data to modal form
    var eduFrom = $(ele).closest('.row-edu-his').find('.from_date').val();
    $('#edu-from-date').val(moment(eduFrom, 'YYYY-MM').format('MM-YYYY'));

    var eduTo = $(ele).closest('.row-edu-his').find('.to_date').val();
    $('#edu-to-date').val(moment(eduTo, 'YYYY-MM').format('MM-YYYY'));

    $('#modal-edu-level').val($(ele).closest('.row-edu-his').find('.degree').val()).trigger('change');
    $('#edu-school').val($(ele).closest('.row-edu-his').find('.school').val());
    $('#edu-graduate').val($(ele).closest('.row-edu-his').find('.graduate').val()).trigger('change');
    $('#edu-address').val($(ele).closest('.row-edu-his').find('.address').val());
    $('#edu-specialized').val($(ele).closest('.row-edu-his').find('.specialized').val());
    $('#edu-specialized-jp').val($(ele).closest('.row-edu-his').find('.specialized_jp').val());

    var eduCert = $(ele).closest('.row-edu-his').find('.certificate').val();
    if (eduCert) {
        $('#edu-certificate').val(moment(eduCert, 'YYYY-MM').format('MM-YYYY'));
    } else {
        $('#edu-certificate').val('');
    }
    var rowIdArr = $(ele).closest('.row-edu-his').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-edu-his-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-edu-his-btn" onclick="editEduHis(' + rowIdArr[rowIdArr.length - 1] + ')">Hoàn tất</button>').insertBefore('#close-edu-modal-btn');

    // reset validation
    $('#add-edu-his-form').parsley().reset();
    // show modal
    $('#add-edu-his-modal').modal('toggle');
}

function addEduHis() {
    //validate form
    var validateResult = $('#add-edu-his-form').parsley().validate();
    if (validateResult) {
        var edu_html = createEduHisTemplate(perData.eduCounter);

        $('#edu-container').append(edu_html);

        // set value for select box
        $('select[name="educations[' + perData.eduCounter + '][degree]"]').val($('#modal-edu-level').val());

        // close modal
        $('#add-edu-his-modal').modal('toggle');

        // reset form in modal
        resetEduHisModal();

        perData.eduCounter++;
    }
}

function editEduHis(rowId) {
    //validate form
    var validateResult = $('#add-edu-his-form').parsley().validate();
    if (validateResult) {
        var edu_html = createEduHisTemplate(rowId);

        $('#row-edu-his-' + rowId).replaceWith(edu_html);

        // set value for select box
        $('select[name="educations[' + rowId + '][degree]"]').val($('#modal-edu-level').val());

        // close modal
        $('#add-edu-his-modal').modal('toggle');

        // reset form in modal
        resetEduHisModal();
    }
}

function removeEduHis(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa lịch sử học vấn',
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
                var rowIdArr = $(delEl).closest('.row-edu-his').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteEducations',
                    data: {
                        'id': $('#edu-his-' + rowId + '-id').find('input').val()
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteEduHisRow(delEl, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteEduHisRow(delEl);
    }
}

function deleteEduHisRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-edu-his').remove();
    if (hiddenId) {
        // case: remove record exists in database
        $('#edu-his-' + hiddenId + '-id').remove();
    }
    perData.eduCounter--;

    var trows = $('#edu-container > tr');
    var idField = $('.edu-id').find('input');
    var inputField = $('#edu-container').find('input');
    var selectField = $('#edu-container').find('select');
    var sttField = $('#edu-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-edu-his-' + i;
        if (hiddenId) {
            $('.edu-id')[i].id = 'edu-his-' + i + '-id';
            idField[i].name = 'educations[' + i + '][id]';
        }
    }

    for (var i = 0; i < inputField.length; i++) {
        var classArr = inputField[i].className.split(' ');
        inputField[i].name = 'educations[' + Math.floor(i / 7) + '][' + classArr[classArr.length - 1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'educations[' + i + '][' + selectField[i].id + ']';
    }
}

function resetEduHisModal() {
    $('#add-edu-his-form')[0].reset();
    $('#modal-edu-level').val(null).trigger('change');
    $('#edu-his-from').data('DateTimePicker').maxDate(false);
    $('#edu-his-to').data('DateTimePicker').minDate(false);
    $('#add-edu-his-form').parsley().reset();
}
// for read-only
function showEduHisModal(ele) {
    $('.modal-edu-from-to').html($(ele).closest('.row-edu-his').find('.edu-from-to').html());
    $('.modal-edu-level').html($(ele).closest('.row-edu-his').find('.edu-level').html());
    $('.modal-edu-school').html($(ele).closest('.row-edu-his').find('.edu-school').html());
    $('.modal-edu-address').html($(ele).closest('.row-edu-his').find('.edu-address').html());
    $('.modal-edu-specialized').html($(ele).closest('.row-edu-his').find('.edu-specialized').html());
    // show modal
    $('#edu-his-modal').modal('toggle');
}

// Experience Manager
function createExpTemplate(counter) {
    var exp_html = exp_template({
        'row': counter + 1,
        'counter': counter,

        'fromdate': 'experiences[' + counter + '][from_date]',
        'fromdateVal': moment($('#exp-from-date').val(), 'MM-YYYY').format('YYYY-MM'),
        'fromdateTxt': $('#exp-from-date').val(),

        'todate': 'experiences[' + counter + '][to_date]',
        'todateVal': moment($('#exp-to-date').val(), 'MM-YYYY').format('YYYY-MM'),
        'todateTxt': $('#exp-to-date').val(),

        'job': 'experiences[' + counter + '][job_id]',
        'jobText': $('#exp-job option:selected').html(),

        'company': 'experiences[' + counter + '][company]',
        'companyVal': $('#exp-company').val(),

        'company_jp': 'experiences[' + counter + '][company_jp]',
        'companyJPVal': $('#exp-company-jp').val(),

        'salary': 'experiences[' + counter + '][salary]',
        'salaryVal': $('#exp-salary').val(),

        'address': 'experiences[' + counter + '][address]',
        'addressVal': $('#exp-address').val()
    });
    return exp_html;
}

function showAddExpModal() {
    // renew add-btn
    $('#add-exp-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-exp-btn" onclick="addExp()">Hoàn tất</button>').insertBefore('#close-exp-modal-btn');
    // reset form in modal
    resetExpModal();
    // show modal
    $('#add-exp-modal').modal('toggle');
}

function addExp() {
    // validate form
    var validateResult = $('#add-exp-form').parsley().validate();
    if (validateResult) {
        var exp_html = createExpTemplate(perData.expCounter);

        $('#exp-container').append(exp_html);

        // set value for select box
        $('select[name="experiences[' + perData.expCounter + '][job_id]"]').val($('#exp-job').val());

        // close modal
        $('#add-exp-modal').modal('toggle');

        // reset form in modal
        resetExpModal();

        perData.expCounter++;
    }
}

function showEditExpModal(ele) {
    // fill data to modal form
    var expFrom = $(ele).closest('.row-exp').find('.from_date').val();
    $('#exp-from-date').val(moment(expFrom, 'YYYY-MM').format('MM-YYYY'));

    var expTo = $(ele).closest('.row-exp').find('.to_date').val();
    $('#exp-to-date').val(moment(expTo, 'YYYY-MM').format('MM-YYYY'));

    $('#exp-job').val($(ele).closest('.row-exp').find('.job_id').val()).trigger('change');
    $('#exp-company').val($(ele).closest('.row-exp').find('.company').val());
    $('#exp-company-jp').val($(ele).closest('.row-exp').find('.company_jp').val());
    $('#exp-salary').val($(ele).closest('.row-exp').find('.salary').val());
    $('#exp-address').val($(ele).closest('.row-exp').find('.address').val());
    var rowIdArr = $(ele).closest('.row-exp').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-exp-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-exp-btn" onclick="editExp(' + rowIdArr[rowIdArr.length - 1] + ')">Hoàn tất</button>').insertBefore('#close-exp-modal-btn');

    // reset validation
    $('#add-exp-form').parsley().reset();

    // show modal
    $('#add-exp-modal').modal('toggle');
}

function editExp(rowId) {
    //validate form
    var validateResult = $('#add-exp-form').parsley().validate();
    if (validateResult) {
        var exp_html = createExpTemplate(rowId);

        $('#row-exp-' + rowId).replaceWith(exp_html);

        // set value for select box
        $('select[name="experiences[' + rowId + '][job_id]"]').val($('#exp-job').val());

        // close modal
        $('#add-exp-modal').modal('toggle');

        // reset form in modal
        resetEduHisModal();
    }
}

function removeExp(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa kinh nghiệm làm việc',
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
                var rowIdArr = $(delEl).closest('.row-exp').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteExperience',
                    data: {
                        'id': $('#exp-' + rowId + '-id').find('input').val()
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteExpRow(delEl, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteExpRow(delEl);
    }
}

function deleteExpRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-exp').remove();
    if (hiddenId) {
        // case: remove record exists in database
        $('#exp-' + hiddenId + '-id').remove();
    }
    perData.expCounter--;

    var trows = $('#exp-container > tr');
    var idField = $('.exp-id').find('input');
    var inputField = $('#exp-container').find('input');
    var selectField = $('#exp-container').find('select');
    var sttField = $('#exp-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-exp-' + i;
        if (hiddenId) {
            $('.exp-id')[i].id = 'exp-' + i + '-id';
            idField[i].name = 'experiences[' + i + '][id]';
        }
    }

    for (var i = 0; i < inputField.length; i++) {
        var classArr = inputField[i].className.split(' ');
        inputField[i].name = 'experiences[' + Math.floor(i / 5) + '][' + classArr[classArr.length - 1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'experiences[' + i + '][' + selectField[i].id + ']';
    }
}

function resetExpModal() {
    $('#add-exp-form')[0].reset();
    $('#exp-job').val(null).trigger('change');

    $('#exp-from').data('DateTimePicker').maxDate(false);
    $('#exp-to').data('DateTimePicker').minDate(false);

    $('#add-exp-form').parsley().reset();
}

// Language Manager
function createLangTemplate(counter) {
    var fromdateVal = '';
    var fromdateTxt = 'N/A';
    if ($('#lang-from-date').val()) {
        fromdateVal = moment($('#lang-from-date').val(), 'MM-YYYY').format('YYYY-MM');
        fromdateTxt = $('#lang-from-date').val();
    }
    var todateVal = '';
    var todateTxt = 'N/A';
    if ($('#lang-to-date').val()) {
        todateVal = moment($('#lang-to-date').val(), 'MM-YYYY').format('YYYY-MM');
        todateTxt = $('#lang-to-date').val();
    }
    var lang_html = lang_template({
        'row': counter + 1,
        'counter': counter,

        'language': 'language_abilities[' + counter + '][lang_code]',
        'languageText': $('#lang-name option:selected').html(),

        'cert': 'language_abilities[' + counter + '][certificate]',
        'certVal': $('#lang-certificate').val(),

        'fromdate': 'language_abilities[' + counter + '][from_date]',
        'fromdateTxt': fromdateTxt,
        'fromdateVal': fromdateVal,

        'todate': 'language_abilities[' + counter + '][to_date]',
        'todateTxt': todateTxt,
        'todateVal': todateVal,
    });
    return lang_html;
}

function showAddLangModal() {
    // renew add-btn
    $('#add-lang-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-lang-btn" onclick="addLang()">Hoàn tất</button>').insertBefore('#close-lang-modal-btn');
    // reset form in modal
    resetLangModal();
    // show modal
    $('#add-lang-modal').modal('toggle');
}

function addLang() {
    // validate form
    var validateResult = $('#add-lang-form').parsley().validate();
    if (validateResult) {
        var lang_html = createLangTemplate(perData.langCounter);

        $('#lang-container').append(lang_html);

        // set value for select box
        $('select[name="language_abilities[' + perData.langCounter + '][lang_code]"]').val($('#lang-name').val());

        // close modal
        $('#add-lang-modal').modal('toggle');

        // reset form in modal
        resetLangModal();

        perData.langCounter++;
    }
}

function showEditLangModal(ele) {
    // fill data to modal form
    var langFrom = $(ele).closest('.row-lang').find('.from_date').val();
    if (langFrom) {
        $('#lang-from-date').val(moment(langFrom, 'YYYY-MM').format('MM-YYYY'));
    } else {
        $('#lang-from-date').val('');
    }

    var langTo = $(ele).closest('.row-lang').find('.to_date').val();
    if (langTo) {
        $('#lang-to-date').val(moment(langTo, 'YYYY-MM').format('MM-YYYY'));
    } else {
        $('#lang-to-date').val('');
    }

    $('#lang-name').val($(ele).closest('.row-lang').find('.lang_code').val()).trigger('change');
    $('#lang-certificate').val($(ele).closest('.row-lang').find('.certificate').val());
    var rowIdArr = $(ele).closest('.row-lang').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-lang-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-lang-btn" onclick="editLang(' + rowIdArr[rowIdArr.length - 1] + ')">Hoàn tất</button>').insertBefore('#close-lang-modal-btn');

    // reset validate
    $('#add-lang-form').parsley().reset();

    // show modal
    $('#add-lang-modal').modal('toggle');
}

function editLang(rowId) {
    //validate form
    var validateResult = $('#add-lang-form').parsley().validate();
    if (validateResult) {
        var lang_html = createLangTemplate(rowId);

        $('#row-lang-' + rowId).replaceWith(lang_html);

        // set value for select box
        $('select[name="language_abilities[' + rowId + '][lang_code]"]').val($('#lang-name').val());

        // close modal
        $('#add-lang-modal').modal('toggle');

        // reset form in modal
        resetLangModal();
    }
}

function removeLang(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa năng lực ngôn ngữ',
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
                var rowIdArr = $(delEl).closest('.row-lang').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteLang',
                    data: {
                        'id': $('#lang-' + rowId + '-id').find('input').val()
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteLangRow(delEl, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteLangRow(delEl);
    }
}

function deleteLangRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-lang').remove();
    if (hiddenId) {
        // case: remove record exists in database
        $('#lang-' + hiddenId + '-id').remove();
    }
    perData.langCounter--;

    var trows = $('#lang-container > tr');
    var idField = $('.lang-id').find('input');
    var inputField = $('#lang-container').find('input');
    var selectField = $('#lang-container').find('select');
    var sttField = $('#lang-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-lang-' + i;
        if (hiddenId) {
            $('.lang-id')[i].id = 'lang-' + i + '-id';
            idField[i].name = 'language_abilities[' + i + '][id]';
        }
    }

    for (var i = 0; i < inputField.length; i++) {
        var classArr = inputField[i].className.split(' ');
        inputField[i].name = 'language_abilities[' + Math.floor(i / 5) + '][' + classArr[classArr.length - 1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'expelanguage_abilitiesriences[' + i + '][' + selectField[i].id + ']';
    }
}

function resetLangModal() {
    $('#add-lang-form')[0].reset();
    $('#lang-name').val(null).trigger('change');
    $('#lang-from').data('DateTimePicker').maxDate(false);
    $('#lang-to').data('DateTimePicker').minDate(false);

    $('#add-lang-form').parsley().reset();
}

function getCsrfToken() {
    var token = $('input[name="_csrfToken"]').val();
    return token;
}

function showEditDocModal(ele) {
    // reset modal
    $('#document-form')[0].reset();
    // set value for input
    $('#modal-submit-date').val($(ele).closest('.row-document').find('.submit_date').val()).trigger('change');
    $('#modal-note').val($(ele).closest('.row-document').find('.submit_note').val());
    $('#document-form').parsley().reset();

    var rowIdArr = $(ele).closest('.row-document').attr('id').split('-');

    $('#submit-document-btn').remove();
    $('<button type="button" class="btn btn-success" id="submit-document-btn" onclick="editDoc(' + rowIdArr[rowIdArr.length - 1] + ')">Hoàn tất</button>').insertBefore('#close-document-modal-btn');

    // show modal
    $('#document-modal').modal('toggle');
}

function editDoc(rowId) {
    var validateResult = $('#document-form').parsley().validate();
    if (validateResult) {
        $('#row-document-' + rowId).find('.submit_date').val($('#modal-submit-date').val());
        $('#row-document-' + rowId).find('.submit-date-txt').html($('#modal-submit-date').val());
        $('#row-document-' + rowId).find('.submit_note').val($('#modal-note').val());

        $('#document-modal').modal('toggle');
    }
}

function viewPresenter(presenterId) {
    var overlayId = '';
    globalViewPresenter(presenterId, overlayId);
}

function getIqScore(studentId) {
    if (initGraph) {
        return;
    }

    if (ajaxing) {
        return;
    }
    ajaxing = true;

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/students/getIqScore',
        data: {
            id: studentId
        },
        success: function (resp) {
            console.log(resp);
        },
        complete: function () {
            ajaxing = false;
        }
    });
}

function initChart(testData, chartId, title) {
    var labels = [];

    var vocabularyScores = [];
    var grammarScores = [];
    var listeningScores = [];
    var conversationScores = [];
    var totalVocScore = totalGraScore = totalLisScore = totalConScore = 0;
    var avgVoc = avgGra = avgLis = avgCon = 0;
    var countVoc = countGra = countLis = countCon = 0;

    testData.forEach(function (e) {
        labels.push(e.date);
        // datasets[0].data.push(e.score)
        if (e.vocabulary_score) {
            vocabularyScores.push(e.vocabulary_score);
            totalVocScore = totalVocScore + e.vocabulary_score;
            countVoc++;
        } else {
            vocabularyScores.push(NaN);
        }
        if (e.grammar_score) {
            grammarScores.push(e.grammar_score);
            totalGraScore = totalGraScore + e.grammar_score;
            countGra++;
        } else {
            grammarScores.push(NaN);
        }
        if (e.listening_score) {
            listeningScores.push(e.listening_score);
            totalLisScore = totalLisScore + e.listening_score;
            countLis++;
        } else {
            listeningScores.push(NaN);
        }
        if (e.conversation_score) {
            conversationScores.push(e.conversation_score);
            totalConScore = totalConScore + e.conversation_score;
            countCon++;
        } else {
            conversationScores.push(NaN);
        }
    });

    var datasets = [
        {
            label: 'Từ vựng',
            spanGaps: false,
            backgroundColor: 'rgb(54, 162, 235)',
            borderColor: 'rgb(54, 162, 235)',
            data: vocabularyScores,
            fill: false,
            pointHoverBackgroundColor: '#fff'
        },
        {
            label: 'Ngữ pháp',
            spanGaps: false,
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: grammarScores,
            fill: false,
            pointHoverBackgroundColor: '#fff'
        },
        {
            label: 'Nghe hiểu',
            spanGaps: false,
            backgroundColor: 'rgb(255, 159, 64)',
            borderColor: 'rgb(255, 159, 64)',
            data: listeningScores,
            fill: false,
            pointHoverBackgroundColor: '#fff'
        },
        {
            label: 'Đàm thoại',
            spanGaps: false,
            backgroundColor: 'rgb(75, 192, 192)',
            borderColor: 'rgb(75, 192, 192)',
            data: conversationScores,
            fill: false,
            pointHoverBackgroundColor: '#fff'
        }
    ];

    if (countVoc != 0) {
        avgVoc = totalVocScore / countVoc;
    }
    if (countGra != 0) {
        avgGra = totalGraScore / countGra;
    }
    if (countLis != 0) {
        avgLis = totalLisScore / countLis;
    }
    if (countCon != 0) {
        avgCon = totalConScore / countCon;
    }
    var radarLabel = ['Từ vựng', 'Ngữ pháp', 'Nghe hiểu', 'Đàm thoại'];
    var radarDatasets = [
        {
            label: 'Điểm trung bình',
            fill: true,
            backgroundColor: transparentize('rgb(54, 162, 235)'),
            borderColor: 'rgb(54, 162, 235)',
            pointBorderColor: "#fff",
            pointBackgroundColor: "rgba(255,99,132,1)",
            pointBorderColor: "#fff",
            data: [avgVoc, avgGra, avgLis, avgCon]
        },
    ];
    var maximum = 100;
    renderLineChart(chartId[0], title[0], labels, datasets, maximum);
    renderRadarChart(chartId[1], title[1], radarLabel, radarDatasets);
}

function transparentize(color, opacity) {
    var alpha = opacity === undefined ? 0.5 : 1 - opacity;
    return Color(color).alpha(alpha).rgbString();
}

function renderLineChart(chartId, title, labels, datasets, maximum) {
    var lineChartOptions = generateChartOptions(title, maximum);
    var config = {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: lineChartOptions
    };

    var ctx = document.getElementById(chartId).getContext('2d');
    window.myLine = new Chart(ctx, config);
}

function renderRadarChart(chartId, title, label, datasets) {
    var config = {
        type: 'radar',
        data: {
            labels: label,
            datasets: datasets
        },
        options: {
            title: {
                display: true,
                text: title
            }
        },
    };

    var ctx = document.getElementById(chartId).getContext('2d');
    window.radar = new Chart(ctx, config);
}


function viewGuild(id) {
    var overlayId = '#list-order-overlay';
    globalViewGuild(id, overlayId);
}

function viewCompany(id) {
    var overlayId = '#list-order-overlay';
    globalViewCompany(id, overlayId);
}

function reportStudent() {
    // reset modal
    $('#std-status').val(null).trigger('change');
    $('#std-presenter').val(null).trigger('change');
    $('#std-edulevel').val(null).trigger('change');
    $('#std-gender').val(null).trigger('change');

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (e) {
        if (e.checked) {
            console.log(e);
            $('input[name="' + e.name + '"]').click();
        }
    });
    // show modal
    $('#report-student-modal').modal('toggle');
}

function downloadIqChart() {
    var chartId = $('#iqtest-tabs-content').find('div.active').find('canvas')[0].id;
    downloadChart(chartId);
}

function showExportModal(studentId) {
    var source = $("#export-template").html();
    var template = Handlebars.compile(source);
    var html = template({
        'studentId': studentId
    });
    $('#export-container').html(html);
    // show modal
    $('#export-student-modal').modal('toggle');
}

function checkDuplicate() {
    var fullname = $('#fullname').val();
    if (fullname == '' || fullname == null) {
        return;
    }

    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;

    // check if current class have test or not
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/students/checkDuplicate/',
        data: {
            'q': $('#fullname').val()
        },
        success: function (resp) {
            if (resp) {
                swal({
                    title: 'Cảnh báo!',
                    text: "Học viên " + $('#fullname').val() + " đã tồn tại.",
                    type: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                });
            } else {
                swal({
                    title: 'Thông tin!',
                    text: "Học viên " + $('#fullname').val() + " chưa tồn tại.",
                    type: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                });
            }
        },
        complete: function () {
            ajaxing = false;
        }
    });
}

function showAddHealthCalendarModal() {
    // clear data
    $('#physical-form')[0].reset();
    $('#phys-result').val(null).trigger('change');
    $('#physical-form').parsley().reset();

    $('#phys-submit-btn').remove();
    $('<button type="button" class="btn btn-success" id="phys-submit-btn" onclick="addPhysCalendar()">Hoàn tất</button>').insertBefore('#close-phys-modal-btn');

    // show modal
    $('#physical-modal').modal('toggle');
}

function addPhysCalendar() {
    var validateResult = $('#physical-form').parsley().validate();
    if (validateResult) {
        var source = $("#phys-template").html();
        var template = Handlebars.compile(source);
        var html = template({
            'counter': perData.physCounter,
            'examDate': $('#phys-exam-date').val(),
            'resultTxt': $('#phys-result option:selected').html(),
            'result': $('#phys-result').val(),
            'notesRaw': $('#phys-notes').val(),
            'notes': $('#phys-notes').val().replace(/\r?\n/g, '<br />'),
        });
        perData.physCounter++;
        $('#phys-container').append(html);
        $('#physical-modal').modal('toggle');
    }
}

function showEditPhysModal(ele) {
    var row = $(ele).closest('.row-phys');
    var rowNum = $(row).attr('id').split('-')[2];
    $('#phys-exam-date').val($(row).find('.exam_date').val());
    $('#phys-result').val($(row).find('.result').val()).trigger('change');
    $('#phys-notes').val($(row).find('.notes').val());
    $('#phys-submit-btn').remove();
    $('<button type="button" class="btn btn-success" id="phys-submit-btn" onclick="editPhysCalendar(' + rowNum + ')">Hoàn tất</button>').insertBefore('#close-phys-modal-btn');
    // show modal
    $('#physical-modal').modal('toggle');
}

function editPhysCalendar(rowNum) {
    var validateResult = $('#physical-form').parsley().validate();
    if (validateResult) {
        $('#row-phys-' + rowNum).find('.exam-date-txt').html($('#phys-exam-date').val());
        $('#row-phys-' + rowNum).find('.exam_date').val($('#phys-exam-date').val());

        $('#row-phys-' + rowNum).find('.result').val($('#phys-result').val());
        $('#row-phys-' + rowNum).find('.result-txt').html($('#phys-result option:selected').html());

        $('#row-phys-' + rowNum).find('.notes').val($('#phys-notes').val());
        $('#row-phys-' + rowNum).find('.notes-txt').html($('#phys-notes').val().replace(/\r?\n/g, '<br />'));
        $('#physical-modal').modal('toggle');
    }
}

function removePhys(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa lịch khám sức khỏe',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var rowNum = $(delEl).closest('.row-phys').attr('id').split('-')[2];

                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deletePhysicalCalendar',
                    data: {
                        'calendarId': $(delEl).closest('.row-phys').find('.phys_id').val()
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deletePhysRow(delEl);
                        }
                    }
                });
            }
        });
    } else {
        deletePhysRow(delEl);
    }
}

function deletePhysRow(delEl) {
    $(delEl).closest('.row-phys').remove();
    $('#phys-container > tr').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
        $(this).find('.exam_date').attr('name', 'physical_exams[' + index + '][exam_date]');
        $(this).find('.result').attr('name', 'physical_exams[' + index + '][result]');
        $(this).find('.notes').attr('name', 'physical_exams[' + index + '][notes]');
    });
    perData.physCounter--;
}

function readURL2(input, croppedId, ratioCrop) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (/^image\/\w+$/.test(file.type)) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if ($("#avatar").hasClass('cropper-hidden')) {
                    $("#avatar").cropper('destroy');
                }
                $('#avatar').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            ratio = ratioCrop;
            croppedIdGlobal = croppedId;
            setTimeout(initCropper2, 1000);
            $('#crop-btn').removeClass('hidden');
            // $('#cropper-modal').modal('toggle');
            $('#cropper-modal').modal({ backdrop: 'static', keyboard: false });
        } else {
            window.alert('Xin hãy chọn đúng định dạng ảnh.');
        }
    } else {
        // clear input
        $('#' + croppedId).empty();
        $('input[name="' + croppedId + '"]').val('');
    }
}

function initCropper2() {
    var imgCropper = $('#avatar').cropper({
        aspectRatio: ratio,
        crop: function (e) {
            console.log(e)
        }
    });

    $('#crop-btn').click(function () {
        var imgurl = imgCropper.cropper('getCroppedCanvas').toDataURL();
        var img = document.createElement("img");

        img.addEventListener('load', function () {
            // set body height        
            var contentHeight = $('.right_col').height();
            var newHeight = contentHeight + this.height + 1;
            $('.right_col').css('min-height', newHeight);
        });

        img.src = imgurl;
        img.id = croppedIdGlobal + '-cropped';
        img.className = 'zoom-able';
        $('#' + croppedIdGlobal).empty().append(img);
        $('input[name="' + croppedIdGlobal + '"]').val(imgurl);
    });
}
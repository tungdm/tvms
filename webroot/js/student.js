var perData = {};
perData.familyCounter = 0;
perData.eduCounter = 0;
perData.expCounter = 0;
perData.langCounter = 0;
var initGraph;

var lineChartOptions = {
    responsive: true,
    title: {
        display: true,
        text: 'Biểu đồ điểm thi IQ đầu vào ngày ' + moment(iqtests[0].test_date).format('YYYY-MM-DD')
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
            }
        }]
    },
    animation: {
        onComplete: function() {
            isChartRendered = true
        }
    }
}

// init handlebars
if ($('#family-template')[0]){
    var family_template = Handlebars.compile($('#family-template').html());
}
if ($('#edu-template')[0]){
    var edu_template = Handlebars.compile($('#edu-template').html());
}
if ($('#exp-template')[0]){
    var exp_template = Handlebars.compile($('#exp-template').html());
}
if ($('#exp-template')[0]){
    var lang_template = Handlebars.compile($('#lang-template').html());
}

$(document).ready(function() {
    // init dynamic data
    perData.familyCounter = $('#family-container > tr').length;
    perData.eduCounter = $('#edu-container > tr').length;
    perData.expCounter = $('#exp-container > tr').length;
    perData.langCounter = $('#lang-container > tr').length;

    // init select2 status
    $('select[name="status"]').find('option').each(function() {
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
    for(var i=0; i < changeCheckbox.length; i++) {
        changeCheckbox[i].onchange = function() {
            console.log(this);
            if (this.checked) {
                $('input[name="'+this.name+'"]').val('1');
            } else {
                $('input[name="'+this.name+'"]').val('0');
            }
        };
    }

    $('#student-tabs').tabCollapse();

    if ($('#line-chart').length ) {
        // renderIqLineGraph();
        renderLineChart();
    }

    var focusTab = window.location.hash;
    if (focusTab) {
        $('#student-tabs a[href="' + focusTab + '"]').tab('show');
    }

    $('.select-city').change(function(e) {
        var token = getToken(this);
        if (this.value == null || this.value == '') {
            $('#addresses-'+token+'-district-id').empty().append('<option value=""></option>');
            $('#addresses-'+token+'-district-id').prop('disabled', true);
        } else {
            $.ajax({
                type: 'GET',
                url: DOMAIN_NAME + '/students/getDistrict',
                data: {
                    city: this.value
                },
                success: function(resp) {
                    var processedOptions = $.map(resp, function(obj, index) {
                        return {id: index, text: obj};
                    });
    
                    // init select2-district with response data
                    if ($('#addresses-'+token+'-district-id').hasClass('select2-hidden-accessible')) {
                        $('#addresses-'+token+'-district-id').select2('destroy').empty().append('<option value=""></option>');
                    }
    
                    // enable select2
                    $('#addresses-'+token+'-district-id').prop('disabled', false);
    
                    // reset validation
                    $('#addresses-'+token+'-district-id').parsley().reset();
                    $('#addresses-'+token+'-district-id').select2({
                        placeholder: 'Xin hãy chọn giá trị',
                        data: processedOptions,
                        allowClear: true,
                        theme: "bootstrap",
                        language: {
                            noResults: function() {
                                return "Không tìm thấy kết quả";
                            }
                        }
                    });
                }
            });
        }

        // clear select2-ward, street input data
        $('#addresses-'+token+'-ward-id').empty().append('<option value=""></option>');
        $('#addresses-'+token+'-ward-id').prop('disabled', true);
        $('#addresses-'+token+'-street').val('');
    });

    $('.select-district').change(function(e) {
        var token = getToken(this);
        // re-validate input
        $('#addresses-'+token+'-district-id').parsley().validate();
        if ($('#addresses-'+token+'-district-id').hasClass('parsley-success')) {
            $('#select2-addresses-'+token+'-district-id').removeClass('parsley-error');
        } else if ($('#addresses-'+token+'-district-id').hasClass('parsley-error')) {
            $('#select2-addresses-'+token+'-district-id').addClass('parsley-error');
        }

        if (this.value == null || this.value == '') {
            $('#addresses-'+token+'-ward-id').empty().append('<option value=""></option>');
            $('#addresses-'+token+'-ward-id').prop('disabled', true);
        } else {
            // Send ajax get ward options
            $.ajax({
                type: 'GET',
                url: DOMAIN_NAME + '/students/getWard',
                data: {
                    district: this.value
                },
                success: function(resp) {
                    var processedOptions = $.map(resp, function(obj, index) {
                        return {id: index, text: obj};
                    });

                    // init select2 with response data
                    if ($('#addresses-'+token+'-ward-id').hasClass('select2-hidden-accessible')) {
                        $('#addresses-'+token+'-ward-id').select2('destroy').empty().append('<option value=""></option>');
                    }

                    // enable select2
                    $('#addresses-'+token+'-ward-id').prop('disabled', false);

                    // reset validation
                    $('#addresses-'+token+'-ward-id').parsley().reset();
                    $('#addresses-'+token+'-ward-id').select2({
                        placeholder: 'Xin hãy chọn giá trị',
                        data: processedOptions,
                        allowClear: true,
                        theme: "bootstrap",
                        language: {
                            noResults: function() {
                                return "Không tìm thấy kết quả";
                            }
                        }
                    });
                }
            });
        }
        // clear street input data
        $('#addresses-'+token+'-street').val('');
    });

    $('.select-ward').change(function(e) {
        var token = getToken(this);
        // re-validate input
        $('#addresses-'+token+'-ward-id').parsley().validate();
        if ($('#addresses-'+token+'-ward-id').hasClass('parsley-success')) {
            $('#select2-addresses-'+token+'-ward-id').removeClass('parsley-error');
        } else if ($('#addresses-'+token+'-ward-id').hasClass('parsley-error')) {
            $('#select2-addresses-'+token+'-ward-id').addClass('parsley-error');
        }
        // clear street input data
        $('#addresses-'+token+'-street').val('');
    });

    $('select[name="is_lived_in_japan"]').change(function () {
        if ($(this).val() == 'Y') {
            // fill data
            if ($('#lived-from').val()) {
                $('#jp-from-date').val($('#lived-from').val()).trigger('change');
            }
            if ($('#lived-to').val()) {
                $('#jp-to-date').val($('#lived-to').val()).trigger('change');
            }
            // show modal
            $('#lived-japan-modal').modal('toggle');
        } else {
            // remove data
            $('.time-lived-jp').addClass('hidden');
            $('.time-lived').empty();
            $('#lived-from').val('');
            $('#lived-to').val('');
        }
    });

    $('.create-student-btn').click(function() {        
        var validateResult = $('#create-student-form').parsley().validate();

        // check parsley error exists
        for (var i=0; i < 2; i++) {
            if ($('#addresses-'+i+'-district-id').hasClass('parsley-error')) {
                $('#select2-addresses-'+i+'-district-id').addClass('parsley-error');
            }
            if ($('#addresses-'+i+'-ward-id').hasClass('parsley-error')) {
                $('#select2-addresses-'+i+'-ward-id').addClass('parsley-error');
            }
        }
        
        if (validateResult) {
            // check family not empty
            if (perData.familyCounter == 0) {
                swal({
                    title: 'Quan hệ gia đình hiện đang bỏ trống',
                    text: "Xin vui lòng kiểm tra lại!",
                    type: 'error',
                }).then((result) => {
                    $('#student-tabs a[href="#tab_content1"]').tab('show');
                })
                return;
            }
            // check education not empty
            if (perData.eduCounter == 0) {
                swal({
                    title: 'Học vấn hiện chưa có thông tin',
                    text: "Xin vui lòng kiểm tra lại!",
                    type: 'error',
                }).then((result) => {
                    $('#student-tabs a[href="#tab_content3"]').tab('show');
                })
                return;
            }
            // check experiences not empty
            if (perData.expCounter == 0) {
                swal({
                    title: 'Kinh nghiệm làm việc chưa có thông tin',
                    text: "Xin vui lòng kiểm tra lại!",
                    type: 'error',
                }).then((result) => {
                    $('#student-tabs a[href="#tab_content3"]').tab('show'); 
                })
                              
                return;
            }
            
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

            setTimeout(function() {
                $('.parsley-error')[0].focus();
            }, 500);
        }
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
        success: function(resp) {
            if (resp.status == 'success') {
                // fill data
                $('#view-candidate-name').html(resp.data.fullname);
                $('#view-candidate-gender').html(resp.gender);
                $('#view-candidate-phone').html(str2Phone(resp.data.phone));
                $('#view-candidate-appointment-date').html(resp.appointment_date);
                $('#view-candidate-birthday').html(resp.birthday);
                $('#view-candidate-address').html(resp.data.addresses[0].city.name);
                $('#view-candidate-edu-level').html(resp.edu_level);
                $('#view-candidate-note').html((resp.data.note).replace(/\r?\n/g,'<br/>'));

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
                notice.get().click(function() {
                    notice.remove();
                });
            }
        },
        complete: function() {
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
        success: function(resp) {
            if (resp.status == 'success') {
                // create hidden id for address
                $('#add-candidate-form').append('<input type="hidden" id="addresses-0-id" name="addresses[0][id]" value="'+ resp.data.addresses[0].id +'" />');
                // fill form
                $('#fullname').val(resp.data.fullname);
                $('#gender').val(resp.data.gender).trigger('change');
                $('#phone').val(resp.data.phone);
                $('input[name="appointment_date"]').val(resp.data.appointment_date).trigger('change');
                $('input[name="birthday"]').val(resp.data.birthday).trigger('change');
                $('#addresses-0-city-id').val(resp.data.addresses[0].city_id).trigger('change');
                $('#educational-level').val(resp.data.educational_level).trigger('change');
                $('#note').val(resp.data.note);

                // change modal title
                $('#add-candidate-modal').find('.modal-title').html('CẬP NHẬT LỊCH HẸN');
                // change to edit button
                $('#add-candidate-btn').remove();
                $('<button type="button" class="btn btn-success" id="add-candidate-btn" onclick="editCandidate('+resp.data.id+')">Hoàn tất</button>').insertBefore('#add-candidate-close-btn');

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
                notice.get().click(function() {
                    notice.remove();
                });
            }
        },
        complete: function() {
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
            success: function(resp){
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
                    notice.get().click(function() {
                        notice.remove();
                    });
                }
            },
            complete: function() {
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
        $('select[name="families['+perData.familyCounter+'][relationship]"]').val($('#modal-relationship').val());
        $('select[name="families['+perData.familyCounter+'][job_id]"]').val($('#modal-job').val());
        $('select[name="families['+perData.familyCounter+'][bank_name]"]').val($('#modal-bank-name').val());

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
    $('#modal-bank-num').val($(ele).closest('.row-member').find('.bank_num').val());
    $('#modal-cmnd-num').val($(ele).closest('.row-member').find('.cmnd_num').val());

    $('#modal-bank-name').val($(ele).closest('.row-member').find('.bank_name').val()).trigger('change');;
    $('#modal-bank-branch').val($(ele).closest('.row-member').find('.bank_branch').val());

    $('#modal-phone').val($(ele).closest('.row-member').find('.phone').val());
    var rowIdArr = $(ele).closest('.row-member').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-member-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-member-btn" onclick="editMember('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-modal-btn');
    
    // show modal
    $('#add-member-modal').modal('toggle');
}

function editMember(rowId) {
    //validate form
    var validateResult = $('#add-member-form').parsley().validate();
    if (validateResult) {
        var member_html = createMemberTemplate(rowId);

        $('#row-member-'+rowId).replaceWith(member_html);

        // set value for select box
        $('select[name="families['+rowId+'][relationship]"]').val($('#modal-relationship').val());
        $('select[name="families['+rowId+'][job_id]"]').val($('#modal-job').val());
        $('select[name="families['+rowId+'][bank_name]"]').val($('#modal-bank-name').val());

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
                var rowId = rowIdArr[rowIdArr.length-1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteFamilyMember',
                    data: {
                        'id': $('#member-' + rowId + '-id').find('input').val()
                    },
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function(resp){
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
        $('#member-'+hiddenId+'-id').remove();
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
        inputField[i].name = 'families[' + Math.floor(i/6) + '][' + classArr[classArr.length-1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        var classArr = selectField[i].className.split(' ');
        selectField[i].name = 'families[' + Math.floor(i/2) + '][' + classArr[classArr.length-1] + ']';
    }
}

function resetFamilyModal() {
    $('#add-member-form')[0].reset();
    $('#modal-relationship').val(null).trigger('change');
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
    var edu_html = edu_template({
        'row': counter + 1,
        'counter': counter,
        
        'fromdate': 'educations[' + counter + '][from_date]',
        'fromdateVal': $('#edu-from-date').val(),
        
        'todate': 'educations[' + counter + '][to_date]',        
        'todateVal': $('#edu-to-date').val(),

        'degree': 'educations[' + counter + '][degree]',
        'degreeText': $('#modal-edu-level option:selected').html(),
        
        'school': 'educations[' + counter + '][school]',
        'schoolVal': $('#edu-school').val(),

        'address': 'educations[' + counter + '][address]',
        'addressVal': $('#edu-address').val(),

        'specialized': 'educations[' + counter + '][specialized]',
        'specializedVal': $('#edu-specialized').val(),
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
    $('#edu-from-date').val($(ele).closest('.row-edu-his').find('.from_date').val()).trigger('change');
    $('#edu-to-date').val($(ele).closest('.row-edu-his').find('.to_date').val()).trigger('change');
    $('#modal-edu-level').val($(ele).closest('.row-edu-his').find('.degree').val()).trigger('change');
    $('#edu-school').val($(ele).closest('.row-edu-his').find('.school').val());
    $('#edu-address').val($(ele).closest('.row-edu-his').find('.address').val());
    $('#edu-specialized').val($(ele).closest('.row-edu-his').find('.specialized').val());
    var rowIdArr = $(ele).closest('.row-edu-his').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-edu-his-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-edu-his-btn" onclick="editEduHis('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-edu-modal-btn');
    
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
        $('select[name="educations['+perData.eduCounter+'][degree]"]').val($('#modal-edu-level').val());

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

        $('#row-edu-his-'+rowId).replaceWith(edu_html);

        // set value for select box
        $('select[name="educations['+rowId+'][degree]"]').val($('#modal-edu-level').val());

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
                var rowId = rowIdArr[rowIdArr.length-1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteEducations',
                    data: {
                        'id': $('#edu-his-'+rowId+'-id').find('input').val()
                    },
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function(resp){
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
        $('#edu-his-'+hiddenId+'-id').remove();
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
        inputField[i].name = 'educations[' + Math.floor(i/5) + '][' + classArr[classArr.length-1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'educations[' + i + '][' + selectField[i].id + ']';
    }    
}

function resetEduHisModal() {
    $('#add-edu-his-form')[0].reset();
    $('#modal-edu-level').val(null).trigger('change');
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
        'fromdateVal': $('#exp-from-date').val(),
        
        'todate': 'experiences[' + counter + '][to_date]',
        'todateVal': $('#exp-to-date').val(),

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
        $('select[name="experiences['+perData.expCounter+'][job_id]"]').val($('#exp-job').val());

        // close modal
        $('#add-exp-modal').modal('toggle');

        // reset form in modal
        resetExpModal();

        perData.expCounter++;
    }
}

function showEditExpModal(ele) {
    // fill data to modal form
    $('#exp-from-date').val($(ele).closest('.row-exp').find('.from_date').val()).trigger('change');
    $('#exp-to-date').val($(ele).closest('.row-exp').find('.to_date').val()).trigger('change');
    $('#exp-job').val($(ele).closest('.row-exp').find('.job_id').val()).trigger('change');
    $('#exp-company').val($(ele).closest('.row-exp').find('.company').val());
    $('#exp-company-jp').val($(ele).closest('.row-exp').find('.company_jp').val());
    $('#exp-salary').val($(ele).closest('.row-exp').find('.salary').val());
    $('#exp-address').val($(ele).closest('.row-exp').find('.address').val());
    var rowIdArr = $(ele).closest('.row-exp').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-exp-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-exp-btn" onclick="editExp('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-exp-modal-btn');
    
    // show modal
    $('#add-exp-modal').modal('toggle');
}

function editExp(rowId) {
    //validate form
    var validateResult = $('#add-exp-form').parsley().validate();
    if (validateResult) {
        var exp_html = createExpTemplate(rowId);

        $('#row-exp-'+rowId).replaceWith(exp_html);

        // set value for select box
        $('select[name="experiences['+rowId+'][job_id]"]').val($('#exp-job').val());

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
                var rowId = rowIdArr[rowIdArr.length-1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteExperience',
                    data: {
                        'id': $('#exp-'+rowId+'-id').find('input').val()
                    },
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function(resp){
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
        $('#exp-'+hiddenId+'-id').remove();
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
        inputField[i].name = 'experiences[' + Math.floor(i/5) + '][' + classArr[classArr.length-1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'experiences[' + i + '][' + selectField[i].id + ']';
    }    
}

function resetExpModal() {
    $('#add-exp-form')[0].reset();
    $('#exp-job').val(null).trigger('change');
    $('#add-exp-form').parsley().reset();
}

// Language Manager
function createLangTemplate(counter) {
    var lang_html = lang_template({
        'row': counter + 1,
        'counter': counter,

        'language': 'language_abilities[' + counter + '][lang_code]',
        'languageText': $('#lang-name option:selected').html(),

        'cert': 'language_abilities[' + counter + '][certificate]',
        'certVal': $('#lang-certificate').val(),

        'fromdate': 'language_abilities[' + counter + '][from_date]',
        'fromdateVal': $('#lang-from-date').val(),
        
        'todate': 'language_abilities[' + counter + '][to_date]',
        'todateVal': $('#lang-to-date').val(),

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
        $('select[name="language_abilities['+perData.langCounter+'][lang_code]"]').val($('#lang-name').val());

        // close modal
        $('#add-lang-modal').modal('toggle');

        // reset form in modal
        resetLangModal();

        perData.langCounter++;
    }
}

function showEditLangModal(ele) {
    // fill data to modal form
    $('#lang-from-date').val($(ele).closest('.row-lang').find('.from_date').val()).trigger('change');
    $('#lang-to-date').val($(ele).closest('.row-lang').find('.to_date').val()).trigger('change');
    $('#lang-name').val($(ele).closest('.row-lang').find('.lang_code').val()).trigger('change');
    $('#lang-certificate').val($(ele).closest('.row-lang').find('.certificate').val());
    var rowIdArr = $(ele).closest('.row-lang').attr('id').split('-');

    // replace add-btn with edit-btn
    $('#add-lang-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-lang-btn" onclick="editLang('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-lang-modal-btn');
    
    // show modal
    $('#add-lang-modal').modal('toggle');
}

function editLang(rowId) {
    //validate form
    var validateResult = $('#add-lang-form').parsley().validate();
    if (validateResult) {
        var lang_html = createLangTemplate(rowId);

        $('#row-lang-'+rowId).replaceWith(lang_html);

        // set value for select box
        $('select[name="language_abilities['+rowId+'][lang_code]"]').val($('#lang-name').val());

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
                var rowId = rowIdArr[rowIdArr.length-1];
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/students/deleteLang',
                    data: {
                        'id': $('#lang-'+rowId+'-id').find('input').val()
                    },
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function(resp){
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
        $('#lang-'+hiddenId+'-id').remove();
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
        inputField[i].name = 'language_abilities[' + Math.floor(i/5) + '][' + classArr[classArr.length-1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        selectField[i].name = 'expelanguage_abilitiesriences[' + i + '][' + selectField[i].id + ']';
    }    
}

function resetLangModal() {
    $('#add-lang-form')[0].reset();
    $('#lang-name').val(null).trigger('change');
    
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
    $('<button type="button" class="btn btn-success" id="submit-document-btn" onclick="editDoc('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-document-modal-btn');

    // show modal
    $('#document-modal').modal('toggle');
}

function editDoc(rowId) {
    var validateResult = $('#document-form').parsley().validate();
    if (validateResult) {
        $('#row-document-'+rowId).find('.submit_date').val($('#modal-submit-date').val());
        $('#row-document-'+rowId).find('.submit-date-txt').html($('#modal-submit-date').val());
        $('#row-document-'+rowId).find('.submit_note').val($('#modal-note').val());

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
        success: function(resp) {
            console.log(resp);
        },
        complete: function() {
            ajaxing = false;
        }
    });
}

function renderLineChart() {
    var config = {
        type: 'line',
        data: {
            labels  : [
                'Câu 1', 'Câu 2', 'Câu 3', 'Câu 4', 'Câu 5', 'Câu 6', 'Câu 7', 'Câu 8', 'Câu 9', 'Câu 10',
                'Câu 11', 'Câu 12', 'Câu 13', 'Câu 14', 'Câu 15', 'Câu 16', 'Câu 17', 'Câu 18', 'Câu 19', 'Câu 20',
                'Câu 21', 'Câu 22', 'Câu 23', 'Câu 24'
            ],
            datasets: [
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
                },
            ]
        },
        options: lineChartOptions
    };

    var ctx = document.getElementById('line-chart').getContext('2d');
    window.myLine = new Chart(ctx, config);
}


function viewGuild(id) {
    var overlayId = '#list-order-overlay';
    globalViewGuild(id, overlayId);
}

function viewCompany(id) {
    var overlayId = '#list-order-overlay';
    globalViewCompany(id, overlayId);
}





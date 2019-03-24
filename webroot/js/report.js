var switcher = {};
switcher.order = [];
switcher.student = [];
switcher.add = [];

$(document).ready(function () {
    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        if ($(html).hasClass('order-group')) {
            var switchery = new Switchery(html, {
                size: 'small',
            });
            switcher.order.push(switchery);
        } else if ($(html).hasClass('student-group')) {
            var switchery = new Switchery(html, {
                size: 'small',
            });
            switcher.student.push(switchery);
        } else if ($(html).hasClass('add-group')) {
            var switchery = new Switchery(html, {
                size: 'small',
                disabled: true
            });
            switcher.add.push(switchery);
        } else {
            var switchery = new Switchery(html, {
                size: 'small'
            });
        }
    });

    $('.js-switch').click(function (e) {
        if ($(this)[0].checked) {
            $(this).closest('tr').find('.filter').prop('disabled', false);
            if ($(this).hasClass('order-group')) {
                switcher.student.forEach(function (ele) {
                    ele.disable();
                });
                switcher.add.forEach(function (ele) {
                    ele.enable();
                });
            } else if ($(this).hasClass('student-group')) {
                switcher.order.forEach(function (ele) {
                    ele.disable();
                });
                switcher.add.forEach(function (ele) {
                    ele.enable();
                });
            }
        } else {
            // clear input value
            $(this).closest('tr').find('.filter').val(null).trigger('change');
            $(this).closest('tr').find('.filter').prop('disabled', true);
            if ($(this).hasClass('order-group') && checkGroup(switcher.order)) {
                switcher.student.forEach(function (ele) {
                    ele.enable();
                });
                $('.add-group').each(function() {
                    if ($(this)[0].checked) {
                        $(this).click();
                    }
                })
                switcher.add.forEach(function (ele) {
                    ele.disable();
                });
            } else if ($(this).hasClass('student-group') && checkGroup(switcher.student)) {
                switcher.order.forEach(function (ele) {
                    ele.enable();
                });
                $('.add-group').each(function() {
                    if ($(this)[0].checked) {
                        $(this).click();
                    }
                })
                switcher.add.forEach(function (ele) {
                    ele.disable();
                });
            }
        }
        
    });

    $('.export-report-btn').click(function () {
        $('#report-form')[0].submit();
    })

    $('.group-1').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
    });

    $('.group-2').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
    });

    $('.group-3').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
    });
})


function checkGroup(group) {
    for (let index = 0; index < group.length; index++) {
        const ele = group[index];
        if (ele.element.checked) {
            return false;
        }
    }
    return true;
}
var curColor = '#3a87ad'; //default
var calendar;
var currentEvent;
var editor = new Simditor({
    textarea: $('.edittextarea'),
    toolbar: ['title', 'bold', 'italic', 'underline', 'color', '|',  'alignment', 'ol', 'ul', '|', 'table', 'link', 'image']
});

$(document).ready(function() {
    init_calendar();

    // init color
    $('input[name="color"]').val(curColor);
    
    $('#title-color').css({'background-color': curColor, 'border-color': curColor});

    $('#color-chooser > li > a').click(function (e) {
        e.preventDefault();
        curColor = $(this).css('color');
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});
        // set value for input
        $('input[name="color"]').val(curColor);
    });

    $('.select2-theme').change(function(e) {
        $('#' + e.target.id).parsley().validate();

        if ($(this).val() === "2") {
            // global event
            curColor = 'red';
            // hide color chooser
            $('.color-chooser-group').addClass('hidden');
        } else {
            // only me event
            curColor = '#3a87ad'; // default color
            // show color chooser
            $('.color-chooser-group').removeClass('hidden');
        }
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});
        // set value for input
        $('input[name="color"]').val(curColor);
    });

});

function init_calendar() {
    var date = new Date(),
		d = date.getDate(),
		m = date.getMonth(),
        y = date.getFullYear(),
		started,
		categoryClass;
    var now = moment();

	calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
        },
        locale: 'vi',
        nowIndicator: true,
        now: now,
		selectable: true,
        selectHelper: true,
		editable: true,
        events: DOMAIN_NAME + '/events/getEvents',
        eventLimit: true,
        // height: "auto",
		select: function (start, end, jsEvent) {
            // reset form
            resetModal();

            // init form data
            var allDayEvent = 'false'; // false
            if (end - start == 86400000) {
                allDayEvent = 'true'; // true
            }
            // remove id
            $('input[name="id"]').remove();
            $('input[name="all_day"]').val(allDayEvent);
            $('input[name="start"]').val(start);
            $('input[name="end"]').val(end);

            // renew add-btn
            $('#submit-event-btn').remove();
            $('#delete-event-btn').remove();
            $('<button type="button" class="btn btn-success" id="submit-event-btn" onclick="addEvent()">Hoàn tất</button>').insertBefore('#close-event-modal-btn');
            
            // show modal
            $('#event-modal').modal('toggle');

			started = start;
			ended = end;
		},
		eventClick: function (calEvent, jsEvent, view) {
            // reset form
            resetModal();
            currentEvent = calEvent;

            // get event detail
            var mode = 'view';
            if (calEvent.editable) {
                // edit information
                mode = 'edit';
            }
            getEvent(calEvent.id, mode);

            // update event
            $(document).on('click', '#submit-event-btn', function() {
                if (ajaxing) {
                    // still requesting
                    return;
                }
                var validateResult = $('#event-form').parsley().validate();
                if (validateResult) {
                    ajaxing = true;
                    $('#event-modal-overlay').removeClass('hidden');

                    $.ajax({
                        type: 'POST',
                        url: DOMAIN_NAME + '/events/edit/' + currentEvent.id,
                        data: $('#event-form').serialize(),
                        success: function(resp) {
                            if (resp.status === "success") {
                                currentEvent.title = resp.title;
                                currentEvent.start = resp.start;
                                currentEvent.end = resp.end;
                                currentEvent.allDay = resp.allDay;
                                currentEvent.backgroundColor = resp.backgroundColor;
                                currentEvent.borderColor = resp.borderColor;

                                // update calendar
                                calendar.fullCalendar('updateEvent', currentEvent);
                                // hide modal
                                $('#event-modal').modal('hide');
                            }
                            
                            // show notification
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
                        }, complete: function() {
                            ajaxing = false;
                            $('#event-modal-overlay').addClass('hidden');
                        }
                    });
                }
            });

            // delte event
            $(document).on('click', '#delete-event-btn', function() {
                if (ajaxing) {
                    // still requesting
                    return;
                }

                if (!confirm("Bạn có chắc chắn muốn xóa sự kiện này?")) {
                    return;
                } else {
                    ajaxing = true;
                    $('#event-modal-overlay').removeClass('hidden');

                    $.ajax({
                        type: 'POST',
                        url: DOMAIN_NAME + '/events/delete/' + currentEvent.id,
                        success: function(resp) {
                            if (resp.status == "success") {
                                calendar.fullCalendar('removeEvents', currentEvent.id);

                                // hide modal
                                $('#event-modal').modal('hide');
                            }
                            // show notification
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
                        }, 
                        complete: function() {
                            ajaxing = false;
                            $('#event-modal-overlay').addClass('hidden');
                        }
                    });
                }
            });
            calendar.fullCalendar('unselect');            
        },
        eventDrop: function (event, delta, revertFunc) {
            if (!confirm("Bạn có chắc chắn muốn thay đổi thời gian cho sự kiện này?")) {
                revertFunc();
            } else {
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/events/editDuration/' + event.id,
                    data: {
                        start: event.start.format(),
                        end: event.end.format()
                    },
                    success: function(resp) {
                        if (resp.status === "error") {
                            revertFunc();
                        }
                        // show notification
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
                });
            }
        },
        eventResize: function (event, delta, revertFunc) {
            if (!confirm("Bạn có chắc chắn muốn thay đổi thời gian cho sự kiện này?")) {
                revertFunc();
            } else {
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/events/editDuration/' + event.id,
                    data: {
                        start: event.start.format(),
                        end: event.end.format()
                    },
                    success: function(resp) {
                        console.log('changed');
                    }
                });
            }
        }
	});
}

function addEvent() {
    if (ajaxing) {
        // still requesting
        return;
    }

    //validate form
    var validateResult = $('#event-form').parsley().validate();
    if (validateResult) {
        ajaxing = true;
        $('#event-modal-overlay').removeClass('hidden');

        $.ajax({
            type: 'POST',
            url: $('#event-form').attr('action'),
            data: $('#event-form').serialize(),
            success: function(resp){
                if (resp.status === "success") {
                    calendar.fullCalendar('renderEvent', {
                        id: resp.id,
                        title: resp.title,
                        start: resp.start,
                        end: resp.end,
                        allDay: resp.allDay,
                        backgroundColor: resp.backgroundColor,
                        borderColor: resp.borderColor,
                        editable: true
                    },
                        true // make the event "stick"
                    );
                }

                calendar.fullCalendar('unselect');
                
                // hide modal
                $('#event-modal').modal('hide');

                // show notification
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
            }, complete: function() {
                ajaxing = false;
                $('#event-modal-overlay').addClass('hidden');
            }
        });
    }
}

function getEvent(id, mode) {
    if (id) {
        if (ajaxing) {
            // still requesting
            return;
        }

        ajaxing = true;
        $.ajax({
            type: 'GET',
            url: DOMAIN_NAME + '/events/getEvent',
            data: {
                id: id
            },
            success: function(resp) {
                fillData(resp, mode);
            }, complete: function() {
                ajaxing = false;
            }
        });
    }
}

function fillData(resp, mode) {
    if (mode === "edit") {
        // fill data to form
        $('<input>').attr({
            type: 'hidden',
            id: 'event-id',
            name: 'id'
        }).appendTo('#event-form');
        $('input[name="id"]').val(resp.id);

        $('input[name="all_day"]').val(resp.all_day);
        $('input[name="start"]').val(resp.start);
        $('input[name="end"]').val(resp.end);
        $('input[name="title"]').val(resp.title);
        editor.setValue(resp.description);

        curColor = resp.backgroundColor;
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});

        $('input[name="color"]').val(curColor);

        $('select[name="scope"]').val(resp.scope).trigger('change');

        $('#submit-event-btn').remove();
        $('#delete-event-btn').remove();
        // create delete button
        $('<button type="button" class="btn btn-danger" id="delete-event-btn">Xóa</button>').insertBefore('#close-event-modal-btn');
        // renew add-btn
        $('<button type="button" class="btn btn-success" id="submit-event-btn">Lưu lại</button>').insertBefore('#close-event-modal-btn');
        //show modal
        $('#event-modal').modal('toggle');                                                          
    } else {
        var description = resp.description;
        if (resp.order) {
            var source = $("#interview-template").html();
            var template = Handlebars.compile(source);
            description = template({
                'admin': resp.admin,
                'orderName': resp.order.name,
                'guild': resp.order.company.guild.name_romaji,
                'company': resp.order.company.name_romaji,
                'job': resp.order.job.job_name,
                'work_at': resp.order.work_at,
                'skill_test': resp.order.skill_test,
                'interview_type': resp.order.interview_type,
                'candidates': resp.order.students
            });
        } else if (resp.jtest) {
            var source = $("#test-template").html();
            var template = Handlebars.compile(source);
            description = template({
                'class': resp.jtest.jclass.name,
                'lesson_from': resp.jtest.lesson_from,
                'lesson_to': resp.jtest.lesson_to,
                'skills': resp.jtest.jtest_contents,
            });
        }
        $('#event-title').html(resp.title);
        // $('#event-description').html((resp.description).replace(/\r?\n/g,'<br/>'));
        $('#event-description').html(description);
        $('#event-owner').html('- ' + resp.owner);
        
        // show modal
        $('#event-info-modal').modal('toggle');
    }
    
}

function resetModal() {
    $('#event-form')[0].reset();
    $('#scope').val(null).trigger('change');
    $('#event-form').parsley().reset();
    editor.setValue('');
}
$(function () {
    let modal = new bootstrap.Modal(document.getElementById("event-modal"), {
        backdrop: "static"
    });
    let printModal = new bootstrap.Modal(document.getElementById("print-modal"), {
        backdrop: "static"
    });
    let calendar = document.getElementById('calendar');
    let formEvent = document.getElementById("form-event");
    let btnNewEvent = $("#btn-new-event");
    let btnDeleteEvent = $("#btn-delete-event");
    let btnSaveEvent = $("#btn-save-event");
    let btnPrintEvent = $("#btn-print-event");
    let btnPrintNow = $("#print-now");
    let modalTitle = $("#modal-title");   
    let username = $("#username").val();
    let calendarObj = null;
    let selectedEvent = null;
    let newEventData = null;

    let AccomData = JSON.parse($('meta[name="data"]').attr('content'));

    function onEventClick(e) {
        formEvent.reset();
        btnDeleteEvent.show();
        btnPrintEvent.show();
        $('.form-check').show();
        modalTitle.text("Edit Accomplishment");
        modal.show();
        newEventData = null;
        selectedEvent = e.event;
        let accomID = selectedEvent.id;

        $.ajax({
            url: `/home/${accomID}/edit`,
            success: function (data) {
                let dateRequestString = data.request_date;
                let dateActedString = data.date_acted;
                let dateCompletedString = data.date_completed;
                const split_date_request = dateRequestString.split(" ");
                const split_date_acted = dateActedString.split(" ");
                const split_date_completed = dateCompletedString.split(" ");
                let request_date = split_date_request[0];
                let request_time = split_date_request[1];
                let date_acted = split_date_acted[0];
                let time_acted = split_date_acted[1];
                let date_completed = split_date_completed[0];
                let time_completed = split_date_completed[1];

                $("#control_no").val(data.control_no);
                $("#office").val(data.office);
                $("#request_date").val(request_date);
                $("#request_time").val(request_time);
                $("#date_acted").val(date_acted);
                $("#time_acted").val(time_acted);
                $("#date_completed").val(date_completed);
                $("#time_completed").val(time_completed);
                $("#problem").val(data.problem);
                $("#solution").val(data.solution);
                $("#requested_by").val(data.requested_by);
                $("#approved_by").val(data.approved_by);
                $("#category").val(data.category);

                btnDeleteEvent.attr('data-key', data.id);
                btnSaveEvent.attr('data-key', data.id);
                btnPrintEvent.attr('data-key', data.id);
            },
        });
    }

    function onDateSelect(e) {
        formEvent.reset();
        btnDeleteEvent.hide();
        btnPrintEvent.hide();
        $('.form-check').hide();
        modalTitle.text("Add New Accomplishment");
        modal.show();   
        selectedEvent = null;
        newEventData = e;
        $("#control_no").val(newEventData.dateStr + "-" + username);
        $("#request_date").val(newEventData.dateStr);
        $("#date_acted").val(newEventData.dateStr);
        $("#date_completed").val(newEventData.dateStr);
        btnSaveEvent.attr('data-key', 'null');

        const inputNames = [
            "office",
            "request_time",
            "time_acted",
            "time_completed",
            "problem",
            "solution",
            "requested_by",
            "approved_by",
        ];
        $.each(inputNames, function (index, value) {
            $(`#${value}`).removeClass("is-invalid");
        });
    }

    let viewDate;
    const savedDate = localStorage.getItem("savedDate");
    
    if (savedDate !== null) {
        viewDate = new Date(savedDate);
    }else{
        viewDate = new Date();
    }

    var t = [];
    AccomData.map((record) => {
        let dateActedString = record.date_acted;
        let dateCompletedString = record.date_completed;
        const split_date_acted = dateActedString.split(" ");
        const split_date_completed = dateCompletedString.split(" ");
        let date_acted = split_date_acted[0];
        let time_acted = split_date_acted[1];
        let date_completed = split_date_completed[0];
        let time_completed = split_date_completed[1];
        t.push({
            id: record.id,
            title: record.problem, 
            start: date_acted + "T" + time_acted, 
            end: date_completed + "T" + time_completed, 
            allday: false,
            className: "bg-primary",
        });
    })
    

    calendarObj = new FullCalendar.Calendar(calendar, {
        initialDate: viewDate,
        themeSystem: "bootstrap",
        bootstrapFontAwesome: !1,
        buttonText: {
            today: "Today",
            month: "Month",
            list: "List",
            prev: "Prev",
            next: "Next"
        },
        initialView: "dayGridMonth",
        handleWindowResize: !0,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,listMonth"
        },
        initialEvents: t,
        editable: !0,
        droppable: !0,
        selectable: !0,
        eventStartEditable: false,
        dateClick: function (e) {
            onDateSelect(e);
        },
        eventClick: function (e) {
            onEventClick(e);
        },
    });

    calendarObj.render();

    btnSaveEvent.click((e) => {
        e.preventDefault();
        if($("#btn-save-event").attr('data-key') == 'null'){
            let data = $("#form-event").serialize();
            let savedDate = $("#date_acted").val();
            localStorage.setItem('savedDate', savedDate);

            $.ajax({
                url: "/calendar-ajax",
                method: "POST",
                data: data + "&type=create",
                success: function (response) {
                    if(response.success){
                        location.reload();
                    }
                },
                error: function (response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;
                        const inputNames = [
                            "category",
                            "office",
                            "request_time",
                            "time_acted",
                            "time_completed",
                            "problem",
                            "solution",
                            "requested_by",
                            "approved_by",
                        ];
                        $.each(inputNames, function (index, value) {
                            if (errors.hasOwnProperty(value)) {
                                $(`#${value}`).addClass("is-invalid");
                                $(`#${value}-error-message`).html("");
                                $(`#${value}-error-message`).append(
                                    `${errors[value][0]}`
                                );
                            } else {
                                $(`#${value}`).removeClass("is-invalid");
                                $(`#${value}-error-message`).html("");
                            }
                        });
                    }
                },
            });
        }else{
            let accomID = $("#btn-save-event").attr('data-key');
            let data = $("#form-event").serialize();
            let savedDate = $("#date_acted").val();
            localStorage.setItem('savedDate', savedDate);

            $.ajax({
                url: "/calendar-ajax",
                method: "POST",
                data: data + "&type=edit&accomID=" + accomID,
                success: function (response) {
                    if(response.success){
                        location.reload();
                    }
                },
                error: function (response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;
                        const inputNames = [
                            "category",
                            "office",
                            "request_time",
                            "time_acted",
                            "time_completed",
                            "problem",
                            "solution",
                            "requested_by",
                            "approved_by",
                        ];
                        $.each(inputNames, function (index, value) {
                            if (errors.hasOwnProperty(value)) {
                                $(`#${value}`).addClass("is-invalid");
                                $(`#${value}-error-message`).html("");
                                $(`#${value}-error-message`).append(
                                    `${errors[value][0]}`
                                );
                            } else {
                                $(`#${value}`).removeClass("is-invalid");
                                $(`#${value}-error-message`).html("");
                            }
                        });
                    }
                },
            });
        }
        
    });

    btnDeleteEvent.click(() => {
        let dataKey = btnDeleteEvent.attr('data-key');
        
        console.log($('meta[name="csrf-token"]').attr('content'))
        swal({
            title: "Delete",
            text: "Are you sure you want to delete this Accomplishment?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            closeOnClickOutside: false,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: `/home/${dataKey}/delete`,
                    method: "DELETE",
                    success: function (response) {
                        swal(
                            "Deleted!",
                            "Successfully deleted.",
                            "success",
                            { closeOnClickOutside: false }
                        ).then((isClicked) => {
                            if (isClicked) {
                                location.reload();
                            }
                        });
                    },
                });
            }
        });
    });

    btnPrintEvent.click(() => {
        let dataKey = btnPrintEvent.attr('data-key');
        let blank = $('#blank_signatory:checked').val();
        let sign = 0;

        if(blank == 'on'){
            sign = 1;
        }else{
            sign = 0;
        }
        
        window.open(`/home/${dataKey}/print/${sign}`);
    });

    btnPrintNow.click(() => {
        let dataKey = btnPrintNow.attr('data-user');
        let from = $('#print-from').val();
        let to = $('#print-to').val();
        if(from === "" || to === ""){
            $('#print-from').addClass('is-invalid');
            $('#print-to').addClass('is-invalid');
            swal({
                title : 'Warning!',
                text : 'Please fill out required fields.',
                icon : 'warning',
                timer : 1000,
                buttons : false,
            });
        }else{
            window.open(`/home/${dataKey}/print-summary/${from}/${to}`);
        }
    });

    $('#print-summary').click(() => {
        printModal.show();
    });

});
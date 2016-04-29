<link rel="stylesheet" href="/BO/theme/gebo/lib/fullcalendar/fullcalendar_gebo.css" />
<script src="/BO/theme/gebo/lib/tiny_mce/jquery.tinymce.js"></script>
<script src="/BO/theme/gebo/lib/tiny_mce/jquery.tinymce.js"></script>

{literal}
<link rel="stylesheet" href="/BO/theme/gebo/lib/fullcalendar/fullcalendar_gebo.css" />
<style>
    #calendar .fc-sat .fc-widget-content,
    #calendar .fc-sun .fc-widget-content{
        background-color:#B6B6B6;}
    .inactiveLink {
        pointer-events: none;
        cursor: default;
    }
    .tooltipevent { /* hide and position tooltip */
        background: #333;
        background: rgba(0,0,0,.8);
        border-radius: 5px;
        top: -5px;
        color: #fff;
        content: attr(alt);
        left: 160px;
        padding: 5px 15px;
        position: absolute;
        width: 150px;
        z-index:10001;
    }
</style>
<script type="text/javascript" >
$(document).ready(function() {
    $('#calendar').children('.fc-content').children().append('<div id="calendarTrash" style="float: right; padding-top: 5px; padding-right: 5px; padding-left: 5px;"><span class="ui-icon ui-icon-trash">hello</span></div>');
    $("#sel_manager").chosen({ allow_single_deselect: false,search_contains: true  });
    $("#sel_user").chosen({ allow_single_deselect: false,search_contains: true  });
    $("#leave_type").chosen({ allow_single_deselect: false,search_contains: true  });
    loadEditors('refusemailcontent');
    loadEditors('acceptmailcontent');
    gebo_calendar.regular();
    //* resize elements on window resize
    var lastWindowHeight = $(window).height();
    var lastWindowWidth = $(window).width();
    $(window).on("debouncedresize",function() {
        if($(window).height()!=lastWindowHeight || $(window).width()!=lastWindowWidth){
            lastWindowHeight = $(window).height();
            lastWindowWidth = $(window).width();
            //* rebuild calendar
            $('#calendar').fullCalendar('render');
        }
    });
    $('#calendar').find ('td.fc-sun' ).css('background-color', '#E0DBDD');
    $('#calendar').find ('td.fc-sat' ).css('background-color', '#E0DBDD');
    $('#calendar').find ('td.fc-sun' ).css('pointer-events', 'none');
    $('#calendar').find ('td.fc-sat' ).css('pointer-events', 'none');

});

function convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}
function acceptRefuseRequest(type) {
    if(type == 'accept_pop')
    {
        var eventId = $("#eventId").val();
        var acceptmailcontent = tinyMCE.get('acceptmailcontent').getContent();
        $.ajax({
            type: "POST",
            url: '/hrm/approve-refuse-leaves',
            data: "id=" +eventId+"&accept=yes&refuse=no&mailId=131&acceptmail="+escape(acceptmailcontent)
        });
        smoke.alert('You have accepted the request');
        $('#status_'+eventId).html('approved');
        $('#Acceptmail').modal('hide');
    }
    else
    {
        var refuse_eventId = $("#refuse_eventId").val();
        var refusemailcontent = tinyMCE.get('refusemailcontent').getContent();
        $.ajax({
            type: "POST",
            url: '/hrm/approve-refuse-leaves',
            data: "id=" +refuse_eventId+"&refuse=yes&mailId=133&accept=no&refusemail="+escape(refusemailcontent)
        });
        smoke.alert('You have refused the request');
        $('#status_'+refuse_eventId).html('refused');
        $('#refusemail').modal('hide');

    }
}
//* calendar
gebo_calendar = {
    regular: function() {
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        //listens for drop event

        var calendar = $('#calendar').fullCalendar({

            header: {
                left: 'prev next',
                center: 'title,today',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                prev: '<i class="icon-chevron-left cal_prev" />',
                next: '<i class="icon-chevron-right cal_next" />'
            },
            aspectRatio:2,
            selectable: true,
            selectHelper: true,
            events: '/hrm/leaves-to-be-approved',
            /*select: function(start, end, allDay) {
             if(convert(start) == '2014-09-20') {
             alert("You cannot book on this day!");
             return false;
             // $(cell).addClass('disabled');
             }else{
             smoke.prompt("Enter Title for Leave?", function(title){
             var leave_type = $("#leave_type").val();
             var sel_manager = $("#sel_manager").val();
             if (title && leave_type != 0 && sel_manager != 0){
             calendar.fullCalendar('renderEvent',
             {
             title: title,
             start: start,
             end: end,
             allDay: allDay
             },
             true // make the event "stick"
             );
             $.ajax({
             type: "POST",
             url: '/hrm/save-leave',
             data: "title=" + title+"&start="+start+"&end="+end+"&allDay="+allDay+"&requestto="+sel_manager+"&type="+leave_type+"&save=yes"
             });

             }else{  alert('please select user and leave type');
             $('#calendar').fullCalendar('updateEvent', event);
             }
             }, {
             ok: "Yes",
             cancel: "No",
             classname: "custom-class",
             reverseButtons: true,
             value: "Please enter the Title"
             });
             }
             calendar.fullCalendar('unselect');
             },*/
            /* eventClick: function(event, element) {
             var href="/hrm/leave-pop-up?leaveId="+event.id;
             $('#leavepopup .modal-body').load(href);
             $("#leavepopup").modal();

             },*/
            eventClick: function(event, element) {
                if(event.status != 'inprocess'){
                    smoke.alert("Already this request has been "+event.status);
                    return false;
                }
                smoke.quiz("Do you want to Approve or Reject?", function(e){
                        var leave_type = $("#leave_type").val();
                        var sel_manager = $("#sel_manager").val();
                        if (e == "Accept"){
                            if (tinymce.getInstanceById('acceptmailcontent'))
                            {
                                tinymce.execCommand('mceRemoveControl', true, 'acceptmailcontent');
                                loadEditors('acceptmailcontent');
                            }
                            else if (!tinymce.getInstanceById('acceptmailcontent'))
                            {
                                loadEditors('acceptmailcontent');
                            }
                            $.ajax({
                                type: "POST",
                                url: "/hrm/getvalidmailpopup",
                                data: "leaveId=" +event.id+"&type=accept",
                                async:false,
                                success: function(result) {
                                    $("#acceptmailcontent").html(result);
                                    $("#eventId").val(event.id);
                                }
                            });
                            $('#Acceptmail').modal('show');
                            /* var href="/hrm/leave-pop-up?leaveId="+event.id;
                             $('#leavepopup .modal-body').load(href);
                             $("#leavepopup").modal();*/
                            event.borderColor = '#79B77C';
                            event.editable = false;
                            event.className = 'inactiveLink';
                            $('#calendar').fullCalendar('updateEvent', event);

                        }else if (e == "Refuse"){
                            if (tinymce.getInstanceById('refusemailcontent'))
                            {
                                tinymce.execCommand('mceRemoveControl', true, 'refusemailcontent');
                                loadEditors('refusemailcontent');
                            }
                            else if (!tinymce.getInstanceById('refusemailcontent'))
                            {
                                loadEditors('refusemailcontent');
                            }
                            $.ajax({
                                type: "POST",
                                url: "/hrm/getvalidmailpopup",
                                data: "leaveId=" +event.id+"&type=refuse",
                                async:false,
                                success: function(result) {
                                    $("#refusemailcontent").html(result);
                                    $("#refuse_eventId").val(event.id);
                                }
                            });
                            $('#refusemail').modal('show');
                            event.color = '#E57E7E';
                            event.editable = false;
                            event.className = 'inactiveLink';
                            $('#calendar').fullCalendar('updateEvent', event);
                        }else {
                            return false;
                            $('#calendar').fullCalendar('updateEvent', event);
                        }
                    },
                    {
                        button_1: "Accept",
                        button_2: "Refuse",
                        button_cancel: "Cancel"
                    });
                /*smoke.quiz("Do you want to Approve or Reject?", function(e){
                 var leave_type = $("#leave_type").val();
                 var sel_manager = $("#sel_manager").val();
                 if (e){
                 if (tinymce.getInstanceById('acceptmailcontent'))
                 {
                 tinymce.execCommand('mceRemoveControl', true, 'acceptmailcontent');
                 loadEditors('acceptmailcontent');
                 }
                 else if (!tinymce.getInstanceById('acceptmailcontent'))
                 {
                 loadEditors('acceptmailcontent');
                 }
                 $.ajax({
                 type: "POST",
                 url: "/hrm/getvalidmailpopup",
                 data: "leaveId=" +event.id+"&type=accept",
                 async:false,
                 success: function(result) {
                 $("#acceptmailcontent").html(result);
                 $("#eventId").val(event.id);
                 }
                 });
                 $('#Acceptmail').modal('show');
                 *//* var href="/hrm/leave-pop-up?leaveId="+event.id;
                 $('#leavepopup .modal-body').load(href);
                 $("#leavepopup").modal();*//*
                 event.borderColor = '#79B77C';
                 event.editable = false;
                 event.className = 'inactiveLink';
                 $('#calendar').fullCalendar('updateEvent', event);

                 }else{
                 if (tinymce.getInstanceById('refusemailcontent'))
                 {
                 tinymce.execCommand('mceRemoveControl', true, 'refusemailcontent');
                 loadEditors('refusemailcontent');
                 }
                 else if (!tinymce.getInstanceById('refusemailcontent'))
                 {
                 loadEditors('refusemailcontent');
                 }
                 $.ajax({
                 type: "POST",
                 url: "/hrm/getvalidmailpopup",
                 data: "leaveId=" +event.id+"&type=refuse",
                 async:false,
                 success: function(result) {
                 $("#refusemailcontent").html(result);
                 $("#refuse_eventId").val(event.id);
                 }
                 });
                 $('#refusemail').modal('show');
                 event.color = '#E57E7E';
                 event.editable = false;
                 event.className = 'inactiveLink';
                 $('#calendar').fullCalendar('updateEvent', event);
                 }
                 }, {
                 button_1: "A",
                 button_2: "B",
                 button_3: "C",
                 button_4: "D",
                 button_cancel: "Nothing"
                 });*/
                calendar.fullCalendar('unselect');
            },
            eventMouseover: function(calEvent,jsEvent) {
                var tooltip = '<div class="tooltipevent" >' + calEvent.tooltipcont + '</div>';
                //var tooltip = '<a class="pop_over" '="" data-content="+ calEvent.title +" data-html="true" data-original-title="Article Details" data-placement="right">test article 1</a>';
                $("body").append(tooltip);
                $(this).mouseover(function(e) {
                    $(this).css('z-index', 10000);
                    $('.tooltipevent').fadeIn('500');
                    $('.tooltipevent').fadeTo('10', 1.9);
                }).mousemove(function(e) {
                    $('.tooltipevent').css('top', e.pageY + 10);
                    $('.tooltipevent').css('left', e.pageX + 20);
                });
            },
            eventMouseout: function(calEvent, jsEvent) {
                $(this).css('z-index', 8);
                $('.tooltipevent').remove();
            },
            eventDragStop: function( event, jsEvent, ui, view ) {
                var eventId = event.id;
                $("#calendarTrash").droppable({
                    tolerance: 'pointer',
                    drop: function(event, ui) {
                        if ( dragged && ui.helper && ui.helper[0] === dragged[0] ) {
                            var event = dragged[1];
                            smoke.confirm("Do you want to delet this event?", function(answer){
                                if(answer)
                                {
                                    $.ajax({
                                        url:'/hrm/removeevent?leaveId='+eventId,
                                        //dataType: 'json',
                                        async: false,
                                        success: function()
                                        {
                                            calendar.fullCalendar('removeEvents' ,eventId);
                                        }
                                    });
                                }else{
                                    $('#calendar').fullCalendar('updateEvent', event);
                                }
                            });
                        }
                    }
                });
                // calendar.fullCalendar('removeEvents' ,eventId);
                // dragged = [ ui.helper[0], event ];
            },
            eventDragStart: function( event, jsEvent, ui, view ) {
                dragged = [ ui.helper[0], event ];
            },
            editable: true,
            // theme: false,
            eventColor: '#bcdeee'

        })
    }

};
function gridView()
{
    var userid = $("#sel_user").val();
    var viewtype = $("#viewtype").val();
    if(userid != 0){
        var target_page = "/hrm/userleaves?userid="+userid;
        $.post(target_page, function(data){
            $("#usergridview").show();
            $("#usergridview").html(data);
            $("#calendarview").hide();
        });
    }else if(viewtype == 'grid'){
        var target_page = "/hrm/userleaves?grid=yes";
        $.post(target_page, function(data){
            $("#gridview").show();
            $("#gridview").html(data);
            $("#usergridview").hide();
            $("#calendarview").hide();
        });
    }
    else if(viewtype == 'calendar'){
        $("#usergridview").hide();
        $("#calendarview").show();
        $("#gridview").hide();
    }
}

</script>
{/literal}
<div class="alert alert-info span4" >
    <div class="chat_msg_heading"><span class="chat_msg_date"></span><span class="chat_user_name"><h3>Pending Leave Requests</h3></span></div>
    <p > <span class="label label-success span">Maladie : {$statistics.slapprovalpending}</span>
        <span class="label label-warning span">Vacances : {$statistics.plapprovalpending}</span>
        <span class="label label-info span">Materit&eacute; : {$statistics.mlapprovalpending}</span>
        <span class="label label-gebo span">RDV : {$statistics.rdvapprovalpending}</span></p>
</div>
<div class="alert alert-success span5" >
    <div class="chat_msg_heading span"><span class="chat_msg_date"></span><span class="chat_user_name"><h3>View by User :</h3></span></div>
    <select name="sel_user" id="sel_user" onchange="gridView();">
        <option value="0" >S&eacute;lectionnez un user</option>
        {foreach from=$userList item=user key=uk name=users}
        <option value={$user->identifier} {if $user->identifier eq $smarty.get.sel_user} selected{/if}>{$user->login}</option>
        {/foreach}
    </select>
    <div class="form-inline pull-right">
        <label class="uni-radio">
            <input type="radio" name="viewtype" id="calendar" class="uni_style" value="calendar"  />Calender View
        </label>
        <label class="uni-radio">
            <input type="radio" name="viewtype" id="grid" class="uni_style" value="grid"  />Grid View
        </label>
    </div>
</div>
<div class="row-fluid" id="calendarview">
    <div class="span12">
        <a class="span1 pull-left btn " id="calendarTrash" data-hint="Drag & drop here to delete"><img src="/BO/theme/gebo/img/gCons/recycle-full.png" alt="" /></a>

        <h2 class="heading">Calendar</h2>
        <div id="calendar" class="fc">
        </div>
    </div>
</div>
<div class="row-fluid span" id="usegridview" style="display: none">
    <div class="row-fluid span" id="gridview" style="display: none">

    </div>
    <!--///for the group profiles popup///-->
    <div class="modal4 hide fade" id="leavepopup">
        <div class="modal-header">
            <button  class="close" onclick="closePopup('leavepopup');">&times;</button>
            <h3>Manage your Leave</h3>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
        </div>
    </div>
    <!--///for mail content show up for accecpting the profile///-->
    <div class="modal hide fade" id="Acceptmail">
        <div class="modal-header">
            <button class="close" onclick="closePopup('Acceptmail');">&times;</button>
            <h3>Accept Request</h3>
        </div>
        <div class="modal-body">
            <textarea name="acceptmailcontent" class="textarea" id="acceptmailcontent"></textarea>
        </div>
        <div class="modal-footer">
            <input id="eventId" name="eventId" type="hidden" >
            <button type="button" id="accp_comment"  name="accp_comment" class="btn btn-success"  onclick="return acceptRefuseRequest('accept_pop');" >Valider</button>
        </div>
    </div>
    <!--///for mail content show up for refusing the profile///-->
    <div class="modal hide fade" id="refusemail">
        <div class="modal-header">
            <button class="close" onclick="closePopup('refusemail');">&times;</button>
            <h3>Refuse Request</h3>
        </div>
        <div class="modal-body">
            <textarea name="refusemailcontent" class="textarea" id="refusemailcontent"></textarea>
        </div>
        <div class="modal-footer">
            <input id="refuse_eventId" name="refuse_eventId" type="hidden" >
            <button type="button" id="ref_comment"  name="ref_comment" class="btn btn-danger" onclick="return acceptRefuseRequest('refuse_pop');" >Refuser</button>
        </div>
    </div>

    <script src="/BO/theme/gebo/lib/fullcalendar/fullcalendar.min.js"></script>
    <script src="/BO/theme/gebo/lib/fullcalendar/gcal.js"></script>


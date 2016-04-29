{literal}
<script language="JavaScript" type="text/javascript" src="/BO/script/jquery.multiselect.filter.js"></script>
<script language="JavaScript" type="text/javascript" src="/BO/script/jquery.multiselect.js"></script>
<link rel="stylesheet" href="/BO/css/jquery.multiselect.filter.css" type="text/css" />
<link rel="stylesheet" href="/BO/css/jquery.multiselect.css" type="text/css" />
<link href="/BO/script/datatable/demo_page.css" rel="stylesheet" type="text/css" />
<link href="/BO/script/datatable/demo_table_jui.css" rel="stylesheet" type="text/css" />
<link href="/BO/script/datatable/TableTools_JUI.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    #example_wrapper
    {
        width:96%;
    }
        /*////for the search popup//*/
    #button { padding: .5em 1em; text-decoration: none; }
    #searchslide { width: 98%; height: auto; padding: 0.4em; position: relative; margin-top: 20px; }
    #searchslide h3 { margin: 0; padding: 0.4em; text-align: center; }
</style>

<script type="text/javascript" charset="utf-8" src="/BO/script/datatable/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8" src="/BO/script/datatable/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="/BO/script/datatable/TableTools.js"></script>
<script type="text/javascript" charset="utf-8" src="/BO/script/statscontribs.js"></script>
<script src="/BO/script/jquery-ui/development-bundle/ui/jquery.ui.slider.js"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script language="javascript">
var asInitVals = new Array();
$.cookie( 'fgpagename', 'fgstatscontribs' ) ;

$(document).ready(function() {

    $("#nationalism").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 2
    });
    $("#nationalism").multiselectfilter();

    //$("#category").multiselect();
    $("#category").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 2
    });
    $("#category").multiselectfilter();
    $("#contribquiz").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 2
    });
    $("#contribquiz").multiselectfilter();
    $("#language").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 1
    });
    $("#language").multiselectfilter();
    $("#language2").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 1
    });
    $("#language2").multiselectfilter();
    $("#aotitle").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 1
    });
    $("#aotitle").multiselectfilter();
    $("#arttitle").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 1
    });
    $("#arttitle").multiselectfilter();
    $("#contrib").multiselect({
        multiple: false,
        header: "",
        noneSelectedText: "S&eacute;lectionner",
        selectedList: 1
    });
    $("#contrib").multiselectfilter();

    /////////////save search function///////////////
    $("#savesearch").click(function(){
        $("#savesearch").hide();
        $("#subsavesearch").show();
        $("#searchname").show();
    });
    $("#subsavesearch").click(function(){

        if ($('#searchname').val() == '') {
            alert('Please Enter Search Name');
            $('#searchname').focus();
            return false;
        }
    });

    var fgrows = $.cookie($.cookie( 'fgpagename')+'rows') ? parseInt( $.cookie($.cookie( 'fgpagename')+'rows') ) : 25  ;
    var idis    =   $.cookie($.cookie( 'fgpagename')) ? ( fgrows * ( parseInt( $.cookie($.cookie( 'fgpagename')) ) - 1 ) ) : 0 ;
    var oTable = $('#example').dataTable( {

        "bJQueryUI": true,
        "iDisplayStart" : parseInt(idis),
        "sPaginationType": "full_numbers",
        "oLanguage": {
            "sSearch": "Search all columns:"
        }

    } );
    $('#emailcontribs').click( function() {
        var sData = $('input', oTable.fnGetNodes()).serialize();
        var value = sData.replace(/contribcheck/g, "usercontacts"); // value = 9:61

        //  $("#userchecks").val(value);
        //  $('#contribcheck').val(sData);
        //  alert( "The following data would have been submitted to the server: \n\n"+value );
        window.location.href = "https://admin-ep-test.edit-place.com/mails/newsletter?submenuId=ML4-SL10&selectgroup=contributor&"+value;
    } );
    $("tfoot input").keyup( function () {  //alert(this.value);
        /* Filter on the column (the index) of this element */
        oTable.fnFilter( this.value, $("tfoot input").index(this) );
    } );

    /* Setting previously used page */
    if( $.cookie($.cookie( 'fgpagename')) )
        $('#fgpage'+$.cookie($.cookie( 'fgpagename'))) . click() ;

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */
    $("tfoot input").each( function (i) {
        asInitVals[i] = this.value;
    } );

    $("tfoot input").focus( function () {
        if ( this.className == "search_init" )
        {
            this.className = "";
            this.value = "";
        }
    } );

    $("tfoot input").blur( function (i) {
        if ( this.value == "" )
        {
            this.className = "search_init";
            this.value = asInitVals[$("tfoot input").index(this)];
        }
    } );
    $('#example_length').append("<b>No. of Results : "+$('#results').val()+"<b>");
} );
//////////////user detials tooltip//////////////
$(function()
{
    var hideDelay = 50;
    var currentID;
    var hideTimer = null;
    // One instance that's reused to show info for the current person
    var container = $('#personPopupContainer');
    $('body').append(container);
    $('.personPopupTrigger').live('mouseover', function()
    {
        // format of 'rel' tag: pageid,personguid
        var settings = $(this).attr('rel').split(',');
        var userid = settings[0];
        currentID = settings[1];
        // If no guid in url rel tag, don't popup blank
        if (currentID == '')
            return;
        if (hideTimer)
            clearTimeout(hideTimer);
        var pos = $(this).offset();
        var width = $(this).width();
        container.css({
            left: (pos.left + width) + 'px',
            top: pos.top - 80 + 'px'
        });
        $('#personPopupContent').html('<img src="http://www.h2obazaar.com/h2obazaarlogin/images/loading.gif" />');
        $.ajax({
            type: 'GET',
            url: '/stats/contribtooltip',
            data: 'userId=' + userid,
            //data: 'tooltipuserid=' + userID,
            success: function(data)
            { //alert(data);
                var text = $(data).find('.personPopupResult').html();
                $('#personPopupContent').html(data);
            }
        });
        container.css('display', 'block');
    });
    $('.personPopupTrigger').live('mouseout', function()
    {
        if (hideTimer)
            clearTimeout(hideTimer);
        hideTimer = setTimeout(function()
        {
            container.css('display', 'none');
        }, hideDelay);
    });
    // Allow mouse over of details without hiding details
    $('#personPopupContainer').mouseover(function()
    {
        if (hideTimer)
            clearTimeout(hideTimer);
    });
    // Hide after mouseout
    $('#personPopupContainer').mouseout(function()
    {
        if (hideTimer)
            clearTimeout(hideTimer);
        hideTimer = setTimeout(function()
        {
            container.css('display', 'none');
        }, hideDelay);
    });

});
/////////////////////////////////////////////////////////////////////////
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
    var totalcontribs = {/literal}{$contribCount[4].never_participated};{literal}
    var neverparts = {/literal}{$contribCount[0].never_participated};{literal}
    var nevervalidated = {/literal}{$contribCount[2].never_participated};{literal}
    var neversent = {/literal}{$contribCount[1].never_participated};{literal}
    var oncevalidated = {/literal}{$contribCount[3].never_participated};{literal}
    var data = google.visualization.arrayToDataTable([
        ['Contributors', 'Count'],
        ['Total',  totalcontribs],
        ['Never Participated',  neverparts],
        ['Never Validated',  neverparts],
        ['Never Sent',  neverparts],
        ['Once Validated',  oncevalidated]
    ]);

    var options = {
        title: 'Graph Representation',
        hAxis: {title: 'Contributors', titleTextStyle: {color: 'red'}},
        width: '700'
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('slidechart'));
    chart.draw(data, options);
}
///////////edit the contributor profile
function EditContribInfo(userid)
{
    var overLay=$("#fade");
    target_page = "/ao/editusercontrib?user="+userid+"&utype=2&stats=1";
    query = $("#myForm").serialize();
    $.get(target_page, query, function(data){
        $("#fade").show();
        $("#pop-box-edit").show();
        $("#pop-box-edit").html(data);

    });
}


function getIds(id){
    $( "#"+id ).slider({
        range: "min",
        value: 0,
        min: 1,
        max: 100,
        slide: function( event, ui ) {
            $( "#percentage_"+id ).val(ui.value );
        }
    });
    $( "#percentage_"+id ).val($( "#"+id ).slider( "value" ) );
}
function getLang2Ids(id){
    $( "#"+id ).slider({
        range: "min",
        value: 0,
        min: 1,
        max: 100,
        slide: function( event, ui ) {
            $( "#perctlang_"+id ).val(ui.value );
        }
    });
    $( "#perctlang_"+id ).val($( "#"+id ).slider( "value" ) );
}
///////////adding percentages values for categories
var selectedarray = [];
function categoryList()
{
    var count = $("#category :selected").length;
    if(count > 0)
    {
        var opts=$("#category :selected").text();
        var optsvalues=$("#category :selected").val();
        var oldVal=$('#targetdiv').val();
        if($.inArray(opts, selectedarray) == -1)
        {
            var newdiv = '<div><div style="float: left;padding-left: 5px;width: 40%;">' + opts + '</div><div style="width: 100px;float: left;margin-left: 15px;" ondblclick="getIds(this.id);" id='+optsvalues+' ></div><input type="text"  style="border:0;width: 25px; font-weight:bold;margin-left: 15px;;margin-bottom: 15px;" id=percentage_'+optsvalues+' name=categ['+optsvalues+']></input></div>';
            $('#targetdiv').append(newdiv);
            $( "#"+optsvalues ).slider({
                range: "min",
                value: 0,
                min: 1,
                max: 100,
                slide: function( event, ui ) {
                    $( "#percentage_"+optsvalues ).val( ui.value );
                }
            });
            $( "#percentage_"+optsvalues ).val($( "#"+optsvalues ).slider( "value" ) );
        }
        selectedarray.push(opts);
        $('#targetdiv').show();

    }
}
///////////adding percentages values for languages 2////////
var selectedarray = [];
function language2List()
{
    var count = $("#language2 :selected").length;
    if(count > 0)
    {
        var opts=$("#language2 :selected").text();
        var optsvalues=$("#language2 :selected").val();
        var oldVal=$('#targetdivlang').val();
        if($.inArray(opts, selectedarray) == -1)
        {
            var newdiv = '<div><div style="float: left;padding-left: 5px;width: 40%;">' + opts + '</div><div style="width: 100px;float: left;margin-left: 15px;" ondblclick="getLang2Ids(this.id);" id='+optsvalues+' ></div><input type="text"  style="border:0;width: 25px; font-weight:bold;margin-left: 15px;;margin-bottom: 15px;" id=perctlang_'+optsvalues+' name=language2['+optsvalues+']></input></div>';
            $('#targetdivlang').append(newdiv);
            $( "#"+optsvalues ).slider({
                range: "min",
                value: 0,
                min: 1,
                max: 100,
                slide: function( event, ui ) {
                    $( "#perctlang_"+optsvalues ).val( ui.value );
                }
            });
            $( "#perctlang_"+optsvalues ).val($( "#"+optsvalues ).slider( "value" ) );
        }
        selectedarray.push(opts);
        $('#targetdivlang').show();

    }
}
function compareContribs(fun)
{
    if(fun == 1)
    {
        var checked = $("input[type=checkbox]:checked").length;
        if ((checked == 3) ||  (checked == 2)) {
            var contribids = [];
            $(':checkbox:checked').each(function(i){
                contribids[i] = $(this).val();
            });
            target_page = "/stats/savesearch?compare=yes&contribids="+contribids;
            window.open(target_page,'_blank');
            //$("#comparediv").show();
            return true;
        }
        else
        {
            alert("select contributors");
            return false;
        }
    }
    else
    {
        var checked = $("input[type=checkbox]:checked").length;
        if (checked == 0) {
            alert("Please select atleast one");
            return false;
        }
        else
            return true;
    }
}
//// function to load quiz list
function fnloadquiz()
{     alert("hello");
    var cat=$('#category').val();
    $.ajax({
        url: "/stats/listcatquiz?cat="+cat,
        global: false,
        type: "GET",
        dataType: "html",
        async:false,
        success: function(msg){ alert(msg);
            $('#quiz_preload').html(msg);
        }
    });
}
//full.campaign_id is js variable should be sent as parameter
</script>
<script type="text/javascript" charset="utf-8" src="/BO/script/datatable/pagntnfns.js"></script>
{/literal}

<div class="topbox">
    <div style="width:100%">
		  <span class="pagehead"><samp id="642">{$nodes[642]}</samp><img align="right" id="showchart" onclick="slidediv();" src="/image/picto/chart.png">
<!--        <a href="/stats/searchstatscontributors?submenuId={$submenuId}" style="float:right"><img border="0" src="/image/picto/search_button.gif" id="contrib_stats_search"> </a>-->
            <a href="#" style="float:right"><img border="0" src="/image/picto/search_button.gif" id="contrib_stats_search"> </a>
          </span>
        <div id="slidechart"></div>

    </div>
    <div class="message">{$actionmessages[0]}</div>

    <div id="imp_stats" style="width: 95%;" >
        <table>
            <tr>
                <td><samp id="746">{$nodes[746]}</samp> : <a href="/stats/statscontribs?submenuId=ML5-SL7&total_contribs=yes">{$contribCount[3].never_participated}</a> | </td>
                <td><samp id="643">{$nodes[643]}</samp> : <a href="/stats/statscontribs?submenuId=ML5-SL7&never_participated=yes">{$contribCount[0].never_participated}</a> | </td>
                <td><samp id="644">{$nodes[644]}</samp> : <a href="/stats/statscontribs?submenuId=ML5-SL7&never_validated=yes">{$contribCount[2].never_participated}</a> | </td>
                <td><samp id="645">{$nodes[645]}</samp>  : <a href="/stats/statscontribs?submenuId=ML5-SL7&never_sent=yes">{$contribCount[1].never_participated}</a> | </td>
                <td><samp id="646">{$nodes[646]}</samp> : <a href="/stats/statscontribs?submenuId=ML5-SL7&once_validated=yes">{$contribCount[4].never_participated}</a> | </td>
                <td>Published : <a href="/stats/statscontribs?submenuId=ML5-SL7&once_published=yes">{$contribCount[5].never_participated}</a></td>
            </tr>
        </table>
    </div>
</div>

<form action="/stats/savesearch?submenuId={$submenuId}" method="post" id="searchcontrib" name="searchcontrib" >

    <table class="display" id="example">
        <thead>
        <tr>

            <th>S.No</th>
            <th>Name</th>
            <th>Joined Date</th>
            <!--				  <th>validated Articles</th>-->
            <th>category</th>
            <th>Mother Tongue</th>
            <th>Type</th>
        </tr>
        </thead>
        <tfoot>
        <tr>

            <th><input type="text" name="search_si" value="Search SI" class="search_init"/></th>
            <th><input type="text" name="search_name" value="search_name title" class="search_init"/></th>
            <th><input type="text" name="search_type" value="Search Date" class="search_init"/></th>
            <!--					<th><input type="text" name="search_status" value="Search validated" class="search_init"/></th>-->
            <th><input type="text" name="search_date" value="Search category" class="search_init"/></th>
            <th><input type="text" name="mother_tongue" value="mother_tongue category" class="search_init"/></th>
            <th><input type="text" name="search_black" value="Search type" class="search_init"/></th>

        </tr>
        </tfoot>

        <tbody><div style="display: none"><input type="text" id="results" name="results" value="{$contributors|@count}"/></div>
        {if $contributors neq 'NO'}
        {foreach item=user from=$contributors name=usergrid}
        <tr>
            <td><input type="checkbox" id="contribcheck" name="contribcheck[]" value="{$user.identifier}" />{$smarty.foreach.usergrid.index+1}</td>
            <td>
                <a href="javascript:void(0);" onClick="connect_fo('client', '{$user.email}', '{$user.password}');"><img alt="Connect to FO" title="Connect to FO" src="http://www.kuleuven.rare3.eu/images/design/login.png"></a>&nbsp;<a class="personPopupTrigger" rel="{$user.identifier}" style="cursor: pointer" onclick="EditContribInfo({$user.identifier});">{$user.email}({$user.first_name},{$user.last_name})</a>
            </td>
            <td>{$user.created_at|date_format:"%d/%m/%Y"}</td>
            <td>{$user.favourite_category}</td>
            <td>{$user.mother_tongue}</td>
            <td>{if $user.profile_type eq "junior"}Junior{else}Senior{/if}</td>

        </tr>
        {/foreach}
        {/if}
        <tbody>
    </table>
    <div class="spacer"></div>
    {assign var="urlstring" value=$smarty.server.REQUEST_URI}
    {assign var=pageurl value=$urlstring|explode:"&"}
    {if $smarty.get.searchsubmit}
    {assign var="urlstring1" value=$urlstring|replace:$pageurl[11]:"download=download"}
    {else}
    {assign var="urlstring1" value="/stats/contributors?submenuId=ML5-SL6&download=download"}
    {/if}
    {if $contributors neq 'NO'}
    <a href="{$urlstring1}"> <samp id="668"> <input type="button" value="{$nodes[668]}" name="download" id="download" class="buttonbg"></samp></a>
    <!--<input type="text" value="" name="userchecks" id="userchecks" >-->
    <!--  <input type="button" value="Compare" name="compare" id="compare" class="buttonbg" onclick="return compareContribs(1);">-->
    <input type="button" value="Email" name="emailcontribs" id="emailcontribs" class="buttonbg" onclick="return compareContribs(2);">
    {/if}
    <div style="float: left;padding-right: 15px;"> <input type="button" value="SaveSearch" name="savesearch" id="savesearch" class="buttonbg">
        <input type="text" value="" name="searchname" id="searchname" style="display: none;">
        <input type="submit" value="Go" name="subsavesearch" id="subsavesearch" onclick="return valSaveSearch();" class="buttonbg" style="display: none;">
    </div>
</form>
<!-- ///////////user details tool tip////////////////////////////////////////////////////////////////-->
<div id="personPopupContainer">
    <table width="" border="0" cellspacing="0" cellpadding="0" align="center" class="personPopupPopup" width="auto">
        <tr>
            <td class="corner topLeft"></td>
            <td class="toptool"></td>
            <td class="corner topRight"></td>
        </tr>
        <tr>
            <td class="left">&nbsp;</td>
            <td><div id="personPopupContent"></div></td>
            <td class="right">&nbsp;</td>
        </tr>
        <tr>
            <td class="corner bottomLeft">&nbsp;</td>
            <td class="bottomtool">&nbsp;</td>
            <td class="corner bottomRight"></td>
        </tr>
    </table>
</div>
<!--/////////pop up for the editing the contributor detials//////////////////////////////////////-->
<div id="fade" class="black_overlay" style="height: 800px;display:none"></div>
<div  id="pop-box-edit" class="box" style="display:none;"></div>

<!--////////////////for search slid down popup in this page/////////-->

<form action="/stats/statscontribs?submenuId={$submenuId}" method="get" id="searchstatscontribs" name="searchstatscontribs" >
    <div id="searchslide" class="ui-widget-content ui-corner-all" style="display: none;">
        <h3 class="ui-widget-header ui-corner-all">Contributor Search</h3>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>   <input type="hidden" id="submenuId"  name="submenuId" value="ML5-SL7"  />
                <td width="83%" height="205"><table width="100%" height="159" border="0" cellpadding="0" cellspacing="0">
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="655">{$nodes[655]}</samp> :</b></td>
                        <td colspan="2"><input type="text" id="start_date" name="start_date" value="{$smarty.get.start_date}" style="padding: 4px;vertical-align: middle;width: 132px !important;" />
                            to
                            <input type="text" id="end_date" name="end_date"  value="{$smarty.get.end_date}" style="padding: 4px;vertical-align: middle;width: 132px !important;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="656">{$nodes[656]}</samp> :</b></td>
                        <td colspan="2"><input type="text" id="activity_start_date" name="activity_start_date" value="{$smarty.get.activity_start_date}" style="padding: 4px;vertical-align: middle;width: 132px !important;" />
                            to
                            <input type="text" id="activity_end_date" name="activity_end_date"  value="{$smarty.get.activity_end_date}" style="padding: 4px;vertical-align: middle;width: 132px !important;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>

                    <tr>
                        <td > <b><samp id="658">{$nodes[658]}</samp> :</b></td>
                        <td>
                            <input type="radio" name="type" id="junior" value="junior" {if $smarty.get.type=='junior'}  checked="checked" {/if} /> Junior
                            <input type="radio" name="type" id="senior" value="senior" {if $smarty.get.type=='senior'}  checked="checked" {/if} /> Senior
                            <input type="radio" name="type" id="both" value="0"  {if $smarty.get.type=='0' || $smarty.get.type==''}  checked="checked" {/if} /> Both
                        </td>
                        <td><b> <samp id="660">{$nodes[660]}</samp> : </b></td>
                        <td>
                            <input type="radio" name="blacklist" id="yes" value="yes" {if $smarty.get.blacklist=='yes'}  checked="checked" {/if} /> Yes
                            <input type="radio" name="blacklist" id="no" value="no" {if $smarty.get.blacklist=='no'}  checked="checked" {/if} /> No
                            <input type="radio" name="blacklist" id="blacklist" value="0" {if $smarty.get.blacklist=='0' || $smarty.get.blacklist==''}  checked="checked" {/if}  /> Ignore
                        </td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="659">{$nodes[659]}</samp> : </b></td>
                        <td><input type="radio" name="status" id="status_yes" value="active" {if $smarty.get.status=='active'}  checked="checked" {/if} /> Active
                            <input type="radio" name="status" id="status_no" value="inactive" {if $smarty.get.status=='inactive'}  checked="checked" {/if} /> Inactive
                            <input type="radio" name="status" id="status_both" value="0" {if $smarty.get.status=='0' || $smarty.get.status==''}  checked="checked" {/if} /> Both
                        </td>
                        <td><b><samp id="662">{$nodes[662]}</samp> :</b></td>
                        <td><input id="minage" name="minage" type="text" value="{$smarty.get.minage}" style="width: 50px;" />
                            to <input id="maxage" name="maxage" type="text" value="{$smarty.get.maxage}" style="width: 50px;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="657">{$nodes[657]}</samp> : </b></td>
                        <td>{html_options  name=contrib id=contrib options=$contrib_list style="width: 200px;" selected=$smarty.get.contrib }</td>
                        <td><b><samp id="664">{$nodes[664]}</samp> :</b></td>
                        <td><input type="text" id="min_arts_validated" name="min_arts_validated" value="{$smarty.get.min_arts_validated}" style="width: 50px;" />
                            to
                            <input type="text" id="max_arts_validated" name="max_arts_validated"  value="{$smarty.get.max_arts_validated}" style="width: 50px;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="661">{$nodes[661]}</samp> : </b></td>
                        <td>{html_options  name=nationalism id=nationalism options=$nationality_array selected=$smarty.get.nationalism  style="width: 200px;"}</td>
                        <td><b>Total Participations :</b></td>
                        <td ><input type="text" id="min_total_parts" name="min_total_parts" value="{$smarty.get.min_total_parts}"  style="width: 50px;"/>
                            to
                            <input type="text" id="max_total_parts" name="max_total_parts"  value="{$smarty.get.max_total_parts}" style="width: 50px;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="666">{$nodes[666]}</samp> : </b></td>
                        <td>{html_options  id=language name=language options=$languages_array selected=$smarty.get.language  style="width: 200px;"}</td>
                        <td><b>Articles Sent :</b></td>
                        <td ><input type="text" id="min_arts_sent" name="min_arts_sent" value="{$smarty.get.min_arts_sent}" style="width: 50px;"/>
                            to
                            <input type="text" id="max_arts_sent" name="max_arts_sent"  value="{$smarty.get.max_arts_sent}" style="width: 50px;" /></td>
                    </tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td><b>Language 2 : </b></td>
                        <td>{html_options  id=language2 name=language2 options=$languages_array selected=$smarty.get.language2  style="width: 200px;" onchange="language2List();"}</td>
                        <td><b>Participations Refused :</b></td>
                        <td ><input type="text" id="min_parts_refused" name="min_parts_refused" value="{$smarty.get.min_parts_refused}" style="width: 50px;" />
                            to
                            <input type="text" id="max_parts_refused" name="max_parts_refused"  value="{$smarty.get.max_parts_refused}" style="width: 50px;" /></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td colspan="3"><div style="width: 500px;height: auto;border:solid 1px #60676a;-moz-border-radius: 3px;display: none;" id="targetdivlang">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td> <b><samp id="663">{$nodes[663]}</samp> :</b></td>
                        <td>{html_options  name=aotitle id=aotitle options=$aoList onChange=fnloadao(); selected=$smarty.get.aotitle  style="width: 200px;"}</td>
                        <td><b>Articles Refused :</b></td>
                        <td ><input type="text" id="min_arts_refused" name="min_arts_refused" value="{$smarty.get.min_arts_refused}" style="width: 50px;"/>
                            to
                            <input type="text" id="max_arts_refused" name="max_arts_refused"  value="{$smarty.get.max_arts_refused}"  style="width: 50px;"/></td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td><b><samp id="665">{$nodes[665]}</samp>  :</b></td>
                        <td><select  multiple="multiple"  id="category" name="category[]"  class="text-field-very-big" onchange="categoryList();">
                            {html_options  id=category options=$categories_array selected=$category onchange=fnloadquiz(); style="width: 200px;"}
                        </select></td>
                        <td><b>Number of disapprovals :</b></td>
                        <td><input type="text" id="noof_disapproved" name="noof_disapproved" value="{$smarty.get.noof_disapproved}" />
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td colspan="3"><div style="width: 500px;height: auto;border:solid 1px #60676a;-moz-border-radius: 3px;display: none;" id="targetdiv">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td><b>Quiz  :</b></td>
                        <td> <span id="quiz_load"> </span>
                        <span id="quiz_preload">
                        <select  multiple="multiple"  id="contribquiz" name="contribquiz"  class="text-field-very-big">
                            {html_options  id=contribquiz options=$quizlist selected=$contribquiz  style="width: 200px;"}
                        </select>  </span>
                        </td>
                        <td><b>Corrector  :</b></td>
                        <td>
                            <input type="radio" name="type2" id="corrector" value="corrector" {if $smarty.get.type2=='corrector'}  checked="checked" {/if} /> Yes
                            <input type="radio" name="type2" id="writer" value="writer" {if $smarty.get.type2=='writer'}  checked="checked" {/if} /> No
                            <input type="radio" name="type2" id="all" value="0"  {if $smarty.get.type2=='0' || $smarty.get.type2==''}  checked="checked" {/if} /> All
                        </td>

                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <div style="text-align: center;padding-top: 50px;margin-top: 50px;">
                                <samp id="667"> <input type="submit" value="{$nodes[667]}" name="searchsubmit" onclick="return valSearchContribs();" class="buttonbg"></samp>
                                <samp id="668"> <input type="submit" value="{$nodes[668]}" name="download" onclick="return valSearchContribs();" class="buttonbg"></samp>
                            </div>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table></td>
                <td width="17%">
                    <div style="padding-bottom:270px;">
                        <fieldset style="color: #ff0000;border: solid;">
                            <legend style="color: #ff0000;">SAVED SEARCH LIST</legend>
                            <div style="padding-left: 10px;">
                                {if $savedSearchesUrls neq 'NO'}
                                {foreach item=urls from=$savedSearchesUrls name=urlslist}
                                <li><a href="{$urls.url}" >{$urls.search_name|wordwrap:10}</a><a href="/stats/deletesearch?submenuId={$submenuId}&searchId={$urls.id}">
                                    <img width="16" height="16" border="0" alt="%Delete_array%" title="%Delete_array%" src="/image/delete.gif"></a></li>
                                {/foreach}
                                {else}
                                No Saved Searches
                                {/if}
                            </div>
                        </fieldset>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</form>


<div style="visibility:hidden" >
    <form id="user_login_form" name="user_login_form" method="post" action="" target="_blank">
        <input type="text" id="login_name" name="login_name" value="">
        <input type="password" id="login_password" name="login_password" value="{$user_detail[0].password}" >
        <input type="submit" value="Login" />
    </form>
</div>

{literal}
<script type="text/javascript" >
    function connect_fo(user,email,pwd)
    {
        document.forms["user_login_form"].action="http://mmm-new.edit-place.com/index/userfologin";
        $('#login_name').val(email);
        $('#login_password').val(pwd);
        document.forms["user_login_form"].submit();
    }
</script>
{/literal}



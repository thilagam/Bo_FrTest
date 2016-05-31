//glocal array ti save the file names//
var export_files = '';
var index = 0;
var n=0;
function filter_search(){
    $("#ajax_load").html('Loading....');
    console.log('ajax initiated');
    var form_data = $("#main_form").serialize()+'&'+$("#filter_form").serialize();
    $("#copify").val('');
    $("#copify").val(window.location.hostname+'/portfolio/search-portfolio?'+form_data);
    $.ajax({
        type: 'POST',
        url: '/portfolio/search-portfolio-on-filter?'+form_data,
        success: function (data) {
            console.log(data);
            $("#ajax_load").html(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}
function main_filter_search(){
    $("#ajax_load").html('Loading....');
    console.log('ajax initiated');
    var form_data = $("#main_form").serialize();
    $("#copify").val('');
    $("#copify").val(window.location.hostname+'/portfolio/search-portfolio?'+form_data);
    $.ajax({
        type: 'POST',
        url: '/portfolio/search-portfolio-on-filter?'+form_data,
        success: function (data) {
            console.log(data);
            $("#ajax_load").html(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}
function export_multi_portfoilio(){
    $('#exportStatus').modal('show');setTimeout(function(){$(".modal-backdrop").remove();},500);
    $("#modal_download_zip").html('We\'re exportig your portfolio. Please wait...');
    setTimeout(function(){
        index = 0;
        n = 0;
        export_files = '';
        call_export_ajax();
    },500);
}
function call_export_ajax(){
    console.log('#contributors_' + n);
    if($('#contributors_' + n).length == 0) {
//            $("#download_zip").hide( "slide" );
        //after all checkboxes checked this block will execute at last.
        $("#modal_download_zip").append('<br>Zipping exported images,Please wait...');
        $.ajax({
            type: 'POST',
            async: false,
            url: '/portfolio/create-zip-for-export?export_files=' + export_files,
            success: function (data) {
                console.log(data);
                $("#modal_download_zip").append(data);
                $("#downloadZipLink")[0].click();//autotriger click//
                $("#modal_download_zip").append('');//clear the status bar//
//                    $("#download_zip").show( "slide" );
            }
        });
        $("#ajax_download_img").html('');
        console.log(export_files);
        return false;
    }
    else{
        //var form_data = $("#ajax_load").serialize();
        //console.log($('#contributors_' + n).is(':checked'));
        if ($('#contributors_' + n).is(':checked')) {
            var id = $('#contributors_' + n).val();
            //console.log(id);
            index++;
            $("#modal_download_zip").append('<br>image <span  class="failed">'+index+'</span> export is in progress,Please wait...');
            $.ajax({
                type: 'POST',
                async: false,
                url: '/portfolio/export-multi-portfolio?id=' + id,
                success: function (data) {
                    //console.log(data);
                    $("#ajax_download_img").html(data);
                }
            });
        }//if
        else {
            n++;
            call_export_ajax(n);

        }
    }
    //donwload link after all the images are created//
}
function toggle_download(){
    var flag = false;
    $("#ajax_load input[type=checkbox]").each(function(){
        if(this.checked)
            flag = true;
    });
    if(flag)
        $("#stick").show( );
    else {
        $("#stick").hide();
        $("#download_zip").html('');
    }
}
$(document).ready(function(){
//        $("#langauge").multiselect().multiselectfilter();
    $("#langauge").chosen({ allow_single_deselect: true,search_contains: true });
    $("#type").chosen({ allow_single_deselect: true,search_contains: true });
    $("#status").chosen({ allow_single_deselect: true,search_contains: true });
    $("#language_more").chosen({ allow_single_deselect: true,search_contains: true });
    $("#favcontribcheck").chosen({ allow_single_deselect: true,search_contains: true });
    $("#client_list").chosen({ allow_single_deselect: true,search_contains: true });
    $("#category_list").chosen({ allow_single_deselect: true,search_contains: true });
    $(" .chosen-container-multi").css('width','90%');
    //to load the link in share input box//
    var form_data = $("#main_form").serialize()+'&'+$("#filter_form").serialize();
    $("#copify").val(window.location.hostname+'/portfolio/search-portfolio?'+form_data);
    //by default hide the link//
    $("#copify").toggle();
    //to limit the number of checkbox select//
    var limit = 5;
    $('input[type=checkbox]').change(function(e){
        if ($('input[type=checkbox]:checked').length > limit) {
            $(this).prop('checked', false)
            alert("allowed only 5");
        }
    })

});

function admSelectCheck(){
    var selected_text = $("#type option:selected").text();
    console.log(selected_text.search(/translator/i));
    if(selected_text.search(/translator/i) >= 0 || selected_text.search(/Translator/i) >= 0){
        $(".dest_lang").removeClass('hide');
    }
    else{
        $(".dest_lang").addClass('hide');
    }
}
function copify() {
    $("#copify").toggle();
}
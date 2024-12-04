{{--layouts.front it's conflict some design pattern --}}
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Preview Form</title>
    <link rel="stylesheet" href="/assets/stylesheets/styles.css" media="all"/>
    <link rel="stylesheet" href="/custom/css/custom.css" media="all"/>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script language="javascript"> var jQuery = jQuery.noConflict(true);</script>
</head>
<body>
<div align="right">
    <input type="button" value="&nbsp;&nbsp;&nbsp; Close &nbsp; &nbsp;&nbsp;" align="right" onClick="CloseMe()" id="closeBtn"  class="btn-submit-1 btn btn-danger" style="position: fixed;right: 0;z-index:999;"/>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div id="previewDiv"></div>
        </div>
    </div>
</div>

<div align="center">
    <input type="button" style="font-size: 18px;"value="Go Back" id="backBtn" onclick="CloseMe()" class="btn-submit-1 btn btn-danger" />
    {{--<input name="actionBtn" type="button"  style="font-size: 18px;"value="Submit" id="submitFromPreviewBtn" onclick="" class="btn-submit-1 btn btn-primary" />--}}
</div>
</body>
</html>
<script language="javascript">
    function commaSeparateNumber(val){
        while (/(\d+)(\d{3})/.test(val.toString())){
            val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
        }
        return val;
    }

    // jQuery(function () {
    //     jQuery('#submitFromPreviewBtn').click(function (e) {
    //         window.opener.document.getElementById("IrcRecommendationSecondAdhocForm").setAttribute("target", "_self");
    //         window.opener.jQuery("#IrcRecommendationSecondAdhocForm").submit();
    //         window.close();
    //     });
    // });

    window.opener.jQuery('select').each(function (index) {
        var text = jQuery(this).find('option:selected').text();
        var id = jQuery(this).attr("id");
        var val = jQuery(this).val();
        jQuery(this).find('option:selected').replaceWith("<option value='" + val + "' selected>" + text + "</option>");
    });

    window.opener.jQuery("#inputForm :input[type=text]").each(function (index) {
        jQuery(this).attr("value", jQuery(this).val());
    });

    window.opener.jQuery("#inputForm :input[type=number]").each(function (index) {
        jQuery(this).attr("value", jQuery(this).val());
    });

    window.opener.jQuery("textarea").each(function (index) {
        jQuery(this).text(jQuery(this).val());
    });

    window.opener.jQuery("#inputForm :input[type=email]").each(function (index) {
        jQuery(this).attr("value", jQuery(this).val());
    });


    window.opener.jQuery("#inputForm :input[type=radio]").each(function (index) {
        if(jQuery(this).is(':checked')){
            jQuery(this).attr("checked","checked");
        }
    });

    window.opener.jQuery("#inputForm :input[type=checkbox]").each(function (index) {
        if(jQuery(this).is(':checked')){
            jQuery(this).attr("checked","checked");
        }
    });

    window.opener.jQuery('.documentUrl').each(function () {
        var href = jQuery(this).attr('href');
        jQuery(this).replaceWith("<a href='" + href + "'></a>");
    })

    // window.opener.jQuery("select").css({
    //     "border": "none",
    //     "background": "#fff",
    //     "pointer-events": "none",
    //     "box-shadow": "none",
    //     "-webkit-appearance": "none",
    //     "-moz-appearance": "none",
    //     "appearance": "none"
    // });

    window.opener.jQuery("fieldset").css({"display": "block"});
    // window.opener.jQuery("#full_same_as_authorized").css({"display": "none"});
    window.opener.jQuery(".actions").css({"display": "none"});
    window.opener.jQuery(".steps").css({"display": "none"});
    window.opener.jQuery(".draft").css({"display": "none"});
    window.opener.jQuery(".title ").css({"display": "none"});
    // window.opener.jQuery("select").prop('disabled', true);

    document.getElementById("previewDiv").innerHTML = window.opener.document.getElementById("inputForm").innerHTML;

    //   JavaScript Document
    function printThis(ob) {
        print();
    }
    jQuery('#showPreview').remove();
    jQuery('#save_btn').remove();
    jQuery('#save_draft_btn').remove();
    jQuery('.stepHeader,.calender-icon,.pss-error').remove();
    jQuery('.required-star').removeClass('required-star');
    jQuery('input[type=hidden]').remove();
    jQuery('.panel-orange > .panel-heading').css('margin-bottom', '10px');
    jQuery('.input-group-addon').css({"visibility": "hidden"});
    jQuery('.hiddenDiv').css({"visibility": "hidden"});
    jQuery('.img-user').css({"width": "100px"});
    jQuery('#invalidInst').html('');
    //    jQuery("#docTabs").tab('show');
    jQuery('#previewDiv .btn').not('.showInPreview').each(function () {
        jQuery(this).replaceWith("");
    });

    jQuery('#IrcRecommendationSecondAdhocForm :input').attr('disabled', true);

    jQuery('#previewDiv').find('input:not([type=radio], [type="checkbox"], [type=hidden], [type=file], [name=accept_terms]), textarea').each(function (i, v)
    {
        var allClass = jQuery(this).attr('class');
        if (allClass.match("onlyNumber")) {
            if (allClass.match("nocomma")) {
                var thisVal = this.value;
            }
            else {
                var thisVal = commaSeparateNumber(this.value);
            }
        } else {
            var thisVal = this.value;
        }
        jQuery(this).replaceWith('<span style="line-height: 16px; padding: 6px 12px; border:1px solid #ccc; display: block; background-color: #eee; border-radius: 4px; min-height: 30px">' + thisVal + '</span>');
    });

    jQuery('#previewDiv').find('input[type=file]').each(function ()
    {
        jQuery(this).replaceWith("<span>" + this.value + "</span>");
    });


    jQuery('#previewDiv #accept_terms').attr("onclick", 'return false').attr("disabled", true);
    jQuery('#previewDiv').find('input[type=radio]').each(function ()
    {
        jQuery(this).attr('disabled', 'disabled');
    });

    // replace select option
    jQuery("select").replaceWith(function ()
    {
        var text = jQuery(this).find('option:selected').text();
        jQuery(this).replaceWith('<span style="line-height: 16px; padding: 6px 12px; border:1px solid #ccc; display: block; background-color: #eee; border-radius: 4px; min-height: 30px">' + text + '</span>');
    });

    jQuery('#previewDiv').find('.save_file').each(function ()
    {
        var href = jQuery(this).children('a:first-child').attr('href');
        console.log(href);
        jQuery(this).children('a:first-child').replaceWith('<a target="_blank" class="documentUrl btn btn-xs btn-primary" href="'+href+'"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Open File </a>')
    });



    // jQuery(".hashs").replaceWith("");

    ///Change in opener
    //    window.opener.jQuery('body').fadeOut("slow");
    //home is the id of body in template page. It may be an id of div or any element
    //    jQuery(window).unload(function () {
    //window.opener.jQuery('#home').css({"display": "none"});
    //    });

    function CloseMe()
    {
        window.opener.jQuery("fieldset").css({"display": "none"});
        window.opener.jQuery("fieldset.scheduler-border").css({"display": "block"});
        window.opener.jQuery(".actions").css({"display": "block"});
        window.opener.jQuery(".steps").css({"display": "block"});
        window.opener.jQuery(".draft").css({"display": "block"});
        window.opener.jQuery(".title ").css({"display": "block"});
        // window.opener.jQuery('.input-group-addon').css({"visibility": "visible"});
        // window.opener.jQuery(".visa_type_box ").css({"display": "block"});
        // window.opener.jQuery("#selected_visa_type ").css({"display": "none"});

        window.opener.jQuery("#IrcRecommendationSecondAdhocForm-p-4").css({"display": "block"});
        window.opener.jQuery(".last").addClass('current');
        window.opener.jQuery('body').css({"display": "block"});
        window.opener.jQuery("select").css({
            "border": '1px solid #ccc',
            "background": '#fff',
            "pointer-events": 'inherit',
            "box-shadow": 'inherit',
            "-webkit-appearance": 'menulist',
            "-moz-appearance": 'menulist',
            "appearance": 'menulist'
        });
        window.close();
    }

    // intel-input plugin style
    jQuery('.iti__country-list, .iti__hide').css({
        "display": 'none'
    });

    jQuery('.iti__selected-dial-code').css({
        "float": 'left',
        "line-height": '30px',
        "padding-left": '12px'
    });

    jQuery('.cr-boundary,.cr-slider-wrap').css({
        "display": 'none'
    });

    jQuery('.select2').css({
        "display": 'none'
    });
</script>
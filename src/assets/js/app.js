import $ from 'jquery';
import 'what-input';
import Headroom from "headroom.js";

import "intl-tel-input";

// Foundation JS relies on a global variable. In ES6, all imports are hoisted
// to the top of the file so if we used `import` to import Foundation,
// it would execute earlier than we have assigned the global variable.
// This is why we have to use CommonJS require() here since it doesn't
// have the hoisting behavior.
window.jQuery = $;
require('foundation-sites');



function viaTelValidator( $el, required, parent ) {
    var value = $el.val().trim();
    var valid = false;
    if (required) {
        valid = ities[$el.attr('data-intl-tel-input-id')].isPossibleNumber();
    } else {
        valid = ((value==='') || ities[$el.attr('data-intl-tel-input-id')].isPossibleNumber());
    }
    return valid;

}
Foundation.Abide.defaults.validators.telvalidator = viaTelValidator;

// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';


$(document).foundation();

var mySiteHeader = document.querySelector('.siteheader');
if (mySiteHeader) {
    var siteheaderheadroom = new Headroom(mySiteHeader, {
        offset : 76,
        // tolerance : {
        //     up : 76,
        //     down : 0
        // },
        classes : {
            // when element is initialised
            initial : "headroom",
            // when scrolling up
            pinned : "siteheader--pinned",
            // when scrolling down
            unpinned : "siteheader--unpinned",
            // when above offset
            top : "siteheader--top",
            // when below offset
            notTop : "siteheader--not-top",
            // when at bottom of scoll area
            bottom : "siteheader--bottom",
            // when not at bottom of scroll area
            notBottom : "siteheader--not-bottom",
            // when frozen method has been called
            frozen: "siteheader--frozen"
        },
    });
    siteheaderheadroom.init();
}

var mySitemheader = document.querySelector('.sitemheader');
if (mySitemheader) {
    var sitemheaderheadroom = new Headroom(mySitemheader, {
        offset : 56,
        classes : {
            initial : "headroom",
            pinned : "sitemheader--pinned",
            unpinned : "sitemheader--unpinned",
            top : "sitemheader--top",
            notTop : "sitemheader--not-top",
            bottom : "sitemheader--bottom",
            notBottom : "sitemheader--not-bottom",
            frozen: "sitemheader--frozen"
        },
    });
    sitemheaderheadroom.init();
}


// var mySideSticky = document.querySelector('.sidesticky');
// if (mySideSticky) {
//     var sidestickyheadroom = new Headroom(mySideSticky, {
//         classes : {
//             initial : "headroom",
//             pinned : "sidesticky--pinned",
//             unpinned : "sidesticky--unpinned",
//             top : "sidesticky--top",
//             notTop : "sidesticky--not-top",
//             bottom : "sidesticky--bottom",
//             notBottom : "sidesticky--not-bottom",
//             frozen: "sidesticky--frozen"
//         },
//     });
//     sidestickyheadroom.init();
// }

$('.mobilemenu, .mobilesecmenu').on('click','a', function(e){
    $('body').removeClass('is-frozen');
    $('body').removeClass('mobilenavpanel-is-open');
});
$('.js-mobilenavpanelopen').on('click', function(e) {
    e.preventDefault();
    $('body').addClass('is-frozen');
    $('body').addClass('mobilenavpanel-is-open');
});

$('.js-allopen').on('click', function(e) {
    e.preventDefault();
    $('body').addClass('is-frozen');
    $('body').addClass('mobilenavpanel-is-open');
});

$('.js-mobilenavpanelclose').on('click', function(e) {
    e.preventDefault();
    $('body').removeClass('is-frozen');
    $('body').removeClass('mobilenavpanel-is-open');
});

$('.js-allclose').on('click', function(e) {
    e.preventDefault();
    $('body').removeClass('is-frozen');
    $('body').removeClass('mobilenavpanel-is-open');
});



/**************
 * 
 * Form Handle
 * 
 * ***********/

$(".reqform").on("submit", function(ev, frm) {
    ev.preventDefault();
})
.on("forminvalid.zf.abide", function(ev, frm) {
    var output = '<p class="itsnotok">' + viamobileappglobals.messages.missingField + '</p>';
    $("#reqformresult").hide().html(output).slideDown();
})
.on("formvalid.zf.abide", function(ev, frm) {
    //get input field values
    var user_fname = $("input[name=r_fname]").val();
    var user_lname = $("input[name=r_lname]").val();
    var user_email = $("input[name=r_email]").val();
    var user_tel = ities[$("input[name=r_tel]").attr('data-intl-tel-input-id')].getNumber(); //var user_tel = $("input[name=r_tel]").val();
    var user_countrycode = ities[$("input[name=r_tel]").attr('data-intl-tel-input-id')].getSelectedCountryData().iso2;
    var user_zip = $("input[name=r_zip]").val();
    var user_city = $("input[name=r_city]").val();
    var user_address = $("input[name=r_address]").val();

    var user_message = $("textarea[name=r_message]").val();
    
    var user_acceptgdpr = $("input[name=r_acceptgdpr]").is(":checked")?1:0;
    var user_acceptmarketing = $("input[name=r_acceptmarketing]").is(":checked")?1:0;

    var user_audiencesource = $('input[name=r_audiencesource]:checked').length?$('input[name=r_audiencesource]:checked').val():'-';


    var user_vehicle= '';
    var user_vehiclearray = [];
    var user_sap_VehicleNature = '';
    var user_sapvehiclenaturearray = [];
    var user_sap_VehicleCategory = '';
    var user_sapvehiclecategoryarray = [];
    $.each($('input[name="r_vehicle[]"]:checked'), function(){
        user_vehiclearray.push($(this).val());
        user_sapvehiclenaturearray.push($(this).attr('data-sapvehiclenature'));
        user_sapvehiclecategoryarray.push($(this).attr('data-sapvehiclecategory'));
    });
    user_vehicle+=user_vehiclearray.join(' | ');
    user_sap_VehicleNature+=user_sapvehiclenaturearray.join(', ');
    user_sapvehiclecategoryarray+=user_sapvehiclecategoryarray.join(', ');

    var user_time= '';
    var user_renttime = [];
    $.each($('input[name="r_time[]"]:checked'), function(){
        user_renttime.push($(this).val());
    });
    user_time+=user_renttime.join(' | ');



    var proceed = true;
    var output = "";



    //everything looks good! proceed...
    if (proceed) {
        //data to be sent to server
        var post_data = {
            fname: user_fname,
            lname: user_lname,
            email: user_email,
            tel: user_tel,
            countrycode: user_countrycode,
            zip: user_zip,
            city: user_city,
            address: user_address,
            message: user_message,
            acceptgdpr: user_acceptgdpr,
            acceptmarketing: user_acceptmarketing,
            time: user_time,
            audiencesource: user_audiencesource,
            vehicle: user_vehicle,
            sap_VehicleNature: user_sap_VehicleNature,
            sap_VehicleCategory: user_sap_VehicleCategory
        };
        $(".reqformsubmit").addClass("disabled");
        $(".reqformsubmit").attr("disabled", "disabled");
        $(".reqformsubmit").text($(".reqformsubmit").data('progresstext'));

        //Ajax post data to server
        $.post(
            $('.reqform').attr("action"),
            post_data,
            function(response) {
                //load json data from server and output message
                if (response.type === "error") {
                    output = '<p class="itsnotok">' + response.text + '</p>';
                    console.log(response.text);
                } else {
                    output = '<p class="itsok">' + response.text + '</p>';
                    $(".reqform").addClass("is-alreadysent");
                    $(".reqformsubmit").addClass('light');
                    $(".reqformclose").text($(".reqformclose").data('succestext')).removeClass('light');
                    $(".reqformresult").prepend(output);
                    $(".reqformresult").addClass("is-active");

                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'event': 'Ajanlatkeres Lakossagi Success'
                    });

                    //reset values in all input fields
                    $("input").val("");
                    $("textarea").val("");
                }
                $(".reqformresult")
                    .hide()
                    .html(output)
                    .slideDown();
                $(".reqformsubmit").removeClass("disabled");
                $(".reqformsubmit").removeAttr("disabled");
                $(".reqformsubmit").text($(".reqformsubmit").data('sendtext'));
            },
            "json"
        );
    }

    return false;
});

//reset previously set border colors and hide all message on .keyup()
$('.reqform input, .reqform textarea, .reqform [name="r_acceptgdpr"]').keyup(function() {
    $(".reqformresult").slideUp();
});


$('a.prtbl__price').on('click', function(e) {
    $('input[name^="r_time"], input[name^="r_vehicle"]').prop( "checked", false );
    var fields = $(this).attr('data-target').split('|');
    var vehicle = fields[0];
    var time = fields[1];
    $('#' + vehicle).prop( "checked", true );
    $('#' + time).prop( "checked", true );
});


var itiErrorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
var ities = [];

$('input[name="r_tel"]').each(function (index, element) {
    ities[index] = window.intlTelInput(element, {
        preferredCountries: ["hu", "sk", "cz", "rs", "ro", "fr", "at" ],
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch("https://ipapi.co/json")
            .then(function(res) { return res.json(); })
            .then(function(data) { callback(data.country_code); })
            .catch(function() { callback("us"); });
        },
        utilsScript: viamobileappglobals.root_uri + 'assets/js/vendor/utils.js'
    });
});

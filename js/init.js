var init = {
    init: function()
    {
        $(".post_content").lightGallery({
            selector: '.c_gallery_link',
            width: '960px',
            controls: true,
            download: false,
            counter: false
        });

        $('.c_tooltip').popover({
            placement: 'right',
            trigger: 'hover',
            html: true
        });

        $('.c_tooltip_bottom').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true
        });

        $('.c_tooltip_top').popover({
            placement: 'top',
            trigger: 'hover',
            html: true
        });
        $('#i_button_karma_clear').click(function(){
            $('#i_karma_clear').modal();
        });
    },

    getPage: function(alias)
    {
        $.ajax({
            url: '/' + alias,
            type    : "post",
            dataType: "json",
            data: {
                action: 'getPage'
            },
            beforeSend: function()
            {
                $('#page_dialog').modal();
                $('#page_dialog_progress').show();
            },
            success: function( data )
            {
                $('#page_dialog_label').html(data.page.title);
                $('#page_dialog_data').html('<div style="position: relative">' + data.page.text + '</div>');
            },
            complete: function()
            {
                $('#page_dialog_progress').hide();
            },
            error: function()
            {
                $('#page_dialog_error').addClass('alert alert-danger').html('Не удалось выполнить запрос');
            }
        });
    }
};

$(document).ready(function(){
    init.init();
});

function number_format ( number, decimals, dec_point, thousands_sep ) {	// Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +	 bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ",";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


    return km + kw + kd;
}
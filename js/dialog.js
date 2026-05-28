var dialog = {
    init: function() {

    },
    actionDialog: function(action, params, url, size)
    {
        $('#i_dialog .modal-dialog').removeClass('modal-lg').removeClass('modal-sm');
        if (url === undefined)
        {
            url = '';
        }

        if (size !== undefined)
        {
            $('#i_dialog .modal-dialog').addClass(size);
        }



        if (typeof(params) != 'object')
        {
            params = {};
        }
        if (Array.isArray(params))
        {
            params = {};
        }
        params.action = action;
        $.ajax({
            type    : "post",
            dataType: "json",
            data: params,
            url: url,
            beforeSend: function()
            {
                $('#i_dialog').modal('show');
                $('#i_dialog_data').hide();
                $('#i_dialog_error').hide();
                $('#i_dialog_progress').show();
            },
            complete: function()
            {
                $('#i_dialog_progress').hide();
            },
            error: function()
            {
                $('#i_dialog_error').html('<div class="alert alert-danger">Не удалось выполнить запрос.</div>').show();
            },
            success: function(data)
            {
                if (data.success)
                {
                    $('#i_dialog_error').hide();
                    $('#i_dialog_label').html(data.title);
                    $('#i_dialog_data').html(data.html).show();
                }
                else
                {
                    $('#i_dialog_data').hide();
                    $('#i_dialog_error').html('<div class="alert alert-danger">'+data.errorText+'</div>').show();
                }
            }
        });

    }
};
$(document).ready(function(){
    dialog.init();
});
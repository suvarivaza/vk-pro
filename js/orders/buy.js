var buy =
{
    services: [],
    limit: 0,
    packId: 0,
    giftId: 0,

    init: function()
    {
        $('.btn-pack-buy').click(function(){
            buy.showPackDialog( $(this).data('packId') );
        });
        $('.btn-gift-activate').click(function(){
            buy.showGiftDialog( $(this).data('giftId') );
        });
        $('#i_pack_dialog_save').click(function()
        {
            var val = $('#i_balance_add_value').val();
            if (val > 0)
            {
                location.href = '/orders/action?' + $('#i_form_pack_select').serialize();
                return;
            }

            $('.c-service').each(function(){
                var val = $(this).val();
                if (val == '')
                {
                    $(this).addClass('alert-danger');
                }
            });
            $('.c-groupId').each(function(){
                var val = $(this).val();
                if (val == 0)
                {
                    $('#' + $(this).attr('id') + '_title').addClass('alert-danger');
                }
            });
            location.href = '/orders/action?' + $('#i_form_pack_select').serialize();
        });

        $('.c-balance-another').click(function(){
            buy.showPackDialog(0);
        });

        $('#i_pack_dialog').on('shown.bs.modal', function(){
            buy.getGroupsList();
        });
    },

    getGroupsList: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getPackForm',
                packId: buy.packId,
                giftId: buy.giftId
            },
            beforeSend: function()
            {
                $('#i_pack_dialog_title').html('Подождите...');
                $('#i_pack_dialog_progress').show();
            },
            success: function( data )
            {
                if (data.success)
                {
                    if (data.redirect)
                    {
                        location.href = data.href;
                    }
                    buy.limit = data.limit;
                    $('#i_pack_dialog_title').html(data.title);
                    $('#i_pack_dialog_container').html(data.html);
                }
                else
                {
                    $('#i_pack_dialog_container').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            },
            complete: function()
            {
                $('#i_pack_dialog_progress').hide();
            },
            error: function()
            {
                $('#i_pack_dialog_container').html('<div class="alert alert-danger">Не удалось выполнить запрос. Попробуйте позднее</div>');
            }
        });
    },

    serviceSelect: function(btn)
    {
        var service = $(btn).data('service');
        var found = false;
        for (i in buy.services)
        {
            if (buy.services[i] == service)
            {
                buy.services.splice(i, 1);
                $(btn).removeClass('btn-success');
                found = true;
                break;
            }
        }

        if (!found && buy.services.length < buy.limit)
        {
            buy.services.push(service);
            $(btn).addClass('btn-success');
        }
    },

    showGiftDialog: function( giftId )
    {
        buy.giftId = giftId;
        $('#i_pack_dialog').modal();
    },

    showPackDialog: function(packId)
    {
        buy.packId = packId;
        $('#i_pack_dialog').modal();
    }
};

$(document).ready(function(){
    buy.init();
});

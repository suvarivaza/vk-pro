var admin_user =
{
    init: function()
    {
        $('#i_modal_activate_active').click(function () {
            admin_user.activateProfit();
        });

        $('.c-referrer').click(function(){
            admin_user.referrerToggle($(this));
        });
    },
    referrerToggle: function(icon)
    {
        var userId = $(icon).data('userId');
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'referrerToggle',
                userId: userId
            },
            success: function(data)
            {
                location.reload();
            }
        });
    },
    showForm: function(userId, action)
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: action,
                userId: userId
            },
            beforeSend: function()
            {
                $('#i_modal_activate_form').html('');
                $('#i_modal_activate').modal();
                $('#i_modal_activate_progress').show();
                $('#i_modal_activate_data').removeClass('alert alert-danger').html('');
            },
            success: function(data)
            {
                $('#i_modal_activate_form').html(data.html);
            },
            complete: function()
            {
                $('#i_modal_activate_progress').hide();
            },
            error: function()
            {
                $('#i_modal_activate_data').addClass('alert alert-danger').html('Не удалось выполнить запрос');
            }
        });
    },
    activateProfit: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i_modal_activate_form').serialize(),
            beforeSend: function()
            {
                $('#i_modal_activate_progress').show();
                $('#i_modal_activate_data').removeClass('alert alert-danger').html('');
            },
            success: function(data)
            {
                if (data.success)
                {
                    $('#i_modal_activate').modal('hide');
                    admin_user.showUserModal(data.userId);
                }
                else
                {
                    $('#i_modal_activate_data').addClass('alert alert-danger').html(data.errorText);
                }
            },
            complete: function()
            {
                $('#i_modal_activate_progress').hide();
            },
            error: function()
            {
                $('#i_modal_activate_data').addClass('alert alert-danger').html('Не удалось выполнить запрос');
            }
        });
    },
    showUserModal: function(userId)
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getUserDetail',
                userId: userId
            },
            beforeSend: function()
            {
                $('#i_modal_user_data').removeClass('alert alert-danger').html('');
                $('#i_modal_user').modal();
                $('#i_modal_user_progress_span').html('Идет загрузка данных пользователя. Подождите...');
                $('#i_modal_user_progress').show();

            },
            success: function(data)
            {
                if (data.success)
                {
                    $('#i_modal_user_data').html(data.html);
                }
            },
            complete: function()
            {
                $('#i_modal_user_progress').hide();
            },
            error: function()
            {
                $('#i_modal_user_data').addClass('alert alert-danger').html('Не удалось выполнить запрос');
            }
        });
    }
};
$(document).ready(function(){
    admin_user.init();
});
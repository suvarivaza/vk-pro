var special = {
    groupId: 0,
    wait: '<div id="i_dialog_group_detail_progress-" class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">&nbsp;</span></div></div>',
    groupBuy: false,
    months: 0,
    init: function()
    {
        $('#i_button_extend').click(function(){
            $('#i_row_extend').toggle('slide');
        });

        $('#i_special_group_add').click(function(){
            $('#i_dialog_group_add').modal('show');
        });
        $('.c_special_slot_active').click(function(){
            special.months = $(this).data('months');
            $('#i_dialog_group_add').modal('show');
        });

        $('#i_dialog_group_add').on('shown.bs.modal', function(){
            special.getGroupsList();
        });

        $('#i_dialog_group_add_button_save').click(function(){
            if (special.groupBuy)
            {
                var val = $('input[name="month"]:checked').val();
                location.href = '/orders/action?service=special&group=true&month=' + val;
                return true;
            }
            special.saveClick();
        });

        $('.c-special-group').click(function(){
            $(this).toggleClass('active');
            location.href = '/tasks/special/' + $(this).data('groupId') + '/all/1';
        });
    },
    templateToggle: function(templateId)
    {
        $.ajax({

        });
    },
    templateAdd: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i-form-template-add').serialize(),
            beforeSend: function()
            {
                $('#i-templateAdd-result').html(special.wait);
            },
            success: function(data)
            {
                if (data.success)
                {
                    special.getTemplatesList();
                }
                else
                {
                    $('#i-templateAdd-result').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            },
            error: function()
            {
                $('#i-templateAdd-result').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    },
    getTemplatesList: function()
    {
        $('.c-div-group-detail').hide();
        $('.btn-special').removeClass('btn-primary').addClass('btn-default');
        $('#i-btn-special-detail-list').removeClass('btn-default').addClass('btn-primary');
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateList',
                groupId: special.groupId
            },
            beforeSend: function(){
                $('#i_templates_list').html(special.wait).show();
            },
            error: function()
            {
                $('#i_templates_list').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i_templates_list').html(data.html);
                }
                else
                {
                    $('#i_templates_list').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            }
        });
    },
    getTemplateAdd: function()
    {
        $('.c-div-group-detail').hide();

        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateFrom',
                groupId: special.groupId
            },
            beforeSend: function(){
                $('#i_template_add').html(special.wait).show();
            },
            error: function()
            {
                $('#i_template_add').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('.btn-special').removeClass('btn-primary').addClass('btn-default');
                    $('#i-btn-special-detail-add').removeClass('btn-default').addClass('btn-primary');
                    $('#i_template_add').html(data.html);
                }
                else
                {
                    $('#i_template_add').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            }
        });

    },
    getGroupDetail: function( btn )
    {
        var groupId = $(btn).data('specialId');
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getGroupForm',
                groupId: groupId
            },
            beforeSend: function(){
                $('#i_dialog_group_detail_progress-' + groupId).show();
            },
            complete: function()
            {
                $('#i_dialog_group_detail_progress-' + groupId).hide();
            },
            error: function()
            {
                $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    special.groupId = groupId;
                    $('#i-group-detail-' + groupId).html(data.html);
                    special.getTemplatesList();
                }
                else
                {
                    $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            }
        });
    },
    saveClick: function(take)
    {
        if (take === true)
        {
            $('#i_dialog_group_add_data > form').append('<input type="hidden" name="take" value="true" />');
        }

        var data = $('#i_dialog_group_add_data > form').serialize();
        $.ajax({
            type    : "post",
            dataType: "json",
            data: data,
            beforeSend: function()
            {
                $('#i_dialog_group_add_error').hide();
                $('#i_dialog_group_add_progress').show();
            },
            complete: function()
            {
                $('#i_dialog_group_add_progress').hide();
            },
            error: function()
            {
                $('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
            },
            success: function( data )
            {
                if (data.success)
                {
                    if (data.reload)
                    {
                        location.reload();
                        return;
                    }
                    $('#i_dialog_group_add_data').html(data.html).show();
                    if (data.token)
                    {
                        $('#i_dialog_group_add_button_save').html('Сохранить');
                    }
                    else
                    {
                        $('#i_dialog_group_add_button_save').html('Добавить');
                    }
                }
                else
                {
                    $('#i_dialog_group_add_error').html('<div class="alert alert-danger">'+data.errorText+'</div>').show();
                }
            }
        });
    },
    getGroupsList: function()
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getGroups',
                months: special.months,
                isFree: $('#i-isFree').val()
            },
            beforeSend: function()
            {
                $('#i_dialog_group_add_error').html('').hide();
                $('#i_dialog_group_add_progress').show();
            },
            complete: function()
            {
                $('#i_dialog_group_add_progress').hide();
            },
            error: function()
            {
                $('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i_dialog_group_add_error').html('').hide();
                    $('#i_dialog_group_add_data').html(data.html);
                    if (data.token)
                    {
                        $('#i_dialog_group_add_button_save').html('Сохранить');
                    }
                    else
                    {
                        $('#i_dialog_group_add_button_save').html('Добавить');
                    }
                }
                else
                {
                    $('#i_dialog_group_add_error').html('<div class="alert alert-danger">'+data.errorText+'</div>').show();
                }
            }
        });
    }
};
$(document).ready(function(){
    special.init();
});
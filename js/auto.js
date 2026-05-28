var auto = {
    groupId: 0,
    wait: '<div id="i_dialog_group_detail_progress-<?= $group->autoGroupId ?>" class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">&nbsp;</span></div></div>',
    groupBuy: false,
    months: 0,
    init: function()
    {
        $('#i_button_extend').click(function(){
            $('#i_row_extend').toggle('slide');
        });

        $('#i_auto_group_add').click(function(){
            $('#i_dialog_group_add').modal('show');
        });
        $('.c_auto_slot_active').click(function(){
            auto.months = $(this).data('months');
            $('#i_dialog_group_add').modal('show');
        });

        $('#i_dialog_group_add').on('shown.bs.modal', function(){
            auto.getGroupsList();
        });

        $('#i_dialog_group_add_button_save').click(function(){
            auto.saveClick();
        });

        $('.c-auto-group').click(function(){
            location.href = '/auto/' + $(this).data('autoGroupId');
        });

    },
    templateToggle: function(templateId)
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'templateToggle',
                templateId: templateId,
                groupId: auto.groupId
            },
            error: function()
            {
                alert('Не удалось выполнить запрос!');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i-group-settings-nav > li.active > a').trigger('click');
                }
                else
                {
                    alert(data.errorText);
                }
            }
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
                $('#i-templateAdd-result').html(auto.wait);
            },
            success: function(data)
            {
                console.log('templateAdd', data)

                if (data.success)
                {
                    $('#i_template_link_list').trigger('click');
                }
                else
                {
                    $('#i-templateAdd-result').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            },
            error: function()
            {
                $('#i-templateAdd-result').html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
            }
        });
    },
    getTemplatesList: function()
    {
        $('.c-div-group-detail').hide();
        $('.btn-auto').removeClass('btn-primary').addClass('btn-default');
        $('#i-btn-auto-detail-list').removeClass('btn-default').addClass('btn-primary');
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateList',
                groupId: auto.groupId
            },
            beforeSend: function(){
                $('#i_templates_list').html(auto.wait).show();
            },
            error: function()
            {
                $('#i_templates_list').html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
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
    templateToArchive: function(templateId)
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'templateToArchive',
                templateId: templateId,
                groupId: auto.groupId
            },
            error: function()
            {
                alert('Не удалось выполнить запрос!');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i-group-settings-nav > li.active > a').trigger('click');
                }
                else
                {
                    alert(data.errorText);
                }
            }
        });
    },
    getTemplatesListArchive: function()
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateList',
                groupId: auto.groupId,
                isArchive: true
            },
            beforeSend: function(){
                $('#i_templates_list_archive').html(auto.wait).show();
            },
            error: function()
            {
                $('#i_templates_list_archive').html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i_templates_list_archive').html(data.html);
                }
                else
                {
                    $('#i_templates_list_archive').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            }
        });
    },
    getTemplateEdit: function(templateId)
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateFrom',
                groupId: auto.groupId,
                templateId: templateId
            },
            beforeSend: function(){
                $('#i_template_add').html(auto.wait).show();
            },
            error: function()
            {
                $('#i_template_add').html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i_template_detail_' + templateId).html(data.html);
                }
                else
                {
                    $('#i_template_add').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            },
            complete: function () {
                $('#i_template_add').hide();
            }
        });
    },
    getTemplateAdd: function()
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateFrom',
                groupId: auto.groupId
            },
            beforeSend: function(){
                $('#i_template_add').html(auto.wait).show();
            },
            error: function()
            {
                $('#i_template_add').html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
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
        var groupId = $(btn).data('autoGroupId');
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
                $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">Не удалось выполнить запрос!</div>');
            },
            success: function( data )
            {
                if (data.success)
                {
                    auto.groupId = groupId;
                    $('#i-group-detail-' + groupId).html(data.html);
                    auto.getTemplatesList();
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
                months: auto.months,
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
    auto.init();
});
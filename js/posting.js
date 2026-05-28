var posting = {
    groupId: 0,
    wait: '<div id="i_dialog_group_detail_progress-<?= $group->postingGroupId ?>" class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">&nbsp;</span></div></div>',
    groupBuy: false,
    postId: '',
    months: 0,
    init: function()
    {
        $('#i_button_extend').click(function(){
            $('#i_row_extend').toggle('slide');
        });

        $('#i_posting_group_add').click(function(){
            $('#i_dialog_group_add').modal('show');
        });
        $('.c_posting_slot_active').click(function(){
            posting.months = $(this).data('months');
            $('#i_dialog_group_add').modal('show');
        });

        $('#i_dialog_group_add').on('shown.bs.modal', function(){
            posting.getGroupsList();
        });

        $('#i_dialog_group_add_button_save').click(function(){
            if (posting.groupBuy)
            {
                var val = $('input[name="month"]:checked').val();
                location.href = '/orders/action?service=posting&group=true&month=' + val;
                return true;
            }
            posting.saveClick();
        });

        $('.c-posting-group').click(function(){
            $(this).toggleClass('active');
            location.href = '/posting/' + $(this).data('postingGroupId');
        });
        $('#i_post_form').submit(false);
        $('#i_group_settings_save').click(function(){
            posting.groupSettingsSave();
        });
    },
    postEdit: function(postId)
    {
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'postEdit',
                postId: postId
            },
            beforeSend: function()
            {
                $('#i_post_form').hide();
                $('#i_post_detail_' + postId).hide();
                $('#i_post_edit_' + postId).html(posting.wait).show();
            },
            error: function()
            {
                $('#i_post_detail_' + postId).show();
                $('#i_post_edit_' + postId).hide();
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос!'})

            },
            success: function( data )
            {
                $('#i_post_edit_' + postId).html(data.html);
                $('#i_form_photos_container_form').html(data.uploadForm);
            }
        });
    },
    postDel: function(postId)
    {

        Sw.confirm.fire({
            text: "Вы действительно хотите удалить пост?",
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отмена',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type    : "post",
                    dataType: "json",
                    data: {
                        action: 'postDel',
                        postId: postId
                    },
                    error: function()
                    {
                        Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос!'})
                    },
                    success: function( data )
                    {
                        location.reload();
                    }
                });
            }
        })
    },
    postPublish: function(postId)
    {

        Sw.confirm.fire({
            text: "Вы действительно хотите опубликовать данный пост прямо сейчас?",
            confirmButtonText: 'Опубликовать',
            cancelButtonText: 'Отмена',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type    : "post",
                    dataType: "json",
                    data: {
                        action: 'postPublish',
                        postId: postId
                    },
                    error: function()
                    {
                        Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос!'})
                    },
                    success: function( data )
                    {
                        location.reload();
                    }
                });
            }
        })


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
                $('#i-templateAdd-result').html(posting.wait);
            },
            success: function(data)
            {
                if (data.success)
                {
                    posting.getTemplatesList();
                }
                else
                {
                    //$('#i-templateAdd-result').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    Sw.toast.fire({icon: 'error', title: data.errorText})
                }
            },
            error: function()
            {
                //$('#i-templateAdd-result').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            }
        });
    },
    getTemplatesList: function()
    {
        $('.c-div-group-detail').hide();
        $('.btn-posting').removeClass('btn-primary').addClass('btn-default');
        $('#i-btn-posting-detail-list').removeClass('btn-default').addClass('btn-primary');
        $.ajax({
            type    : "post",
            dataType: "json",
            data: {
                action: 'getTemplateList',
                groupId: posting.groupId
            },
            beforeSend: function(){
                $('#i_templates_list').html(posting.wait).show();
            },
            error: function()
            {
                //$('#i_templates_list').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('#i_templates_list').html(data.html);
                }
                else
                {
                    //$('#i_templates_list').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    Sw.toast.fire({icon: 'error', title: data.errorText})
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
                groupId: posting.groupId
            },
            beforeSend: function(){
                $('#i_template_add').html(posting.wait).show();
            },
            error: function()
            {
                //$('#i_template_add').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            },
            success: function( data )
            {
                if (data.success)
                {
                    $('.btn-posting').removeClass('btn-primary').addClass('btn-default');
                    $('#i-btn-posting-detail-add').removeClass('btn-default').addClass('btn-primary');
                    $('#i_template_add').html(data.html);
                }
                else
                {
                    $('#i_template_add').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    Sw.toast.fire({icon: 'error', title: data.errorText})

                }
            }
        });

    },
    getGroupDetail: function( btn )
    {
        var groupId = $(btn).data('postingGroupId');
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
                //$('#i-group-detail-' + groupId).html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            },
            success: function( data )
            {
                if (data.success)
                {
                    posting.groupId = groupId;
                    $('#i-group-detail-' + groupId).html(data.html);
                    posting.getTemplatesList();
                }
                else
                {
                    $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    Sw.toast.fire({icon: 'error', title: data.errorText})
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
                //$('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнит запрос.'})
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
                    //$('#i_dialog_group_add_error').html('<div class="alert alert-danger">'+data.errorText+'</div>').show();
                    Sw.toast.fire({icon: 'error', title: data.errorText})
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
                months: posting.months,
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
                //$('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
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
    },
    cancelEditPost: function()
    {
        location.reload();
    },

    addPost: function()
    {
        $('#i_text' + posting.postId).html($('#post_field' + posting.postId).html());

        var paramObj = {};
        $.each($('#i_post_form' + posting.postId).serializeArray(), function(_, kv) {
            if (paramObj.hasOwnProperty(kv.name)) {
                paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                paramObj[kv.name].push(kv.value);
            }
            else {
                paramObj[kv.name] = kv.value;
            }
        });

        paramObj.videos = post_edit.videos_done;

        $.ajax({
            type    : "post",
            dataType: "json",
            data: paramObj,
            complete: function(){
                location.reload();
            }
        });
        return false;
    },

    addPoll: function()
    {
        var index = $('#i_poll_example').data('index');
        index += 1;
        $('#i_poll_example').data('index', index);
        var html = $('#i_poll_example').html();
        var div = $('<div id="i_poll_containder_'+index+'"></div>').html(html).data('index', index);
        $('#submit_post' + posting.postId).before(div);
        $(div).find('.c_poll_title').attr('name', 'poll['+index+'][title]');
        $(div).find('.c_poll_answer').data('index', index).attr('name', 'poll['+index+'][answers][]').keyup(function(e){
            post_edit.answerEdit($(this), e);
        });
    },

    addAnswer: function(button)
    {
        var html = $(button).parent().prev().html();

        var div = $('<div></div>').addClass('col-sm-12').html(html);
        $(button).parent().before(div);
        $('.c_poll_answer', $(button).parent().prev()).focus();
    },

    emojiAdd: function(code, postId)
    {
        if (postId)
        {
            $('#post_field_' + postId).focus();
            document.execCommand('insertHTML', null, ' <img src="/img/emoji/'+code+'.png" />');

            $('#post_field_' + postId).focus();
        }
        else
        {
            $('#post_field').focus();

            document.execCommand('insertHTML', null, ' <img src="/img/emoji/'+code+'.png" />');

            $('#post_field').focus();
        }
    },
    insertTextAtCursor: function(text) {
        var sel, range, html;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var elem = document.createElement('span');
                elem.innerHTML = text;
                range.insertNode( elem );
            }
        } else if (document.selection && document.selection.createRange) {
            document.selection.createRange().pasteHTML(text);
        }
    },
    saveSelection: function() {
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                return sel.getRangeAt(0);
            }
        }
        return null;
    },

    restoreSelection: function (range) {
        if (range)
        {
            if (window.getSelection)
            {
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
            else if (document.selection && range.select)
            {
                range.select();
            }
        }
    },
    groupSettingsSave: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i_group_settings_form').serialize(),
            beforeSend: function()
            {
                $('#i_group_settings_progress').show();
            },
            complete: function()
            {
                $('#i_group_settings_progress').hide();
            },
            error: function()
            {
                //$('#i_group_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос').show();
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            },
            success: function( data )
            {
                if (data.success)
                {
                    location.reload();
                }
                else
                {
                    //$('#i_group_settings_error').addClass('alert alert-danger').html(data.errorText).show();
                    Sw.toast.fire({icon: 'error', title: data.errorText})
                }
            }
        });
    },

    groupSettings: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getGroupSettingsFrom'
            },
            beforeSend: function()
            {
                $('#i_group_settings_progress').show();
                $('#i_group_settings_error').addClass('alert alert-info').html('Подождите, идет выполнение запроса');
                $('#i_group_settings').modal('show');
            },
            complete: function()
            {
                $('#i_group_settings_progress').hide();
            },
            success: function(data)
            {
                $('#i_group_settings_error').removeClass('alert').removeClass('alert-info').html('');
                if (data.success)
                {
                    $('#i_group_settings_data').html(data.html);
                }
                else
                {
                    //$('#i_group_settings_error').addClass('alert alert-danger').html(data.errorText);
                    Sw.toast.fire({icon: 'error', title: data.errorText})
                }
            },
            error: function()
            {
                //$('#i_group_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос. Попробуйте позднее');
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос. Попробуйте позднее!'})
            }
        });
    },
};
$(document).ready(function(){
    posting.init();
});
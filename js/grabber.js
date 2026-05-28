var grabber = {
    groupId: 0,
    wait: '<div id="i_dialog_group_detail_progress-" class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">&nbsp;</span></div></div>',
    groupBuy: false,
    postId: '',
    months: 0,
    init: function () {
        $('#i_button_extend').click(function () {
            $('#i_row_extend').toggle('slide');
        });

        $('#i_grabber_group_add').click(function () {
            $('#i_dialog_group_add').modal('show');
        });
        $('.c_grabber_slot_active').click(function () {
            grabber.months = $(this).data('months');
            $('#i_dialog_group_add').modal('show');
        });

        $('#i_dialog_group_add').on('shown.bs.modal', function () {
            grabber.getGroupsList();
        });

        $('#i_dialog_group_add_button_save').click(function () {
            if (grabber.groupBuy) {
                var val = $('input[name="month"]:checked').val();
                location.href = '/orders/action?service=grabber&group=true&month=' + val;
                return true;
            }
            grabber.saveClick();
        });

        $('.c-grabber-group').click(function () {
            $(this).toggleClass('active');
            location.href = '/grabber/' + $(this).data('grabberGroupId');
        });
        $('.c-source-settings').click(function () {
            grabber.sourcesEdit($(this).data('sourceId'));
        });
        $('#i_source_settings_save').click(function () {
            grabber.sourceSave();
        });
        $('.c-source-refresh').click(function () {

            Sw.confirm.fire({
                text: "Вы действительно хотите очистить посты из очереди по данному источнику?",
                confirmButtonText: 'Очистить',
                cancelButtonText: 'Отмена',
            }).then((result) => {
                if (result.isConfirmed) {
                    grabber.sourceRefresh($(this).data('sourceId'));
                }
            })


        });
        $('.c-source-remove').click(function () {

            Sw.confirm.fire({
                text: "Вы действительно хотите удалить источник?",
                confirmButtonText: 'Удалить',
                cancelButtonText: 'Отмена',
            }).then(result => {
                if (result.isConfirmed) {
                    grabber.sourceRemove($(this).data('sourceId'));
                }
            })

        });

        $('#i_group_settings_save').click(function () {
            grabber.groupSettingsSave();
        });
    },

    postEdit: function (postId) {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'postEdit',
                postId: postId
            },
            beforeSend: function () {
                $('#i_post_form').hide();
                $('#i_post_detail_' + postId).hide();
                $('#i_post_edit_' + postId).html(grabber.wait).show();
            },
            error: function () {
                $('#i_post_detail_' + postId).show();
                $('#i_post_edit_' + postId).hide();
                alert('Не удалось выполнить запрос');
            },
            success: function (data) {
                $('#i_post_edit_' + postId).html(data.html);
                $('#i_form_photos_container_form').html(data.uploadForm);
            }
        });
    },
    postDel: function (postId) {
        Sw.confirm.fire({
            text: "Вы действительно хотите удалить пост?",
            confirmButtonText: 'Удалить'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        action: 'postDel',
                        postId: postId
                    },
                    error: function () {
                        alert('Не удалось выполнить запрос');
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        })


    },
    postPublish: function (postId) {

        Sw.confirm.fire({
            text: "Вы действительно хотите опубликовать пост прямо сейчас?",
            confirmButtonText: 'Опубликовать',
            cancelButtonText: 'Отмена',
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        action: 'postPublish',
                        postId: postId
                    },
                    error: function () {
                        alert('Не удалось выполнить запрос! Обратитесь пожалуйста в поддержку!');
                    },
                    success: function (response) {

                        console.log(response);
                        if (response.success === true) {

                            $('#i_post_detail_' + postId).remove();
                            Swal.fire({
                                icon: 'info',
                                title: 'Успешно',
                                text: 'Пост успешно размещен!',
                                timer: 1500,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })


                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ошибка!',
                                text: 'Не удалось разместить пост!',
                            })
                        }


                    }
                })

            }
        })

    },

    addPoll: function () {
        var index = $('#i_poll_example').data('index');
        index += 1;
        $('#i_poll_example').data('index', index);
        var html = $('#i_poll_example').html();
        var div = $('<div id="i_poll_containder_' + index + '"></div>').html(html).data('index', index);
        $('#submit_post' + posting.postId).before(div);
        $(div).find('.c_poll_title').attr('name', 'poll[' + index + '][title]');
        $(div).find('.c_poll_answer').data('index', index).attr('name', 'poll[' + index + '][answers][]').keyup(function (e) {
            post_edit.answerEdit($(this), e);
        });
    },

    addAnswer: function (button) {
        var html = $(button).parent().prev().html();

        var div = $('<div></div>').addClass('col-sm-12').html(html);
        $(button).parent().before(div);
        $('.c_poll_answer', $(button).parent().prev()).focus();
    },
    groupSettingsSave: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i_group_settings_form').serialize(),
            beforeSend: function () {
                $('#i_group_settings_progress').show();
            },
            complete: function () {
                $('#i_group_settings_progress').hide();
            },
            error: function () {
                $('#i_group_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос').show();
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    $('#i_group_settings_error').addClass('alert alert-danger').html(data.errorText).show();
                }
            }
        });
    },

    groupSettings: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getGroupSettingsFrom'
            },
            beforeSend: function () {
                $('#i_group_settings_progress').show();
                $('#i_group_settings_error').addClass('alert alert-info').html('Подождите, идет выполнение запроса');
                $('#i_group_settings').modal('show');
            },
            complete: function () {
                $('#i_group_settings_progress').hide();
            },
            success: function (data) {
                $('#i_group_settings_error').removeClass('alert').removeClass('alert-info').html('');
                if (data.success) {
                    $('#i_group_settings_data').html(data.html);
                } else {
                    $('#i_group_settings_error').addClass('alert alert-danger').html(data.errorText);
                }
            },
            error: function () {
                $('#i_group_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос. Попробуйте позднее');
            }
        });
    },

    sourceRemove: function (sourceId) {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'sourceRemove',
                sourceId: sourceId
            },
            error: function () {
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})

            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка!',
                        text: data.errorText,
                    })
                }
            }
        });
    },
    sourceRefresh: function (sourceId) {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'sourceRefresh',
                sourceId: sourceId
            },
            error: function () {
                Sw.toast.fire({icon: 'error', title: 'Не удалось выполнить запрос'})
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка!',
                        text: data.errorText,
                    })

                }
            }
        });
    },
    sourceSave: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i_settings_form').serialize(),
            beforeSend: function () {
                $('#i_source_settings_progress').show();
            },
            complete: function () {
                $('#i_source_settings_progress').hide();
            },
            error: function () {
                $('#i_source_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос').show();
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    $('#i_source_settings_error').addClass('alert alert-danger').html(data.errorText).show();
                }
            }
        });
    },
    sourcesEdit: function (sourceId) {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getSourceAddFrom',
                sourceId: sourceId
            },
            beforeSend: function () {
                $('#i_source_settings_progress').show();
                $('#i_source_settings_error').addClass('alert alert-info').html('Подождите, идет выполнение запроса');
                $('#i_source_settings').modal('show');
            },
            complete: function () {
                $('#i_source_settings_progress').hide();
            },
            success: function (data) {
                $('#i_source_settings_error').removeClass('alert').removeClass('alert-info').html('');
                if (data.success) {
                    $('#i_source_settings_data').html(data.html);
                } else {
                    $('#i_source_settings_error').addClass('alert alert-danger').html(data.errorText);
                }
            },
            error: function () {
                $('#i_source_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос. Попробуйте позднее');
            }
        });
    },
    sourcesAdd: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getSourceAddFrom'
            },
            beforeSend: function () {
                $('#i_source_settings_progress').show();
                $('#i_source_settings_error').addClass('alert alert-info').html('Подождите, идет выполнение запроса');
                $('#i_source_settings').modal('show');
            },
            complete: function () {
                $('#i_source_settings_progress').hide();
            },
            success: function (data) {
                $('#i_source_settings_error').removeClass('alert').removeClass('alert-info').html('');
                if (data.success) {
                    $('#i_source_settings_data').html(data.html);
                } else {
                    $('#i_source_settings_error').addClass('alert alert-danger').html(data.errorText);
                }
            },
            error: function () {
                $('#i_source_settings_error').addClass('alert alert-danger').html('Не удалось выполнить запрос. Попробуйте позднее');
            }
        });
    },

    templateAdd: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i-form-template-add').serialize(),
            beforeSend: function () {
                $('#i-templateAdd-result').html(grabber.wait);
            },
            success: function (data) {
                if (data.success) {
                    grabber.getTemplatesList();
                } else {
                    $('#i-templateAdd-result').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                }
            },
            error: function () {
                $('#i-templateAdd-result').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    },
    getTemplatesList: function () {
        $('.c-div-group-detail').hide();
        $('.btn-grabber').removeClass('btn-primary').addClass('btn-default');
        $('#i-btn-grabber-detail-list').removeClass('btn-default').addClass('btn-primary');
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getTemplateList',
                groupId: grabber.groupId
            },
            beforeSend: function () {
                $('#i_templates_list').html(grabber.wait).show();
            },
            error: function () {
                $('#i_templates_list').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function (data) {
                if (data.success) {
                    $('#i_templates_list').html(data.html);
                } else {
                    $('#i_templates_list').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                }
            }
        });
    },
    getTemplateAdd: function () {
        $('.c-div-group-detail').hide();

        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getTemplateFrom',
                groupId: grabber.groupId
            },
            beforeSend: function () {
                $('#i_template_add').html(grabber.wait).show();
            },
            error: function () {
                $('#i_template_add').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function (data) {
                if (data.success) {
                    $('.btn-grabber').removeClass('btn-primary').addClass('btn-default');
                    $('#i-btn-grabber-detail-add').removeClass('btn-default').addClass('btn-primary');
                    $('#i_template_add').html(data.html);
                } else {
                    $('#i_template_add').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                }
            }
        });

    },
    getGroupDetail: function (btn) {
        var groupId = $(btn).data('grabberGroupId');
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getGroupForm',
                groupId: groupId
            },
            beforeSend: function () {
                $('#i_dialog_group_detail_progress-' + groupId).show();
            },
            complete: function () {
                $('#i_dialog_group_detail_progress-' + groupId).hide();
            },
            error: function () {
                $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            },
            success: function (data) {
                if (data.success) {
                    grabber.groupId = groupId;
                    $('#i-group-detail-' + groupId).html(data.html);
                    grabber.getTemplatesList();
                } else {
                    $('#i-group-detail-' + groupId).html('<div class="alert alert-danger">' + data.errorText + '</div>');
                }
            }
        });
    },
    saveClick: function (take) {
        if (take === true) {
            $('#i_dialog_group_add_data > form').append('<input type="hidden" name="take" value="true" />');
        }

        var data = $('#i_dialog_group_add_data > form').serialize();
        $.ajax({
            type: "post",
            dataType: "json",
            data: data,
            beforeSend: function () {
                $('#i_dialog_group_add_error').hide();
                $('#i_dialog_group_add_progress').show();
            },
            complete: function () {
                $('#i_dialog_group_add_progress').hide();
            },
            error: function () {
                $('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
            },
            success: function (data) {
                if (data.success) {
                    if (data.reload) {
                        location.reload();
                        return;
                    }
                    $('#i_dialog_group_add_data').html(data.html).show();
                    if (data.token) {
                        $('#i_dialog_group_add_button_save').html('Сохранить');
                    } else {
                        $('#i_dialog_group_add_button_save').html('Добавить');
                    }
                } else {
                    $('#i_dialog_group_add_error').html('<div class="alert alert-danger">' + data.errorText + '</div>').show();
                }
            }
        });
    },
    getGroupsList: function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: 'getGroups',
                months: grabber.months,
                isFree: $('#i-isFree').val()
            },
            beforeSend: function () {
                $('#i_dialog_group_add_error').html('').hide();
                $('#i_dialog_group_add_progress').show();
            },
            complete: function () {
                $('#i_dialog_group_add_progress').hide();
            },
            error: function () {
                $('#i_dialog_group_add_error').html('<div class="alert alert-danger">Не удалось выполнит запрос.</div>').show();
            },
            success: function (data) {
                if (data.success) {
                    $('#i_dialog_group_add_error').html('').hide();
                    $('#i_dialog_group_add_data').html(data.html);
                    if (data.token) {
                        $('#i_dialog_group_add_button_save').html('Сохранить');
                    } else {
                        $('#i_dialog_group_add_button_save').html('Добавить');
                    }
                } else {
                    $('#i_dialog_group_add_error').html('<div class="alert alert-danger">' + data.errorText + '</div>').show();
                }
            }
        });
    },
    cancelEditPost: function () {
        location.reload();
    },

    addPost: function () {
        $('#i_text' + grabber.postId).html($('#post_field' + grabber.postId).html());

        var paramObj = {};
        $.each($('#i_post_form' + grabber.postId).serializeArray(), function (_, kv) {
            if (paramObj.hasOwnProperty(kv.name)) {
                paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                paramObj[kv.name].push(kv.value);
            } else {
                paramObj[kv.name] = kv.value;
            }
        });

        paramObj.videos = post_edit.videos_done;

        $.ajax({
            type: "post",
            dataType: "json",
            data: paramObj,
            complete: function () {
                location.reload();
            }
        });
        return false;
    },
    emojiAdd: function (code, postId) {
        if (postId) {
            $('#post_field_' + postId).focus();
            document.execCommand('insertHTML', null, ' <img src="/img/emoji/' + code + '.png" />');

            $('#post_field_' + postId).focus();
        } else {
            $('#post_field').focus();

            document.execCommand('insertHTML', null, ' <img src="/img/emoji/' + code + '.png" />');

            $('#post_field').focus();
        }
    }
};
$(document).ready(function () {
    grabber.init();
});
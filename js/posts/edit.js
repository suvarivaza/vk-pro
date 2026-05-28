var post_edit = {
    videos: {},
    selected_videos: [],
    videos_done: [],
    postId: '',
    init: function()
    {
        $('#i_textarea_text').click(function(){
            post_edit.text_change();
        }).change(function(){
            post_edit.text_change();
        }).keyup(function(){
            post_edit.text_change();
        });
        $('#i_form_photos').on('hidden.bs.modal', function(){
            post_edit.load_attachments();
        });

        $('#i_form_video').on('hidden.bs.modal', function(){
            post_edit.load_attachments();
        });

        $('.datepicker').datetimepicker({
            format: 'dd.mm.yyyy',
            startDate: new Date(),
            minView: 2,
            language: 'ru'
        });
        
        $('#i_button_video_search').click(function(){
            post_edit.video_search();
        });

        post_edit.load_attachments();
        refreshLabels();
    },
    formSubmit: function()
    {
        var urls = [];
        $('.c_link_add_input').each(function(){
            urls.push($(this).val());
        });

        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: $('#i_post_action').val(),
                uuid: $('#i_post_uuid').val(),
                postDate: $("#i_post_date").val(),
                hour: $('#i_hour option:selected').val(),
                minute: $('#i_minute option:selected').val(),
                text: $('#i_textarea_text').val(),
                urls: urls,
                videos: post_edit.videos_done
            },
            success: function(data)
            {
                if (data.success)
                {
                    location.href = data.href;
                }
                else
                {
                    alert(data.errorText);
                }
            },
            error: function()
            {
                alert('Не удалось добавить пост. Попробуйте позднее.');
            },
            beforeSend: function()
            {
                $('#i_form_progress').modal('show');
            },
            complete: function()
            {
                $('#i_form_progress').modal('hide');
            }
        });
    },
    video_add: function(button)
    {
        var videoId = $(button).data('videoId');
        var index = post_edit.selected_videos.indexOf(videoId);
        if ( index > -1)
        {
            for (i in post_edit.videos_done)
            {
                if (post_edit.videos_done[i].id == videoId)
                {
                    post_edit.videos_done.splice(i, 1);
                    break;
                }
            }
            post_edit.selected_videos.splice(index, 1);
            $(button).html('<span class="glyphicon glyphicon-plus-sign" style="font-size: 18px;"></span>');
        }
        else
        {
            for (i in post_edit.videos)
            {
                if (post_edit.videos[i].id == videoId)
                {
                    post_edit.videos_done.push(post_edit.videos[i]);
                    break;
                }
            }
            post_edit.selected_videos.push(videoId);
            $(button).html('<span class="glyphicon glyphicon-ok" style="font-size: 18px;"></span>');
        }
    },
    video_del: function(videoId)
    {
        for (i in post_edit.videos_done)
        {
            if (post_edit.videos_done[i].id == videoId)
            {
                post_edit.videos_done.splice(i, 1);
                break;
            }
        }
    },
    video_search: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "video_search",
                query: $('#i_video_search_query').val()
            },
            success: function(data){
                if (data.success)
                {
                    $('#i_form_video_data').html(data.html);
                }
                else
                {
                    $('#i_form_video_data').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                }
            },
            error: function(){
                $('#i_form_video_data').html('<div class="alert alert-danger">Не удалось выполнить поиск видео. Попробуйте позднее</div>');
            },
            beforeSend: function(){
                $('#i_form_video_progress').show();
            },
            complete: function(){
                $('#i_form_video_progress').hide();
            }

        })
    },

    load_attachments: function()
    {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "load_attachments",
                uuid: $('#i_post_uuid' + post_edit.postId).val(),
                postId: post_edit.postId,
                videoId: post_edit.videos_done
            },
            beforeSend: function () {
                $('#i_form_attachments_progress' + post_edit.postId).show();
                $('#i_div_attachments' + post_edit.postId).hide();
            },
            success: function (data) {
                if (data.success)
                {
                    $('#i_div_attachments' + post_edit.postId).html(data.html);
                }
                else
                {
                    $('#i_div_attachments' + post_edit.postId).html('<div class="alert alert-danger">'+data.errorText+'</div>');
                }
            },
            error: function () {
                $('#i_div_attachments + post_edit.postId').html('<div class="alert alert-danger">Не удалось загрузить вложения</div>');
            },
            complete: function()
            {
                $('#i_form_attachments_progress' + post_edit.postId).hide();
                $('#i_div_attachments' + post_edit.postId).show();
            }
        });
    },
    nl2br: function  (str) {
        var breakTag = '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
    },
    text_change: function ()
    {
        var html = post_edit.nl2br($('#i_textarea_text').val());
        $('#i_div_text').html(html);
        var height = $('#i_div_text').height() + 40;
        height = Math.max(height, 80);
        $('#i_textarea_text').css({height: height});
    }
};
$(document).ready(function(){
    post_edit.init();
});
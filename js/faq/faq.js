var faq = {
    init: function()
    {
        $('#i_dialog_question_dome').click(function () {
            faq.questionAdd();
        });

        $('#i_dialog_question').on('hidden.bs.modal', function(){
            location.reload();
        });

        $('.c-button-answer').click(function(){
            var button = $(this);
            $.ajax({
                type    : "post",
                dataType: "json",
                data    : $(this).parent().parent().parent().serialize(),
                beforeSend: function()
                {
                    $(button).prop('disable', true);
                },
                success : function ( data )
                {
                    if (data.success)
                    {
                        location.reload();
                    }
                    else
                    {
                        $(button).html('Не удалось отправить вопрос');
                    }
                },
                error: function()
                {
                    $(button).html('Не удалось отправить вопрос');
                }
            });
        });

        $('.c-button-done').click(function(){
            var button = $(this);
            $.ajax({
                type    : "post",
                dataType: "json",
                data    : {
                    action: 'done',
                    qId: $(button).data('qId')
                },
                beforeSend: function()
                {
                    $(button).prop('disable', true);
                },
                success : function ( data )
                {
                    if (data.success)
                    {
                        location.reload();
                    }
                    else
                    {
                        $(button).html('Не удалось отправить вопрос');
                    }
                },
                error: function()
                {
                    $(button).html('Не удалось отправить вопрос');
                }
            });
        });
    },
    questionAdd: function () {
        $.ajax({
            type    : "post",
            dataType: "json",
            data    : $('#i_dialog_question_form').serialize(),
            beforeSend: function()
            {
                $('#i_dialog_question_progress').show();
            },
            success : function ( data )
            {
                if (data.success)
                {
                    $('#i_dialog_question_form').hide();
                    $('#i_dialog_question_error').removeClass('alert alert-danger').addClass('alert alert-success').html(data.errorText);
                }
                else
                {
                    $('#i_dialog_question_error').removeClass('alert alert-success').addClass('alert alert-danger').html(data.errorText);
                }
            },
            complete: function(){
                $('#i_dialog_question_dome').hide();
                $('#i_dialog_question_progress').hide();
            },
            error: function()
            {
                $('#i_dialog_question_error').removeClass('alert alert-success').addClass('alert alert-danger').html('Не удалось выполнить запрос.');
            }
        });
    }
};

$(document).ready(function(){
    faq.init();
});
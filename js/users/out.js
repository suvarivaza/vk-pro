var out = {
    init: function()
    {
        $('#i-button-balanceRef-out').click(function(){
            $.ajax({
                type: "post",
                dataType: "json",
                data: {
                    action: 'outPrepare'
                },
                beforeSend: function()
                {
                    $('#i-dialog-balanceRef-container').html('');
                    $('#i-dialog-balanceRef-progress').show();
                    $('#i-dialog-balanceRef-save').hide();
                    $('#i-dialog-balanceRef').modal('show');
                },
                success: function(data)
                {
                    if (data.success)
                    {
                        $('#i-dialog-balanceRef-container').html(data.html);
                        $('#i-dialog-balanceRef-save').show();
                    }
                    else
                    {
                        $('#i-dialog-balanceRef-container').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    }
                },
                complete: function()
                {
                    $('#i-dialog-balanceRef-progress').hide();
                },
                error: function()
                {
                    $('#i-dialog-balanceRef-container').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                }
            });
        });
        $('#i-dialog-balanceRef-save').click(function(){
            $.ajax({
                type: "post",
                dataType: "json",
                data: $('#i-form-balanceRef-out').serialize(),
                beforeSend: function()
                {
                    $('#i-dialog-balanceRef-progress').show();
                    $('#i-dialog-balanceRef-save').hide();
                },
                complete: function()
                {
                    $('#i-dialog-balanceRef-progress').hide();
                    $('#i-dialog-balanceRef-save').show();
                },
                success: function(data)
                {
                    if (data.success)
                    {
                        $('#i-dialog-balanceRef-container').html('<div class="alert alert-info">Заявка создана.</div>');
                        location.reload();
                    }
                    else
                    {
                        $('#i-dialog-balanceRef-data').html('<div class="alert alert-danger">'+data.errorText+'</div>');
                    }
                },
                error: function()
                {
                    $('#i-dialog-balanceRef-data').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
                }
            })
        });
    }
};

$(document).ready(function(){
    out.init();
});
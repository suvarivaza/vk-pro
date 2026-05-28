var messages = {

    messages: null,

    init: function(){
        messages.showMessages();
    },
    showMessages: function()
    {
        $.ajax({
            url: "/messages/ajax",
            type    : "post",
            dataType: "json",
            data    : {
                action: "get_messages"
            },
            success : function ( data )
            {
                messages.messages = data.messages;

                if (data.success)
                {
                    $('#i_div_messages_container').html(data.html);
                }
            },
            error: function()
            {

            }
        });
    },
    showMessageDetail: function(li)
    {
        var messageUserId = $(li).data('messageId');
        for (i in messages.messages)
        {
            if (messages.messages[i].messageUserId == messageUserId)
            {
                $('#i_dialog_container_label').html('Текст уведомления');
                $('#i_dialog_container_container').html(messages.messages[i].text);
            }
        }
        $('#i_dialog_container').modal('show');
    },
    hideMessage: function (li)
    {
        var messageUserId = $(li).data('messageId');
        $('#i_li_' + messageUserId).remove();
        $.ajax({
            url: "/messages/ajax",
            type    : "post",
            dataType: "json",
            data    : {
                action: "removeMessage",
                messageUserId: messageUserId
            },
            success : function ( data )
            {
                messages.showMessages();
            },
            error: function()
            {
                messages.showMessages();
            }
        });
    }
};
$(document).ready(function(){
    messages.init();
});
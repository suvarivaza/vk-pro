var login = {
    loggedin: false,
    form_login_show: function () {
        if (login.loggedin === true)
        {
            location.href = '/tasks/all';
            return true;
        }
        $('#i_login_container').hide();
        $('#i_form_login_bottom').hide();
        $('#i_ulogin_container').show();
        $('#i_form_login').modal('show');
    },
    init: function()
    {
        $('#i_form_login_button_login').click(function(){
            $.ajax( {
                type    : "post",
                dataType: "json",
                url: "/users/login",
                data    : {
                    action: "login",
                    email: $("#i_form_login_email" ).val(),
                    password: $("#i_form_login_password").val()
                },
                beforeSend: function()
                {
                    $('#i_form_login_progress').show();
                    $('#i_form_login_button_login').hide();
                    $('#i_form_login_button_register').hide();
                },
                success : function ( data )
                {
                    console.log('login', data)
                    if (data.success)
                    {
                        location.href = '/tasks/all';
                    }
                    else
                    {

                        $('#i_form_login_data').html(data.errorText).show();
                    }
                },
                complete: function(){
                    $('#i_form_login_progress').hide();
                    $('#i_form_login_button_login').show();
                    $('#i_form_login_button_register').show();
                },
                error: function()
                {
                    alert('Не удалось выполнить запрос. Обратитесь в техподдержку.');
                    $('#i_form_login').modal('hide');
                }
            } );
        });
    }
};

$(document).ready(function(){
    login.init();
});
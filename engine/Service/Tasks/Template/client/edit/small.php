<div id="i_task_check_div_container">
    <div class="alert alert-info text-center">
        <h4>Внимание!</h4>
        <h5>В Вашей группе слишком мало подписчиков.</h5>
        <h5>Скорость накрутки будет снижена, с целью не привлекать лишнее внимание администрации Вконтакте к Вашему
            сообществу, и <strong>не допустить</strong> списания подписчиков и/или блокировки сообщества.</h5>
        <h5>Подписчики будут поступать в Ваше сообщество с сервиса равномерно.</h5>
    </div>
    <div class="text-center">
        <button id="i_button_task_renew" class="btn btn-primary">Спасибо, всё понятно</button>
    </div>
</div>

<script type="text/javascript">
    $('#i_button_task_renew').click(function () {
        $('#i_form_task_add_action').val('<?= $vars['action'] ?? 'add'; ?>');
        $('#i_form_task_add').submit();
        $('#i_task_check_div_container').html('');
        $('#i_dialog_task_check_error').removeClass('alert alert-danger').html('');
        $('#i_dialog_task_check_progress').show();
    });

    $('#i_buton_task_cancel').click(function () {
        $('#i_dialog_task_check').modal('hide');
    });
</script>
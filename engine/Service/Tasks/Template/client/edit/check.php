<?php

?>
<div id="i_task_check_div_container">
    <div class="alert alert-info text-center">
        <h4>У Вас уже есть такое задание ID:<?= $vars['taskId'] ?></h4>
        <h5>Для исключения дубликатов выполнения заданий одними и теми же иполнителями рекомендуем обновить предыдущее
            задание.</h5>
    </div>
    <div class="pull-right">
        <button id="i_buton_task_cancel" class="btn btn-danger ">Отменить</button>
        <button id="i_button_task_renew" class="btn btn-primary">Обновить существующее задание</button>
    </div>
</div>

<script type="text/javascript">
    $('#i_button_task_renew').click(function () {
        $('#i_form_task_add_action').val('<?= $vars['action'] ?? 'add'; ?>');
        $('#i_form_task_add_taskId').val('<?= $vars['taskId']; ?>');
        $('#i_form_task_add').submit();
        $('#i_task_check_div_container').html('');
        $('#i_dialog_task_check_error').removeClass('alert alert-danger').html('');
        $('#i_dialog_task_check_progress').show();
    });

    $('#i_buton_task_cancel').click(function () {
        $('#i_dialog_task_check').modal('hide');
    });
</script>
<h4>Текст комментария <small>(скопируйте и вставьте на странице задания)</small></h4>
<div class="input-group">
    <input id="i-input-comment-<?= $vars['taskId']; ?>" class="form-control" value="<?= $vars['comment']; ?>">
    <span class="input-group-btn">
        <button data-task-id="<?= $vars['taskId']; ?>" id="i-button-comment-copy-<?= $vars['taskId']; ?>"
                class="btn btn-default" type="button">Копировать</button>
      </span>
</div><!-- /input-group -->

<div class="text-right" style="margin-top: 20px;">
    <button id="i-button-comment-close" class="btn btn-default">Отмена</button>
    <a onclick="ws.taskId = <?= $vars['taskId']; ?>" target="_blank" href="/tasks/go?taskId=<?= $vars['taskId']; ?>"
       class="btn btn-primary">Оставить комментарий</a>
</div>
<script>
    $('#i-button-comment-copy-<?= $vars['taskId']; ?>').click(function () {
        var target = $('#i-input-comment-' + $(this).data('taskId'))[0];
        target.focus();
        target.setSelectionRange(0, target.value.length);
        var succeed;
        try {
            succeed = document.execCommand("copy");
        } catch (e) {
            succeed = false;
        }
        if (succeed) {
            $(this).removeClass('btn-default').addClass('btn-success').html('Скопировано');
        }
    });
    $('#i-button-comment-close').click(function () {
        $('#i-div-task-detail-<?= $vars['taskId']; ?>').slideUp(400, function () {
            $(this).html('');
        });
    });
</script>
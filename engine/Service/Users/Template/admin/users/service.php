<input type="hidden" name="action" value="activateService"/>
<input type="hidden" name="service" value="<?= $vars['service']; ?>"/>
<input type="hidden" name="userId" value="<?= $vars['userId']; ?>"/>
<div class="form-group">
    <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="col-sm-3">
            <label><input type="radio" name="month"
                          value="<?= $i; ?>"><?= \Lib_Text::Word4NumberNewReturn($vars['settings'][$vars['service']]['months'][$i],
                    ['месяц', 'месяца', 'месяцев']); ?></label>
        </div>
    <?php endfor; ?>
</div>
<script type="text/javascript">
    $('#i_modal_activate_label').html('Добавить слот для <?= $vars['title']; ?>');
</script>
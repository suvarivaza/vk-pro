<?php
/** @var \Service\Tasks\Model_Specials_Groups_Group $group */
$group = $vars['group'];
?>
<div>
    Данная функция позволяет обнаружить собак, обновить данные по подписчикам и вернуть баллы за отписавшихся.
</div>
<?php if (time() - $group->lastUpdate < 86400): ?>
    <div class="alert alert-warning">
        Обновлять данные можно не чаще, чем раз в сутки
    </div>
<?php else: ?>
    <form>
        <input type="hidden" name="action" value="update"/>
        <div class="text-center">
            <button type="button" class="btn btn-primary">Обновить данные по подписчикам</button>
        </div>
    </form>
<?php endif; ?>

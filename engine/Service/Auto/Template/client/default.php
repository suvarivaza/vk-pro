<?php
/** @var Model_Autos_Auto $auto */
use Service\Auto\Model_Autos_Auto;
use Service\Auto\Model_Autos_Groups_Group;

$auto = $vars['auto'];
$slots = $auto->getSlots();
$list = [];

foreach ($slots as $months) {
    if (!isset($list[$months])) {
        $list[$months] = 1;
    } else {
        ++$list[$months];
    }
}
/** @var Model_Autos_Groups_Group[] $groups */
$groups = $vars['groups'];
?>
<div class="pull-right">
    <a href="/faq/list/6" target="_blank" style="padding: 7px; display: block;" title="Помощь по сервису">
        <img src="/img/icons/32/icon-help.png" width="32"/>
    </a>
</div>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-auto.png" width="30"/>
        <a href="/auto">Автоведение</a>
    </li>
</ul>
<?php foreach ($list as $months => $count): ?>
    <div class="alert alert-info">
        <div class="pull-right">
            <button class="button-green c_auto_slot_active" data-months="<?= $months; ?>"
                    id="i_auto_slot_active_<?= $months; ?>">Активировать слот
            </button>
        </div>
        <h5>У вас есть <?= Lib_Text::Word4NumberNewReturn($count, ['слот', 'слота', 'слотов']); ?>
            на <?= Lib_Text::Word4NumberNewReturn($months, ['месяц', 'месяца', 'месяцев']); ?> для активации
        </h5>
    </div>
<?php endforeach; ?>

<?php foreach ($groups as $group): ?>
    <div class="c-task-detail c-auto-group" data-auto-group-id="<?= $group->autoGroupId; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60px;">
                    <img class="img-thumbnail" src="<?= $group->photo; ?>" style="width: 50px; height: 50px;"/>
                </td>
                <td>
                    <h5<?php if ($group->dateValid < time()): ?> style="color: #d2d2d2;"<?php endif; ?>><?= $group->title; ?></h5>
                </td>
                <td style="width: 200px;">
                    <?php if ($group->isFree): ?>
                        Осталось постов: <?= $group->isFreeCount; ?><br/>
                        Истекает: <strong><?= date('d.m.Y H:i', $group->dateValid); ?></strong>
                    <?php else: ?>
                        <?php if ($group->dateValid > time()): ?>Истекает<?php else: ?>Истекло<?php endif; ?>: <br/>
                        <strong<?php if ($group->dateValid < time()): ?> style="color: red;"<?php endif; ?>><?= date('d.m.Y H:i',
                                $group->dateValid); ?></strong>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>

<div id="i_auto_group_add" class="row c-task-detail c-auto-group-add">
    <div class="col-sm-2">
        <img src="https://vk.com/images/community_100.png" class="img-thumbnail"/>
    </div>
    <div class="col-sm-8">
        <h3>Добавить группу для автоведения</h3>
    </div>
</div>
<div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в автоведение</h5>
            </div>
            <div class="modal-body" id="i_dialog_group_add_container">
                <div id="i_dialog_group_add_data"></div>
                <div id="i_dialog_group_add_error"></div>
                <div id="i_dialog_group_add_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_group_add_button_cancel" type="button" class="btn btn-default"
                        data-dismiss="modal">Отмена
                </button>
            </div>
        </div>
    </div>
</div>
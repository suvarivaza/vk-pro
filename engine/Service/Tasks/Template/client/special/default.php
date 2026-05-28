<?php
/** @var \Service\Tasks\Model_Specials_Special $special */
$special = $vars['special'];
$slots = $special->getSlots();
/** @var \Service\Tasks\Model_Specials_Groups_Group[] $groups */
$groups = $vars['groups'];
$list = [];

foreach ($slots as $months) {
    if (!isset($list[$months])) {
        $list[$months] = 1;
    } else {
        ++$list[$months];
    }
}
?>
<div class="pull-right">
    <a href="/faq/list/9" target="_blank" style="padding: 7px; display: block;" title="Помощь по сервису Спецзадания">
        <img src="/img/icons/32/icon-help.png" width="32"/>
    </a>
</div>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-special.png" width="30"/>
        <a href="/tasks/special">Спецзадания</a>
    </li>
</ul>
<?php foreach ($list as $months => $count): ?>
    <div class="alert alert-info">
        <div class="pull-right">
            <button class="button-green c_special_slot_active" data-months="<?= $months; ?>"
                    id="i_special_slot_active_<?= $months; ?>">Активировать слот
            </button>
        </div>
        <h5>У вас есть <?= \Lib_Text::Word4NumberNewReturn($count, ['слот', 'слота', 'слотов']); ?>
            на <?= \Lib_Text::Word4NumberNewReturn($months, ['месяц', 'месяца', 'месяцев']); ?> для активации
        </h5>
    </div>
<?php endforeach; ?>
<?php foreach ($groups as $group): ?>
    <div class="c-task-detail c-special-group" data-group-id="<?= $group->groupId; ?>">
        <table style="width: 100%;">
            <tr>
                <td style="width: 56px;">
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
        <div id="i_dialog_group_detail_progress-<?= $group->groupId; ?>" class="progress progress-striped active"
             style="display: none;">
            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                 style="width: 100%">
                <span class="sr-only">&nbsp;</span>
            </div>
        </div>
    </div>
    <div id="i-group-detail-<?= $group->groupId; ?>"></div>
<?php endforeach; ?>

<div id="i_special_group_add" class="row c-task-detail c-special-group-add">
    <div class="col-sm-2">
        <img src="https://vk.com/images/community_50.png" class="img-thumbnail"/>
    </div>
    <div class="col-sm-10">
        <h3>Добавить группу для Спецзадания</h3>
    </div>
</div>
<div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в Спецзадания</h5>
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
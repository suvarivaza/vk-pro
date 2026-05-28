<?php
/** @var Model_Autos_Auto $auto */
$auto = $vars['auto'];
/** @var Model_Autos_Groups_Group[] $groups */
$groups = $vars['groups'];

use Service\Auto\Model_Autos_Auto;
use Service\Auto\Model_Autos_Groups_Group;

?>
<?php STPL::Fetch('controls/short'); ?>
<h2>
    Автоведение
    <small class="pull-right">
        <a class="btn btn-primary btn-block" href="/auto/settings">Настройки автоведения</a>
    </small>
</h2>
<div class="c-task-my-detail">
    <div class="row">
        <div class="col-sm-6">
            Дата истечения: <strong><?= date('d.m.Y H:i', $auto->dateValid); ?></strong>

        </div>
        <div class="col-sm-6 text-right">
            Осталось: <strong><?= ceil(($auto->dateValid - time()) / 86400); ?></strong> дней
        </div>
    </div>
    <?php if ($remain < 30): ?>
        <div class="row">
            <div class="col-sm-12">
                Рекомендуем
                <button id="i_button_extend" class="btn btn-primary" href="/auto/buy">Продлить</button>
            </div>
        </div>
    <?php endif; ?>
    <div id="i_row_extend" style="display: none;">
        <form id="i_form_extend">
            <div class="row">
                <div class="col-sm-12">
                    Продлить на:
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <label><input type="radio" name="srok" value="1"/> 1 месяц</label>
                </div>
                <div class="col-sm-3">
                    <label><input type="radio" name="srok" value="2"/> 2 месяца</label>
                </div>
                <div class="col-sm-3">
                    <label><input type="radio" name="srok" value="3"/> 3 месяца</label>
                </div>
                <div class="col-sm-3">
                    <label><input type="radio" name="srok" value="6"/> 6 месяцев</label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    <button class="btn btn-primary">Оплатить</button>
                </div>
            </div>
            <div id="i_form_extend_result">

            </div>
        </form>
    </div>
</div>

<div class="c-task-my-detail">
    <h4 class="text-center">Текущие проекты</h4>
    <?php foreach ($groups as $group): ?>
        <div class="row c_group_item">
            <div class="col-sm-2">
                <img class="img-thumbnail" src="<?= $group->photo; ?>"/>
            </div>
            <div class="col-sm-8">
                <?= $group->title; ?>
            </div>
            <div class="col-sm-2">
                Истекает: <strong><?= date('d.m.Y H:i', $group->dateValid); ?></strong>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="row c_group_item">
        <div class="col-sm-12 text-center">
            <a href="/auto/group/add" class="btn btn-default" id="i_auto_project_add">
                <span class="glyphicon glyphicon-plus-sign"></span>
                Добавить проект
            </a>
        </div>
    </div>
</div>
<?php
/** @var \Service\Posting\Model_Groups_Group $group */
$group = $vars['group'];
/** @var \Service\Grabber\Model_Grabbers_Grabber $grabber */
$grabber = $vars['grabber'];

$days = ceil($grabber->interval / 24);
$hour = $grabber->interval - ($days * 24);
?>
<ul class="breadcrumb">
    <li><a href="/grabber">Граббер</a></li>
    <li><a href="/grabber/<?= $group->groupId; ?>"><?= $group->title; ?></a></li>
    <li><?php if ($vars['action'] == 'add'): ?>Добавление<?php else: ?>Редактирование<?php endif; ?> задания</li>
</ul>
<form method="post" class="form-horizontal" role="form">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <?php if ($vars['errors']): ?>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="alert alert-danger"><?= implode('<br />', $vars['errors']); ?></div>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="i_url" class="form-control form-span" name="source"
                   placeholder="Укажите ссылку на целевую группу" value="<?= $grabber->source; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-12 control-label" style="text-align: left;">Укажите, через какой интервал публиковать пост
            в группе</label>
        <div class="col-sm-6">
            <div class="input-group">
                <select name="days" class="form-control">
                    <option value="0">0</option>
                    <?php for ($i = 1; $i <= 30; $i++): ?>
                        <option value="<?= $i; ?>"<?php if ($days == $i): ?> selected="selected"<?php endif; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
                <div class="input-group-addon">дней</div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="input-group">
                <select class="form-control">
                    <option value="0">0</option>
                    <?php for ($i = 1; $i <= 24; $i++): ?>
                        <option value="<?= $i; ?>"<?php if ($hour == $i): ?> selected="selected"<?php endif; ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
                <div class="input-group-addon">часов</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="linkDelete"
                           <?php if ($grabber->linkDelete): ?>checked="checked"<?php endif; ?> /> Удалять ссылки из
                    поста
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input class="form-control form-span" id="i_hashtags" name="hashtags"
                   placeholder="Укажите хэштеги через пробел"
                   value="<?= \Lib_Html::ChangeQuotes($grabber->hashtags); ?>"/>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
            <button type="button" class="btn btn-danger" onclick="history.back();">Отмена</button>
        </div>
    </div>
</form>

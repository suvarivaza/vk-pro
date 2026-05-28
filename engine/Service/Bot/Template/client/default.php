<?php
$bot = $vars['bot'];
$types = $vars['types'];
$fields = $vars['fields'];
?>
<?php if ($bot->isActive): ?>
    <div class="alert alert-success">
        <h3 class="text-center">Автоматическое выполнение заданий включено</h3>
        <div class="text-center">
            <a class="btn btn-danger" href="?isBot=1">
                <span class="glyphicon glyphicon-off"></span>
                Выключить
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <h3 class="text-center">Автоматическое выполнение заданий выключено</h3>
        <div class="text-center">
            <a class="button-green" href="?isBot=1">
                <span class="glyphicon glyphicon-off"></span>
                Включить
            </a>
        </div>
    </div>
<?php endif; ?>
<?php if ($bot->isPro): ?>
<div class="alert alert-info text-center">
    <h4>Срок действия платной вресии Автобота истекает <strong><?= date('d.m.Y', $bot->dateValid); ?></strong></h4>
    <div>
        <button class="button-green" id="i_auto_button_add">Продлить PRO-версию</button>
    </div>
</div>
<form method="post">
    <input type="hidden" name="action" value="saveIsBot" />
    <?php elseif ($bot->dateValid > 0): ?>
        <div class="alert alert-danger text-center">
            <h4>Срок действия платной вресии Автобота истек <strong><?= date('d.m.Y', $bot->dateValid); ?></strong></h4>
            <div>
                <button class="button-green" id="i_auto_button_add">Продлить PRO-версию</button>
            </div>
        </div>
    <?php endif; ?>
    <table class="table">
        <tr>
            <td colspan="2"></td>
            <td class="text-right">Выполнено</td>
        </tr>
        <?php foreach ($types as $id => $title): ?>
            <tr>
                <td><?= $fields[$title]['title']; ?></td>
                <td>
                    <?php
//                    var_dump(sprintf('%08b', $id));
//                    var_dump(sprintf('%08b', $bot->isBot));
//                    var_dump([$id , $bot->isBot]);
//                    var_dump(($id & $bot->isBot));
                    ?>
                    <div class="material-switch">
                        <input
                            <?php if (!$bot->isPro): ?>disabled="disabled"<?php endif; ?>
                            id="i-<?= $id; ?>"
                            type="checkbox"
                            name="isBot[]"
                            value="<?= $id; ?>"
                            <?php if ($id & $bot->isBot): ?>checked="checked"<?php endif; ?>>
                        <label for="i-<?= $id; ?>" class="label-primary">
                        </label>
                    </div>
                </td>
                <td class="text-right">
                    <?= $vars['tasks']['total'][$title] ?? 0; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="text-center">
        <?php if ($bot->isPro): ?>
            <button class="btn btn-success" type="submit" name="save">Сохранить</button>
        <?php else: ?>
            Для выбора типов выполняемых заданий необходимо <button class="btn btn-success btn-lg" id="i_auto_button_add">приобрести версию PRO</button>
        <?php endif; ?>
    </div>
    <?php if ($bot->isPro): ?>
</form>
<?php endif; ?>
<?php if ($bot->isPro): ?>
    <h3>За сегодняшний день</h3>
<ul class="list-group">
    <li class="list-group-item">Выполнено заданий:
        <div class="pull-right">
            <strong style="font-size: 1.5em;"><?= $vars['total']; ?></strong>
        </div>
    </li>
    <li class="list-group-item">
        Заработано баллов:
        <div class="pull-right">
            <strong style="font-size: 1.5em;"><?= $vars['totalBalance']; ?></strong>
        </div>
    </li>
    <li class="list-group-item">Получено кармы:
        <div class="pull-right">
            <strong style="font-size: 1.5em;">+<?= $vars['totalKarma']; ?></strong>
        </div>
    </li>
</ul>
<?php endif; ?>
<div class="modal fade" id="i_dialog_auto_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_auto_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="i_dialog_auto_add_label">Приобрести автобот PRO</h5>
            </div>
            <div class="modal-body" id="i_dialog_auto_add_container">
                <form action="/orders/order" method="post">
                    <input type="hidden" name="service" value="bot" />
                    <div id="i_dialog_auto_add_data">
                        <div class="form-group">
                            <ul class="c-bot-list pro">
                                <li class="icon-check">До <strong>1000</strong> баллов в день</li>
                                <li class="icon-check"><strong>Можно</strong> указать типы выполняемых заданий</li>
                                <li class="icon-check">Работает при любых балансах и карме</li>
                                <li class="icon-check">Ежедневный отчет о проделанной работе</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="col-sm-3">
                                    <label><input type="radio" name="month" value="<?= $i; ?>"><?= \Lib_Text::Word4NumberNewReturn($vars['settings']['bot']['months'][$i], ['месяц', 'месяца', 'месяцев']); ?></label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div id="i_dialog_auto_add_error"></div>
                    <div id="i_dialog_auto_add_progress" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
<!--                    <input name="shopId"         value="--><?//= shopId; ?><!--"          type="hidden" />-->
<!--                    <input name="scid"           value="--><?//= scid; ?><!--"            type="hidden" />-->
                    <input name="customerNumber" value="<?= $vars['userId']; ?>" type="hidden" />
                    <input id="i-sum" name="sum"            value="0"    type="hidden" />
                    <button id="i-button-buy" type="submit" class="button-green btn-block" style="display: none;">Купить за <span id="i-span-sum"></span> рублей</button>
                </form>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_auto_add_button_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var settings = <?= json_encode($vars['settings']['bot'], JSON_UNESCAPED_UNICODE); ?>;
    $('input[name="month"]').change(function(){
        var val = $('input[name="month"]:checked').val();

        var html = '';
        var profit =  (settings.groups[0] * settings.months[val]) - settings.groups[val];
        if (profit)
        {
            html = '<h3 class="alert alert-success text-center">Выгода: '+profit+' рублей</h3>';
        }
        $('#i_dialog_auto_add_error').html('<div class="alert alert-info"><h2 class="text-center">' + settings.groups[val] + ' рублей</h2>'+html+'</div>');

        $('#i-sum').val(settings.groups[val]);
        $('#i-span-sum').html(settings.groups[val]);
        $('#i-button-buy').show();
    });
    $('#i_auto_button_add').click(function(){
        $('#i_dialog_auto_add').modal();
    });
</script>
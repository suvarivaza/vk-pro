<?php
/** @var \Service\Orders\Model_Orders_Packs_Pack $pack */
$pack = $vars['pack'];
?>
<h1><?= $vars['title']; ?></h1>
<form action="" method="post" class="form-horizontal">
    <input type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <div class="form-group">
        <label class="col-sm-2 control-label">Наименование</label>
        <div class="col-sm-6">
            <input class="form-control" name="title" value="<?= $pack->title; ?>"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Баланс</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input class="form-control" name="balance" value="<?= $pack->balance; ?>"/>
                <span class="input-group-addon">баллов</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Бонус</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input class="form-control" name="bonus" value="<?= $pack->bonus; ?>"/>
                <span class="input-group-addon">баллов</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Стоимость</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input class="form-control" name="price" value="<?= $pack->price; ?>"/>
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Реферальная система</label>
        <div class="col-sm-2">
            <div class="material-switch">
                <input id="i-isReferrer" type="checkbox" name="isReferrer"
                       value="1"<?php if ($pack->isReferrer): ?> checked="checked"<?php endif; ?>>
                <label for="i-isReferrer" class="label-primary">
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Все улуги</label>
        <div class="col-sm-2">
            <div class="material-switch">
                <input id="i-serviceAll" type="checkbox" name="serviceAll"
                       value="1"<?php if ($pack->serviceAll): ?> checked="checked"<?php endif; ?>>
                <label for="i-serviceAll" class="label-primary">
                </label>
            </div>
        </div>
    </div>

    <div class="form-group" id="i-serviceCount-container">
        <label class="col-sm-2 control-label">Включенние услуг</label>
        <div class="col-sm-6">
            <select class="form-control" name="serviceCount">
                <option value="0">Без услуг</option>
                <option value="1"<?php if ($pack->serviceCount == 1): ?> selected="selected"<?php endif; ?>>Одна услуга
                    на выбор
                </option>
                <option value="2"<?php if ($pack->serviceCount == 2): ?> selected="selected"<?php endif; ?>>Две услуги
                    на выбор
                </option>
                <option value="3"<?php if ($pack->serviceCount == 3): ?> selected="selected"<?php endif; ?>>Три услуги
                    на выбор
                </option>
                <option value="4"<?php if ($pack->serviceCount == 4): ?> selected="selected"<?php endif; ?>>Четыре
                    услуги на выбор
                </option>
            </select>
        </div>
    </div>

    <div class="form-group" id="i-serviceMonth-container">
        <label class="col-sm-2 control-label">На срок</label>
        <div class="col-sm-6">
            <select class="form-control" name="serviceMonth">
                <option value="1">Месяц</option>
                <option value="2">Два месяца</option>
                <option value="3">Три месяца</option>
                <option value="4">Четыре месяца</option>
                <option value="5">Пять месяцев</option>
                <option value="6">Полгода</option>
                <option value="12">Год</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-2">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <button class="btn btn-danger" type="button" onclick="history.back();">Отмена</button>
        </div>
    </div>
</form>
<script>
    $('#i-serviceAll').change(function () {
        var prop = $(this).prop('checked');
        if (prop) {
            $('#i-serviceCount-container').hide();
        } else {
            $('#i-serviceCount-container').show();
        }
    });
    $(document).ready(function () {
        var prop = $('#i-serviceAll').prop('checked');

        if (prop) {
            $('#i-serviceCount-container').hide();
        } else {
            $('#i-serviceCount-container').show();
        }
    });
</script>
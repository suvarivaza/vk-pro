<h3>
    Цены на сервисы
</h3>
<form class="form-horizontal" method="post">
    <div class="form-group">
        <input type="hidden" name="balance[title]" value="<?= $vars['settings']['balance']['title']; ?>"/>
        <label class="col-sm-3 control-label">Стоимость 10 баллов</label>
        <div class="col-sm-9">
            <div class="input-group">
                <input class="form-control" name="balance[price]"
                       value="<?= $vars['settings']['balance']['price']; ?>"/>
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="karma1[title]"
               value="<?= $vars['settings']['karma1']['title'] ?: 'Очистка кармы'; ?>"/>
        <label class="col-sm-3 control-label">Очистка кармы впервые</label>
        <div class="col-sm-9">
            <div class="input-group">
                <input class="form-control" name="karma1[price]" value="<?= $vars['settings']['karma1']['price']; ?>"/>
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="karma2[title]"
               value="<?= $vars['settings']['karma2']['title'] ?: 'Очистка кармы'; ?>"/>
        <label class="col-sm-3 control-label">Очистка кармы второй раз</label>
        <div class="col-sm-9">
            <div class="input-group">
                <input class="form-control" name="karma2[price]" value="<?= $vars['settings']['karma2']['price']; ?>"/>
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="karma3[title]"
               value="<?= $vars['settings']['karma3']['title'] ?: 'Очистка кармы'; ?>"/>
        <label class="col-sm-3 control-label">Очистка кармы третий и более раз</label>
        <div class="col-sm-9">
            <div class="input-group">
                <input class="form-control" name="karma3[price]" value="<?= $vars['settings']['karma3']['price']; ?>"/>
                <span class="input-group-addon">рублей</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
    <?php foreach ($vars['settings'] as $key => $service) : if (in_array($key,
        ['balance', 'karma', 'karma1', 'karma2', 'karma3'])) {
    continue;
} ?>
        <h2>
            <?= $service['title']; ?><input type="hidden" name="<?= $key; ?>[title]" value="<?= $service['title']; ?>">
            <small class="pull-right" style="width: 50%;">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Бесплатно</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input class="form-control" name="<?= $key; ?>[free]" value="<?= $service['free']; ?>"/>
                            <span class="input-group-addon"><?= $key == 'special' ? 'заданий' : 'постов'; ?></span>
                        </div>
                    </div>
                </div>
            </small>
        </h2>
        <div class="form-group">
            <label class="col-sm-3 control-label">Срок</label>
            <label class="col-sm-3 control-label">Стоимость</label>
            <label class="col-sm-3 control-label">Кол-во слотов</label>
            <label class="col-sm-3 control-label">Стомость слота</label>
        </div>
        <?php for ($i = 0; $i < 4; $i++) : ?>
            <div class="form-group">
                <div class="col-sm-3">
                    <select class="form-control" name="<?= $key; ?>[months][]">
                        <option value="">-- Укажите --</option>
                        <?php foreach ($vars['months'] as $month => $title): ?>
                            <option value="<?= $month; ?>"<?php if ($service['months'][$i] == $month): ?> selected="selected"<?php endif; ?>><?= $title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input class="form-control" name="<?= $key; ?>[prices][<?= $i; ?>]"
                           value="<?= $service['prices'][$i]; ?>">
                </div>
                <div class="col-sm-3">
                    <input class="form-control" name="<?= $key; ?>[limits][<?= $i; ?>]"
                           value="<?= $service['limits'][$i]; ?>">
                </div>
                <div class="col-sm-3">
                    <input class="form-control" name="<?= $key; ?>[groups][<?= $i; ?>]"
                           value="<?= $service['groups'][$i]; ?>">
                </div>
            </div>
        <?php endfor; ?>
    <?php endforeach; ?>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
        </div>
    </div>
</form>
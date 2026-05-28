<h1>
    Таргетинг
    <small class="pull-right">
        <a class="btn btn-primary" href="/admin/tasks/prices">Цены на задания</a>
    </small>
</h1>
<form method="post">
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Наименование</th>
            <th>Наценка</th>
            <th></th>
        </tr>

        <tr>
            <td>Задание в начале списка</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="percent_prior" value="<?= $vars['settings']['percent_prior']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>

        <tr>
            <td><strong>Минимальная карма</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['minKarma'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[minKarma][<?= $val; ?>]"
                               value="<?= $vars['percents']['minKarma'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td><strong>Пол</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['sex'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[sex][<?= $val; ?>]"
                               value="<?= $vars['percents']['sex'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Возраст, от</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['ageFrom'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[ageFrom][<?= $val; ?>]"
                               value="<?= $vars['percents']['ageFrom'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Возраст, до</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['ageTo'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[ageTo][<?= $val; ?>]"
                               value="<?= $vars['percents']['ageTo'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td>Страна</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="percent_country"
                           value="<?= $vars['settings']['percent_country']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Город</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="percent_city" value="<?= $vars['settings']['percent_city']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Свой город</td>
            <td>
                <div class="input-group">
                    <input class="form-control" name="percent_city_my"
                           value="<?= $vars['settings']['percent_city_my']; ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>Семеное положение</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['relation'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[relation][<?= $val; ?>]"
                               value="<?= $vars['percents']['relation'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Количество аватарок</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['avatarCount'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[avatarCount][<?= $val; ?>]"
                               value="<?= $vars['percents']['avatarCount'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Заполненность странички</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['filled'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[filled][<?= $val; ?>]"
                               value="<?= $vars['percents']['filled'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Возраст странички</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['pageAge'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[pageAge][<?= $val; ?>]"
                               value="<?= $vars['percents']['pageAge'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Количество друзей и подписчиков</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['followersCount'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[followersCount][<?= $val; ?>]"
                               value="<?= $vars['percents']['followersCount'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Количество интересных страниц</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['interestingPage'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[interestingPage][<?= $val; ?>]"
                               value="<?= $vars['percents']['interestingPage'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><strong>Частота постов на стене</strong></td>
            <td></td>
        </tr>
        <?php foreach (\Service\Tasks\Model_Config::$targeting['frequencyPost'] as $val => $title): ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $title; ?></td>
                <td>
                    <div class="input-group">
                        <input class="form-control" name="percent[frequencyPost][<?= $val; ?>]"
                               value="<?= $vars['percents']['frequencyPost'][$val]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button class="btn btn-primary btn-lg">Сохранить</button>
</form>
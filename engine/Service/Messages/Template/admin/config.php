<h1>Сообщения</h1>
<?php $config = $vars['config']; ?>

<form class="form-horizontal" method="post" enctype="multipart/form-data">
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <?php foreach ($config as $type => $conf): ?>
            <tr style="background-color: #5b7196; color: #ffffff;">
                <th colspan="2" class="text-left">
                    <input type="hidden" name="<?= $type; ?>[title]" value="<?= $conf['title']; ?>"/>
                    <h4>
                        <img src="<?= \Service\Messages\Model_Config::$icons[$type]; ?>" width="32"/>
                        &nbsp;
                        <?= $conf['title']; ?>
                    </h4>
                </th>
            </tr>
            <?php foreach ($conf['types'] as $name => $value): ?>
                <tr>
                    <td style="width: 230px;">
                        <input type="hidden" name="<?= $type; ?>[types][<?= $name; ?>][title]"
                               value="<?= $value['title']; ?>"/>
                        <label class="control-label"><?= $value['title']; ?></label>
                    </td>
                    <td>
                        <input class="form-control" type="text" name="<?= $type; ?>[types][<?= $name; ?>][text]"
                               value="<?= \Lib_Html::ChangeQuotes($value['text']); ?>"/>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="2">
                <button class="btn btn-primary btn-lg" type="submit">Сохранить</button>
            </td>
        </tr>
    </table>
</form>
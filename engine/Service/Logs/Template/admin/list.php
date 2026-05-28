<?php /** @var array $vars */
use Service\Logs\Model_Config;
use Service\Logs\Model_Logs_Log;


?>
<h1>Логи</h1>

<section class="main">
    <form class="form-inline" role="form" method="get">
        <div class="form-group">
            <label class="sr-only" for="objectId">ID объекта</label>
            <input type="text" class="form-control" id="objectId" name="objectId" placeholder="ID объекта"
                   value="<?= $vars['filter']['objectId']; ?>">
        </div>
        <div class="form-group">
            <label class="sr-only" for="userId">ID пользователя</label>
            <input type="text" class="form-control" id="userId" name="userId" placeholder="ID пользователя"
                   value="<?= $vars['filter']['userId']; ?>">
        </div>
        <div class="form-group">
            <label class="sr-only" for="login">Логин</label>
            <input type="text" class="form-control" id="login" name="login" placeholder="Логин"
                   value="<?= $vars['filter']['login']; ?>">
        </div>
        <div class="form-group">
            <label class="sr-only" for="action">Действие</label>
            <select class="form-control" name="action">
                <option value=""></option>
                <?php foreach (Model_Config::$Actions as $_action => $_title): ?>
                    <option value="<?= $_action; ?>"
                            <?php if (!empty($vars['filter']) && $vars['filter']['action'] == $_action): ?>selected<?php endif; ?>><?= $_title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-default">Применить</button>
        <button type="submit" class="btn btn-default" name="reset_filter" value="1">Сбросить</button>
    </form>


    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => true,
    ]); ?>

    <table class="table">
        <tr class="head">
            <th>Дата</th>
            <th>Действие</th>
            <th>Пользователь</th>
            <th>ИД объекта</th>
            <th>Параметры</th>
        </tr>
        <?php /** @var Model_Logs_Log $log */ ?>
        <?php $zebra = 1;

        foreach ($vars['list'] as $log):
            $params = $log->getParams();
            ?>
            <tr<?php if ($zebra): ?> class="odd"<?php endif; ?>>
                <td><?= Lib_TimeStamp::createFromTimestamp($log->date)->format(Lib_TimeStamp::FULL_FORMAT); ?></td>
                <td><?= $log->title; ?></td>
                <td><?= $log->userId; ?></td>
                <td><?= $log->objectId; ?></td>
                <td style="max-width: 400px;">
                    <pre style="overflow-y: auto; max-height: 200px;"><?php print_r($params); ?></pre>
                </td>
            </tr>
            <?php $zebra = 1 - $zebra; endforeach; ?>
    </table>
</section>
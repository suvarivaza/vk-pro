<?php /** @var array $vars */ ?>
<a class="btn btn-default pull-right" href="/admin/logs/list/1">Логи</a>
<div class="content_cointainer" style="margin-top: 10px;">
    <form method="get" class="form-inline" role="form">
        <div class="form-group">
            <label for="dateFrom" class="col-sm-2 control-label">с</label>
            <div class="col-sm-10">
                <input type="text" class="input c_datePicker fill form-control" id="dateFrom" name="dateFrom"
                       placeholder="Дата с" value="<?= $vars['dateFrom']; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="dateTo" class="col-sm-2 control-label">по</label>
            <div class="col-sm-10">
                <input type="text" class="input c_datePicker fill form-control" id="dateTo" name="dateTo"
                       placeholder="Дата по" value="<?= $vars['dateTo']; ?>">
            </div>
        </div>
        <button class="button-green" type="submit"> Применить</button>
    </form>


    <table class="table table-striped table-hover" style="margin-top: 10px;">
        <tr>
            <th>#</th>
            <th>IP</th>
            <th>User ID</th>
            <th>Логин</th>
            <th>Имя</th>
            <th>E-mail</th>
            <th>Количество</th>
        </tr>
        <?php $i = 1;

        foreach ($vars['logs'] as $log): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $log['ip']; ?></td>
                <td><?= $log['userId']; ?></td>
                <td><?= $log['login']; ?></td>
                <td><?= $log['name']; ?></td>
                <td><?= $log['email']; ?></td>
                <td><?= $log['count']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<script type="text/javascript">
    $(".c_datePicker").datepicker({
        dateFormat: "dd.mm.yy"
    });
</script>
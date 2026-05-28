<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Пользователи
        <small>Общая статистика</small>
    </h1>
</section>
<section>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $vars['total']['total']; ?></h3>

                    <p>Всего</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
                <a href="/admin/users/list/1" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= $vars['total']['online']; ?></h3>
                    <p>Онлайн</p>
                </div>
                <div class="icon">
                    <i class="ion ion-android-globe"></i>
                </div>
                <a href="/admin/users/list/online/1" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= $vars['total']['bad']; ?></h3>
                    <p>Не прошли проверку</p>
                </div>
                <div class="icon">
                    <i class="ion ion-sad-outline"></i>
                </div>
                <a href="/admin/users/list/bad/1" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= $vars['total']['ban']; ?></h3>
                    <p>Собаки</p>
                </div>
                <div class="icon">
                    <i class="ion ion-close-circled"></i>
                </div>
                <a href="/admin/users/list/ban/1" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
</section>
<form method="get" class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-3">
            <input class="form_datetime form-control form-span" name="dateFrom"
                   value="<?= date('d.m.Y', $vars['from']); ?>" id="i_dateFrom" placeholder="">
        </div>
        <div class="col-sm-3">
            <input class="form_datetime form-control form-span" name="dateTo" value="<?= date('d.m.Y', $vars['to']); ?>"
                   id="i_dateTo" placeholder="">
        </div>
        <div class="col-sm-2">
            <button type="submit" class="btn btn-primary">Применить</button>
        </div>
    </div>
</form>
<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
<script>
    var area = new Morris.Line({
        element: 'revenue-chart',
        resize: true,
        data: [
            <?php foreach ($vars['keys'] as $key => $title): ?>
            {
                y: '<?= $key; ?>',
                online: <?= $vars['online'][$key] ?? 0; ?>,
                stat: <?= $vars['stat'][$key] ?? 0; ?>,
                bad: <?= $vars['bad'][$key] ?? 0; ?>,
                ban: <?= $vars['ban'][$key] ?? 0; ?>
            },
            <?php endforeach; ?>
        ],
        xkey: 'y',
        ykeys: ['online', 'stat', 'bad', 'ban'],
        labels: ['Онлайн', 'Новых пользователей', 'Не прошедших проверку', 'Забанненых'],
        lineColors: [
            '#00a65a',
            '#00c0ef',
            '#f39c12',
            '#dd4b39'
        ],
        hideHover: 'auto'
    });

    $('.form_datetime').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        orientation: "bottom"
    });
</script>
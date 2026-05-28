<style>
    .small-box > .c_div_more_info {
        max-height: 0;
        transition: max-height 0.35s ease-out;
        overflow: hidden;
    }

    .small-box.active > .c_div_more_info {
        font-size: 14px;
        max-height: 600px;
        transition: max-height 0.25s ease-in;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Покупки
        <small>Общая статистика</small>
    </h1>
</section>
<div class="row">
    <?php foreach ($vars['months'] as $month => $sum): ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua" id="i_div_month_<?= $month; ?>">
                <div class="inner">
                    <h3><?= $sum; ?></h3>
                    <p><?= $month; ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-list-ol"></i>
                </div>
                <div class="c_div_more_info">
                    <ul class="list-group">
                        <?php if (isset($vars['packs'][$month])) {
    foreach ($vars['packs'][$month] as $packId => $data): ?>
                                <li class="list-group-item bg-aqua">
                                    Пакет <strong><?= $vars['list'][$packId]->title; ?></strong>
                                    (<?= $data['count']; ?>)
                                    <strong class="pull-right"><?= $data['sum']; ?></strong>
                                </li>
                            <?php endforeach;
} ?>
                        <?php foreach ($vars['services'][$month] as $service => $data): ?>
                            <li class="list-group-item bg-aqua">
                                <?= $vars['titles'][$service]; ?>
                                (<strong><?= $data['count']; ?></strong>)
                                <strong class="pull-right"><?= $data['sum']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                        <?php if (isset($vars['balance'][$month])): ?>
                            <li class="list-group-item bg-aqua">
                                Покупка баллов
                                <strong class="pull-right"><?= $vars['balance'][$month]; ?></strong>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($vars['karma'][$month])): ?>
                            <li class="list-group-item bg-aqua">
                                Карма
                                <strong class="pull-right"><?= $vars['karma'][$month]; ?></strong>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <a href="javascript:void(0)" onclick="$('#i_div_month_<?= $month; ?>').toggleClass('active');"
                   class="small-box-footer">Подробная информация <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

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
<h3>Сумма заказов</h3>
<div class="chart tab-pane active" id="sum-chart" style="position: relative; height: 300px;"></div>
<h3>Количество заказов</h3>
<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>

<script>
    var area = new Morris.Line({
        element: 'revenue-chart',
        resize: true,
        data: [
            <?php foreach ($vars['keys'] as $key => $title): ?>
            {
                y: '<?= $key; ?>',
                stat: <?= $vars['stat'][$key] ?? 0; ?>,
                done: <?= $vars['done'][$key] ?? 0; ?>,
            },
            <?php endforeach; ?>
        ],
        xkey: 'y',
        ykeys: ['stat', 'done'],
        labels: ['Заказы', 'Оплаченные'],
        lineColors: [
            '#00c0ef',
            '#00a65a'
            //'#dd4b39'
        ],
        hideHover: 'auto'
    });

    var areaSum = new Morris.Line({
        element: 'sum-chart',
        resize: true,
        data: [
            <?php foreach ($vars['keys'] as $key => $title): ?>
            {
                y: '<?= $key; ?>',
                sum: <?= $vars['sum'][$key] ?? 0; ?>,
                sumDone: <?= $vars['sumDone'][$key] ?? 0; ?>,
            },
            <?php endforeach; ?>
        ],
        xkey: 'y',
        ykeys: ['sum', 'sumDone'],
        labels: ['Заказы', 'Оплаченные'],
        lineColors: [
            '#00c0ef',
            '#00a65a'
            //'#dd4b39'
        ],
        hideHover: 'auto'
    });

    $('.form_datetime').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        orientation: "bottom"
    });
</script>
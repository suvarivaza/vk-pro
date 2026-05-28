<section class="content-header">
    <h1>
        Бот
        <small>Общая статистика</small>
    </h1>
</section>
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua active" id="i_div_all">
            <div class="inner">
                <h3><?= $vars['counts']['total']; ?></h3>
                <p>Всего</p>
            </div>
            <div class="icon">
                <i class="fa fa-list-ol"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green active">
            <div class="inner">
                <h3>
                    <?= $vars['counts']['active']; ?>
                    <small><?= $vars['counts']['active']; ?></small>
                </h3>
                <p>Активные</p>
            </div>
            <div class="icon">
                <i class="ion ion-android-globe"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-purple active">
            <div class="inner">
                <h3><?= $vars['counts']['pro']; ?></h3>
                <p>PRO</p>
            </div>
            <div class="icon">
                <i class="fa fa-check"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red active">
            <div class="inner">
                <h3><?= $vars['counts']['work'] ?: 0; ?></h3>
                <p>Нет токена доступа</p>
            </div>
            <div class="icon">
                <i class="ion ion-close-circled"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
</div>

<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
<script>
    var area = new Morris.Line({
        element: 'revenue-chart',
        resize: true,
        data: [
            <?php foreach ($vars['keys'] as $key => $title) : ?>
            {
                y: '<?= $key; ?>',
                all: <?= $vars['stat'][$key] ?? 0; ?>
            },
            <?php endforeach; ?>
        ],
        xkey: 'y',
        ykeys: ['all'],
        labels: ['Выполнений'],
        lineColors: [
            '#00c0ef'
        ],
        hideHover: 'auto'
    });
</script>
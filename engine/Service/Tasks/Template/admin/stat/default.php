<style>
    .small-box > .c_div_more_info {
        max-height: 0;
        transition: max-height 0.15s ease-out;
        overflow: hidden;
    }

    .small-box.active > .c_div_more_info {
        max-height: 500px;
        transition: max-height 0.25s ease-in;
    }
</style>
<section class="content-header">
    <h1>
        Задания
        <small>Общая статистика</small>
    </h1>
</section>
<section>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua active" id="i_div_all">
                <div class="inner">
                    <h3><?= $vars['stat']['all']; ?></h3>

                    <p>Всего</p>
                </div>
                <div class="icon">
                    <i class="fa fa-list-ol"></i>
                </div>
                <div class="c_div_more_info">
                    <ul class="list-group">
                        <?php foreach ($vars['types'] as $type => $title): ?>
                            <li class="list-group-item bg-aqua">
                                <?= $title; ?>
                                <strong class="pull-right"><?= number_format($vars['stat']['type']['all'][$type], '0',
                                        '.', ' '); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="/admin/tasks" class="small-box-footer">Список задаий <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green active">
                <div class="inner">
                    <h3><?= $vars['stat']['active']; ?></h3>
                    <p>Активные</p>
                </div>
                <div class="icon">
                    <i class="ion ion-android-globe"></i>
                </div>
                <div class="c_div_more_info">
                    <ul class="list-group">
                        <?php foreach ($vars['types'] as $type => $title): ?>
                            <li class="list-group-item bg-green">
                                <?= $title; ?>
                                <strong class="pull-right">
                                    <?php if (isset($vars['stat']['type']['active'][$type]) && $vars['stat']['type']['active'][$type] > 0): ?>
                                        <?= number_format($vars['stat']['type']['active'][$type], '0', '.', ' '); ?>
                                    <?php else: ?>
                                        <span style="color: red;">0</span>
                                    <?php endif; ?>
                                </strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="/admin/tasks/list/active" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple active">
                <div class="inner">
                    <h3><?= $vars['stat']['done']; ?></h3>
                    <p>Завершенные</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check"></i>
                </div>
                <div class="c_div_more_info">
                    <ul class="list-group">
                        <?php foreach ($vars['types'] as $type => $title): ?>
                            <li class="list-group-item bg-purple">
                                <?= $title; ?>
                                <strong class="pull-right"><?= number_format($vars['stat']['type']['done'][$type], '0',
                                        '.', ' '); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="/admin/tasks/list/done" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red active">
                <div class="inner">
                    <h3><?= $vars['stat']['isDel']; ?></h3>
                    <p>Удаленные</p>
                </div>
                <div class="icon">
                    <i class="ion ion-close-circled"></i>
                </div>
                <div class="c_div_more_info">
                    <ul class="list-group">
                        <?php foreach ($vars['types'] as $type => $title): ?>
                            <li class="list-group-item bg-red">
                                <?= $title; ?>
                                <strong class="pull-right"><?= number_format($vars['stat']['type']['isDel'][$type], '0',
                                        '.', ' '); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="/admin/tasks/list/del" class="small-box-footer">Подробнее <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
</section>
<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
<script>
    var area = new Morris.Line({
        element: 'revenue-chart',
        resize: true,
        data: [
            <?php foreach ($vars['keys'] as $key => $title): ?>
            {
                y: '<?= $key; ?>',
                all: <?= $vars['all'][$key] ?? 0; ?>,
                active: <?= $vars['active'][$key] ?? 0; ?>,
                done: <?= $vars['done'][$key] ?? 0; ?>,
                del: <?= $vars['del'][$key] ?? 0; ?>
            },
            <?php endforeach; ?>
        ],
        xkey: 'y',
        ykeys: ['all', 'active', 'done', 'del'],
        labels: ['Новых заданий', 'Активных', 'Завершенных', 'Удаленных'],
        lineColors: [
            '#00c0ef',
            '#00a65a',
            '#605ca8',
            '#dd4b39'
        ],
        hideHover: 'auto'
    });
</script>
<?php /** @var array $vars */ ?>
<form class="form-inline" role="form" style="text-align: left;" method="get" action="" id="frm_filter">
    <div class="form-group">
        <label for="month" class="control-label">Месяц</label>
        <select id="month" name="month">
            <?php foreach ($vars['month'] as $_id => $_month): ?>
                <option value="<?= $_id; ?>"<?php if ($_id == $vars['filter']['month']): ?> selected<?php endif; ?>><?= $_month; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="year" class="control-label">Год</label>
        <select id="year" name="year">
            <?php foreach ($vars['year'] as $year): ?>
                <option value="<?= $year; ?>"<?php if ($year == $vars['filter']['year']): ?> selected<?php endif; ?>><?= $year; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Применить</button>
    <button type="submit" class="btn btn-warning" name="reset_filter" value="1">Сбросить</button>
</form>
<div id="i_div_chart"></div>
<script type="text/javascript">
    $('#i_div_chart').highcharts({
        chart: {
            type: 'line',
            height: 370,
            'min-width': 100
        },
        title: {
            text: 'Статистика пользователя <?= $vars['user']; ?>'
        },
        xAxis: {
            categories: [
                '<?= implode("','", $vars['titles']); ?>'
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Кол'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [
            <?php $first = true;

            foreach ($vars['charts'] as $data): ?>
            <?php if (!$first): ?>,<?php endif; ?>
            {
                name: '<?= $data['title']; ?>',
                data: [<?= implode(',', $data['list']); ?>]
            }
            <?php $first = false; endforeach; ?>
        ]
    });
</script>
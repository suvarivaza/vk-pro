<?php
/** @var \Service\Users\Model_Cities_City[] $list */
$list = $vars['list'];
?>
    <h1>
        Города
        <small class="pull-right">
            <a href="/admin/tasks/cities" class="btn btn-primary">Города</a>
            <a href="/admin/tasks/countries" class="btn btn-default">Страны</a>
        </small>
    </h1>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Город</th>
            <th class="text-center">Пользователей</th>
            <th class="text-center">Видимость</th>
        </tr>
        <?php foreach ($list as $city): ?>
            <tr class="<?php if ($city->isVisible): ?>success<?php endif; ?>">
                <td><?= $city->title; ?></td>
                <td class="text-center"><?= $city->count; ?></td>
                <td class="text-center">
                    <?php if ($city->isVisible): ?>
                        <a class="btn btn-success btn-sm"
                           href="?toggle=<?= $city->cityId; ?>&page=<?= $vars['page']; ?>">
                            <span class="glyphicon glyphicon-eye-open"></span>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-default btn-sm"
                           href="?toggle=<?= $city->cityId; ?>&page=<?= $vars['page']; ?>">
                            <span class="glyphicon glyphicon-eye-close"></span>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
<?php
/** @var Model_News_New[] $list */
$list = $vars['list'];

use Service\News\Model_News_New;

?>
<div>
    <a class="btn btn-success" href="/admin/news/add"><?= $vars['add']; ?></a>
</div>

<?php if ($vars['list']): ?>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
    <table class="table">
        <?php foreach ($list as $new): $photo = $new->getPhoto(); ?>
            <tr>
                <td style="vertical-align: top; width: 170px;">
                    <a href="/<?= $new->alias; ?>">
                        <?php if (isset($photo['small'])): ?>
                            <img class="img_news" src="<?= $photo['small']['url']; ?>">
                        <?php endif; ?>
                    </a>
                </td>
                <td>
                    <h4>
                        <a href="/<?= $new->alias; ?>"><?= $new->title ?: 'Редактировать'; ?></a>
                        <small class="pull-right">
                            <a class="btn btn-primary" href="/admin/news/edit/<?= $new->alias; ?>">Редактировать</a>
                            <a class="btn btn-danger"
                               href="/admin/pages/delete/<?= urlencode($page['alias']); ?>?backUrl=<?= urlencode($vars['backUrl']); ?>">удалить</a>
                        </small>
                    </h4>
                    <div>
                        <?= $new->desc; ?>
                    </div>
                    <div>
                        <a class="btn <?php if ($new->announce): ?>btn-success<?php else: ?>btn-danger<?php endif; ?>"
                           href="?toggle=<?= $new->newId; ?>"><?php if ($new->announce): ?>Отображается<?php else: ?>Скрыта<?php endif; ?></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
<?php endif; ?>

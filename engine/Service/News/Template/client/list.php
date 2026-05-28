<?php
/** @var Model_News_New[] $list */
use Service\News\Model_News_New;

$list = $vars['list'];
?>
    <ul class="breadcrumb">
        <li><strong>Новости</strong></li>
    </ul>

<?php if ($vars['list']): ?>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
    <table class="table">
        <?php foreach ($list as $new): $photo = $new->getPhoto(); ?>
            <tr>
                <td style="vertical-align: top; width: 170px;">
                    <a href="/news/<?= $new->alias; ?>">
                        <?php if (isset($photo['small'])): ?>
                            <img class="img_news" src="<?= $photo['small']['url']; ?>">
                        <?php endif; ?>
                    </a>
                </td>
                <td>
                    <h4>
                        <a href="/news/<?= $new->alias; ?>"><?= $new->title; ?></a>
                    </h4>
                    <div><?= $new->desc; ?></div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo STPL::PagesLink([
        'pageslink' => $vars['pageslink'],
        'showtitle' => false,
    ]); ?>
<?php endif; ?>
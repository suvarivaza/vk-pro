<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
$photos = $user->getPhotos();
/** @var \Service\Catalog\Model_Items_Item[] $items */
$items = $vars['items'];
?>
<ul class="breadcrumb">
    <?php $total = count($vars['chain']);
    $i = 0;

    foreach ($vars['chain'] as $data): $i++; ?>
        <li<?php if ($i == $total): ?> class="active"<?php endif; ?>>
            <?php if ($i != $total): ?><a
                    href="<?= $data['url']; ?>"><?php endif; ?><?= $data['title']; ?><?php if ($i != $total): ?></a><?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<h1>
    <?= $user->lastName; ?>
    <?= $user->firstName; ?>
    <?= $user->secondName; ?>
    <small><?= $user->year ? ($user->year . ' г.р.') : ''; ?></small>
</h1>
<div>
    <?php if (isset($photos[0]['path'])): ?>
        <img align="left" src="/images/users/big/<?= $photos[0]['path']; ?>" title="<?= $user->getShortName(); ?>"
             width="200"/>
    <?php endif; ?>
    <?= $user->bioFull; ?>
</div>
<div style="clear: both"></div>
<h3>Произведения автора</h3>
<ul class="users-list">
    <?php foreach ($items as $item): $photos = $item->getPhotos(); ?>
        <li class="users-list-item">
            <a href="/users/tale/<?= urlencode($item->alias); ?>">
                <table class="users-list-item-table">
                    <tr>
                        <td style="width: 20%;">
                            <div class="bg-default text-center writer-name-container">
                                <?php if (isset($photos[0]['path'])): ?>
                                    <img src="/images/catalog/small/<?= $photos[0]['path']; ?>"/>
                                <?php else: ?>
                                    <img src="/images/no-photo.png" width="130" height="130"/>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <h4><?= $item->title; ?></h4>
                            <?= \Lib_Text::Truncate(strip_tags($item->text), 400); ?>
                        </td>
                    </tr>
                </table>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
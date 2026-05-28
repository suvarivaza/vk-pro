<?php
/** @var \Service\Users\Model_Users_User $user */
?>
    <table class="c_letters_links">
        <tr>
            <?php foreach ($vars['letters'] as $letter): ?>
                <td class="text-center"><a
                            class="c_letter_link<?php if ($letter == $vars['active']): ?> active<?php endif; ?>"
                            href="/users/<?= $letter; ?>"><?= $letter; ?></a></td>
            <?php endforeach; ?>
        </tr>
    </table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>

    <ul class="users-list">
        <?php foreach ($vars['list'] as $user): $photos = $user->getPhotos(); ?>
            <li class="users-list-item">
                <a href="?p=<?= $vars['page']; ?>&writer=<?= $user->userId; ?>">
                    <table class="users-list-item-table">
                        <tr>
                            <td style="width: 20%;">
                                <div class="bg-default text-center writer-name-container">
                                    <?php if (isset($photos[0]['path'])): ?><img
                                        src="/images/users/small/<?= $photos[0]['path']; ?>" /><?php endif; ?>
                                    <div class="users-list-item-name"><?= $user->getShortName(); ?></div>
                                </div>
                            </td>
                            <td>
                                <?= \Lib_Html::ChangeBR($user->bioShort); ?>
                            </td>
                        </tr>
                    </table>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
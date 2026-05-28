<?php
$list = $vars['list'];
/* @var \Service\Auto\Model_Autos_Groups_Group $group */
?>
    <ul class="breadcrumb">
        <li>
            <img src="/img/icons/32/icon-services.png" width="30"/>
            <a href="/grabber">Приобретенные функции</a>
        </li>
    </ul>

<?php if (count($list)): ?>
    <table class="table">
        <?php foreach ($list as $group): ?>
            <tr>
                <td><img class="img-circle" src="<?= $group['photo']; ?>" style="width: 32px;"/></td>
                <td><h5><?= $group['title']; ?></h5></td>
                <td style="white-space: nowrap;">
                    <?php if ($group['isAuto']): ?>
                        <a href="/auto/<?= $group['isAutoId']; ?>"><img class="c_tooltip"
                                                                        data-content="Автоведение до <?= date('d.m.Y',
                                                                            $group['isAutoValid']); ?>"
                                                                        src="/img/icons/32/icon-auto.png"/></a>
                    <?php endif; ?>
                    <?php if ($group['isPosting']): ?>
                        <a href="/posting/<?= $group['isPostingId']; ?>"><img class="c_tooltip"
                                                                              data-content="Автопостинг до <?= date('d.m.Y',
                                                                                  $group['isPostingValid']); ?>"
                                                                              src="/img/icons/32/icon-post.png"/></a>
                    <?php endif; ?>
                    <?php if ($group['isGrabber']): ?>
                        <a href="/grabber/<?= $group['isGrabberId']; ?>"><img class="c_tooltip"
                                                                              data-content="Граббер до <?= date('d.m.Y',
                                                                                  $group['isGrabberValid']); ?>"
                                                                              src="/img/icons/32/icon-grabber.png"/></a>
                    <?php endif; ?>
                    <?php if ($group['isSpecial']): ?>
                        <a href="/tasks/special/<?= $group['isSpecialId']; ?>/all/1"><img class="c_tooltip"
                                                                                          data-content="Спецзадания до <?= date('d.m.Y',
                                                                                              $group['isSpecialValid']); ?>"
                                                                                          src="/img/icons/32/icon-special.png"/></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="alert alert-info">
        У вас нет приобретенных функций
    </div>
<?php endif; ?>
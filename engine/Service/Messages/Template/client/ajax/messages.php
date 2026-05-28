<?php
/** @var \Service\Messages\Model_Users_User $messageUser */
?>
<ul>
    <?php if ($vars['count'] > 0): ?>
        <li style="cursor: pointer; position: relative;">
            <div style="position: absolute; top: 0; left: 0; color: #ffffff; background-color: #5b7196; width: 100%; border-radius: 3px; padding: 3px;">
                Новые ответы техподдержки (<?= $vars['count']; ?>)
            </div>
            <a href="/faq/my">
                <p>У вас есть новые ответы от Технической поддержки</p>
            </a>
        </li>
    <?php endif; ?>
    <?php foreach ($vars['list'] as $messageUser): ?>
        <li id="i_li_<?= $messageUser->messageUserId; ?>" style="cursor: pointer; position: relative;">
            <div style="position: absolute; top: 0; left: 0; color: #ffffff; background-color: #5b7196; width: 100%; border-radius: 3px; padding: 3px;">
                <span class="pull-right glyphicon glyphicon-remove" onclick="messages.hideMessage($(this));"
                      data-message-id="<?= $messageUser->messageUserId; ?>"></span>
                <img class="pull-left" src="<?= \Service\Messages\Model_Config::$icons[$messageUser->icon]; ?>"
                     height="18" style="margin-right: 5px;"/>
                <?= date('d.m.Y H:i:s', $messageUser->dateCreate); ?>
            </div>
            <div onclick="messages.showMessageDetail($(this));" data-message-id="<?= $messageUser->messageUserId; ?>">
                <?= $messageUser->text; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
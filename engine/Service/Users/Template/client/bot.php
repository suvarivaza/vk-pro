<?php
/** @var \Service\Users\Model_Users_User $user */
$user = $vars['user'];
?>
<?php if ($user->isBot > 0): ?>
    <div class="alert alert-success">
        <h3 class="text-center">Автоматическое выполнение заданий включено</h3>
        <div class="text-center">
            <a class="btn btn-danger" href="?isBotDisable=1">
                <span class="glyphicon glyphicon-off"></span>
                Выключить
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <h3 class="text-center">Автоматическое выполнение заданий выключено</h3>
        <div class="text-center">
            <a class="button-green" href="?isBot=1">
                <span class="glyphicon glyphicon-off"></span>
                Включить
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if ($user->isBot > 0): ?>
    <form method="post" action="">
        <input type="hidden" name="action" value="botSettingsSave"/>
        <table class="table">
            <?php foreach ($vars['botTypes'] as $id => $botType): ?>
                <tr>
                    <td>
                        <?= $vars['types'][$botType]; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </form>
<?php endif; ?>
<?php
/** @var \System\App $app */
$app = $vars['app'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $app->Title->Head; ?>
</head>
<body>
<?php STPL::Display('controls/header', $vars); ?>
<?php STPL::Display('controls/body', $vars); ?>
<?php STPL::Display('controls/footer', $vars); ?>
</body>
</html>
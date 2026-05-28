<div class="pure_white" style="padding: 0 20px;">
    <br/>
    <?php if ($vars['message'] == 'register.success'): ?>
        <h1 class="bg-success" style="padding: 20px;">Регистрация прошла успешно</h1>
        <p><a href="/users/login" target="_self">Авторизоваться</a></p>
    <?php elseif ($vars['message'] == 'login.success'): ?>
        <h1>Добро пожаловать</h1>
        <p><a href="/orders/my" target="_self">Мои фирмы</a></p>
    <?php endif; ?>
    <br/>
</div>
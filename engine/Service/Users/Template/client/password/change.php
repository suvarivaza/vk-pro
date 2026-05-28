<div class="pure_white">
    <form class="form-horizontal" role="form" method="post">
        <?php if (isset($vars['success'])): ?>
            <?php if ($vars['success']): ?>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-4 bg-success">
                        Пароль успешно сменен
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-4 bg-danger">
                        Пароль сменить не удалось. Попробуйте еще раз.
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vars['errors']): ?>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4 bg-danger">
                    <?= implode('<br />', $vars['errors']); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-10">
                <h1 class="h2">Смена пароля</h1>
            </div>
        </div>

        <div class="form-group">
            <label for="i_oldPassword" class="col-sm-4 control-label"></label>
            <div class="col-sm-6">
                <input class="form-control" type="password" name="oldPassword" id="i_oldPassword" value=""
                       placeholder="Старый пароль"/>
            </div>
        </div>

        <div class="form-group">
            <label for="i_newPassword" class="col-sm-4 control-label"></label>
            <div class="col-sm-6">
                <input class="form-control" type="password" name="newPassword" id="i_newPassword" value=""
                       placeholder="Новый пароль"/>
            </div>
        </div>

        <div class="form-group">
            <label for="i_confirmPassword" class="col-sm-4 control-label"></label>
            <div class="col-sm-6">
                <input class="form-control" type="password" name="confirmPassword" id="i_confirmPassword" value=""
                       placeholder="Подтверждение нового пароля"/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-10">
                <button type="submit" class="btn btn-success">Сменить пароль</button>
                <a href="/users/general">Отмена</a>
            </div>
        </div>
    </form>
    <br/>
</div>
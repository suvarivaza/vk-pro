<form method="post" class="form-horizontal">
    <input name="action" value="token" type="hidden">
    <div class="form-group">
        <label class="col-sm-12 control-label" style="text-align: left;">Для работы Автобота необходимо указать токен</label>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <div class="input-group">
                <input name="access_token" class="form-control" id="i_user_token" value="">
                <a class="input-group-addon btn btn-primary" href="<?= VK_TOKEN_URL; ?>" target="_blank">
                    Получить токен
                </a>
            </div>
        </div>
    </div>
    <div class="alert alert-info">
        После нажатия кнопки "Получить токен", скопируйте целиком ссылку из открывшейся вкладки, вставьте её в поле выше, и нажмите "Сохранить".
        Не переживайте из-за находящегося во вкладке предупреждения, токен нужен только для полноценной работы всех функций сервиса.
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>
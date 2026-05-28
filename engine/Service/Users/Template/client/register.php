<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <div class="alert alert-info">
                <h5>Перед использованием сервиса необходимо дополнить профиль.</h5>
            </div>
        </div>
        <div class="modal-body" id="i_form_login_container">
            <form method="post" class="form-horizontal" role="form" id="i_form_login_form">
                <input type="hidden" name="action" value="register"/>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="i_form_register_login" name="login" class="form-control form-span"
                               value="<?= $vars['user']->login; ?>" placeholder="Логин" type="text"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="i_form_register_email" name="email" class="form-control form-span"
                               placeholder="e-mail" type="text" value="<?= $vars['user']->email; ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="i_form_register_password" name="password" class="form-control form-span" value=""
                               placeholder="Пароль" type="password"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="i_form_register_passwordConfirm" name="passwordConfirm"
                               class="form-control form-span" value="" placeholder="Подтверждение пароля"
                               type="password"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        Регистрируясь, вы соглашаетесь с нашими <a href="javascript:void(0)"
                                                                   onclick="init.getPage('user_agreement'); return false;">условиями
                            использования</a> и <a href="javascript:void(0)"
                                                   onclick="init.getPage('privacy_policy'); return false;">политикой
                            конфиденциальности</a>
                    </div>
                </div>
            </form>
            <div id="i_form_regiter_data"></div>
            <div id="i_form_register_progress" class="progress progress-striped active" style="display: none;">
                <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                     style="width: 100%">
                    <span class="sr-only">&nbsp;</span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button id="i_form_regiter_button_register" type="button" class="btn btn-primary btn-lg btn-block">
                Сохранить
            </button>
        </div>
    </div>
</div>
<?php
/** @var \System\App $app */
$app = $vars['app'];
?>
<?php if (!$app->UserIsAuth()): ?>
    <div class="modal fade" id="i_form_login" tabindex="-1" role="dialog" aria-labelledby="form_login">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background-color: #1d759b;">
                <div class="modal-header">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal" aria-label="Close"
                            style="padding: 0 7px; font-size: 18px;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="i_form_login_label">
                        <img src="/img/logo.new.135.png" style="width: 30%;"/>
                    </h4>
                </div>
                <div class="modal-body" id="i_form_login_container" style="background-color: #ffffff;">
                    <form method="post" class="form-horizontal" role="form" id="i_form_login_form">
                        <input type="hidden" name="action" value="login"/>
                        <div id="i_login_container" style="display: none;">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input id="i_form_login_email" name="email" class="form-control form-span"
                                           placeholder="Логин или е-майл" type="text"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input id="i_form_login_password" name="password" class="form-control form-span"
                                           value="" placeholder="Пароль" type="password"/>
                                </div>
                            </div>
                            <a class="pull-right" href="/users/remind" style="color: #808080; margin-top: -10px;">Забыли
                                пароль?</a>
                            <div class="form-group">
                                <div class="col-xs-12">

                                </div>
                            </div>
                        </div>
                        <div id="i_ulogin_container">
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <h4>Что-бы начать работу с сайтом войдите, нажав кнопку:</h4>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 text-center">
                                    <div id="uLogin_eb7899e9" data-uloginid="eb7899e9" data-ulogin="display=buttons">
                                        <img src="/img/vk-login.jpg" style="max-width: 100%; cursor: pointer;"
                                             data-uloginbutton="vkontakte"/></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 text-center">
                                    Или
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 text-center">
                                    <button type="button"
                                            onclick="$('#i_login_container').slideDown();$('#i_form_login_bottom').show();$('#i_ulogin_container').hide();"
                                            class="button-green btn-block">Войти по логину и паролю
                                    </button>
                                </div>
                            </div>
                            <div>
                                Нажимая кнопку «Войти через ВКонтакте», вы принимаете условия <a target="_blank"
                                                                                                 href="/html?page=user_agreement">Пользовательского
                                    соглашения</a>
                            </div>
                        </div>
                    </form>
                    <div id="i_form_login_data"></div>
                    <div id="i_form_login_progress" class="progress progress-striped active" style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                             aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">&nbsp;</span>
                        </div>
                    </div>
                </div>
                <div id="i_form_login_bottom" class="modal-footer" style="background-color: #ffffff; display: none;">
                    <div>
                        Нажимая кнопку «Войти», вы принимаете условия <a target="_blank"
                                                                         href="/html?page=user_agreement">Пользовательского
                            соглашения</a>
                    </div>
                    <button id="i_form_login_button_login" type="button" class="button-green btn-block"
                            style="font-size: 18px;">Войти
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="page_dialog" tabindex="-1" role="dialog" aria-labelledby="privacy_policy">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="page_dialog_label"></h5>
            </div>
            <div class="modal-body" id="privacy_policy_container">
                <div id="page_dialog_data"></div>
                <div id="page_dialog_error"></div>
                <div id="page_dialog_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="page_dialog_cancel" type="button" class="btn btn-default" data-dismiss="modal">Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
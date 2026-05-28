<?php
/** @var App $app */
use System\App;

$app = $vars['app'];
?>
<footer>
    <div class="container text-center">
        <ul class="c-footer-menu">
            <li class="c-footer-menu-item">
                <a class="page-scroll active" href="/">Главная</a>
            </li>
            <li class="c-footer-menu-item">
                <a class="page-scroll " href="/about">О сервисе</a>
            </li>
            <li class="c-footer-menu-item">
                <a class="page-scroll " href="/news">Новости</a>
            </li>
            <li class="c-footer-menu-item">
                <a class="page-scroll " href="/rules">Правила</a>
            </li>
            <li class="c-footer-menu-item">
                <a class="page-scroll " href="/help">Помощь</a>
            </li>
            <li class="c-footer-menu-item">
                <a class="page-scroll " href="/contacts">Контакты</a>
            </li>
        </ul>
    </div>
    <div class="copyright text-center">VKSTORM © 2017</div>
</footer>
<?php if (!$app->UserIsAuth()): ?>
    <div class="modal fade" id="i_form_login" tabindex="-1" role="dialog" aria-labelledby="form_login">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="i_form_login_label">Вход в личный кабинет</h4>
                </div>
                <div class="modal-body" id="i_form_login_container">
                    <form method="post" class="form-horizontal" role="form" id="i_form_login_form">
                        <input type="hidden" name="action" value="login"/>
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
                        <div class="form-group">
                            <div class="col-xs-8">
                                Войти через ВКонтакте:
                                <div id="uLogin_eb7899e9" data-uloginid="eb7899e9"></div>
                            </div>
                            <div class="col-xs-4 text-right">
                                <a href="/users/remind" style="color: #808080;">Забыли пароль?</a>
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
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button id="i_form_login_button_login" type="button" class="btn btn-success btn-lg btn-block">
                        Войти
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

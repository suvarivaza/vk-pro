<?php
/**
 * @var \Service\Users\Model_Users_User
 */
?>
<style>
    .overflow {
        height: 50px; /* высота нашего блока */
        width: 500px; /* ширина нашего блока */
        background: #fff; /* цвет фона, белый */
        border: 1px solid #C1C1C1; /* размер и цвет границы блока */
        overflow-x: scroll; /* прокрутка по горизонтали */
        overflow-y: scroll; /* прокрутка по вертикали */
    }
</style>
<h1>
    <?= $vars['title']; ?>
    <span class="pull-right">
        <a class="btn btn-default" href="/admin/users/karma">
            <span class="glyphicon glyphicon-star"></span>
            Настройки кармы
        </a>
    </span>
</h1>
<?php if (!empty($vars['errors']) and count($vars['errors'])): ?>
    <div class="bg-danger text-danger" style="padding: 5px 10px;">
        <?= implode('<br />', $vars['errors']); ?>
    </div>
<?php endif; ?>
<form method="get" action="1" class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Поиск" name="q" value="<?= $_GET['q'] ?? ''; ?>"/>
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><span style="font-size: 19px;"
                                                                        class="fa fa-search"></span></button>
                </span>
            </div><!-- /input-group -->
        </div>
    </div>
</form>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>
<table class="table">
    <tr>
        <th></th>
        <th>ИД</th>
        <th></th>
        <th>Имя</th>
        <th>Рефералка</th>
        <th>Карма</th>
        <th class="text-right">Действия</th>
    </tr>
    <?php foreach ($vars['list'] as $user): $photo = $user->getPhotos(); ?>
        <tr>
            <td style="width: 60px; vertical-align: middle; position: relative;">
                <?php if (isset($vars['orders'][$user->userId])): ?>
                    <div style="position: absolute; right: 0; top: 0; border-radius: 10px; background: green; color: #fff; width: 20px; height: 20px; text-align: center;"><?= $vars['orders'][$user->userId]; ?></div>
                <?php endif; ?>
                <?php if (isset($photo['small']['url'])): ?>
                    <img class="img-circle" style="width: 50px;" src="<?= $photo['small']['url']; ?>"/>
                <?php else: ?>
                    <img class="img-circle" style="width: 60px;" src="/img/no-avatar.png"/>
                <?php endif; ?>
            </td>
            <td>
                <h3><?= $user->userId; ?></h3>
            </td>
            <td>
                <div class="overflow container">
                    <?= $user->access_token; ?>
                </div>
                <h4><?= $user->login; ?></h4>
                <div><?= $user->email; ?></div>
            </td>
            <td>
                <h4>
                    <a href="javascript:void(0);"
                       onclick="admin_user.showUserModal(<?= $user->userId; ?>);"><?= $user->name; ?></a>
                </h4>
                <div>
                    <span id="i_user_balance_<?= $user->userId; ?>"><?= number_format($user->balance, 1, '.',
                            ' '); ?></span> баллов
                </div>
            </td>
            <td>
                <h4 style="cursor: pointer;"><span class="fa fa-users c-referrer" data-user-id="<?= $user->userId; ?>"
                                                   style="color: <?php if ($user->isRefferer): ?> green<?php else: ?>#961101<?php endif; ?>;"></span>
                </h4>
            </td>
            <td>
                <div class="karma" style="cursor: pointer;"
                     onclick="$('#i_modal_karma_userId').val(<?= $user->userId; ?>); $('#i_modal_karma').modal();">
                    <div style="width: <?= abs($user->karma) > 100 ? 100 : abs($user->karma); ?>%;"
                         class="loadbar<?php if ($user->karma < 0): ?> minus<?php endif; ?>"></div>
                    <div class="index">Карма <?= number_format($user->karma, 1); ?>%</div>
                </div>
            </td>
            <td class="text-right">
                <a class="btn btn-default" title="Переключиться" href="?userId=<?= $user->userId; ?>"
                   onClick="return window.confirm('Вы действительно хотите переключиться на этого пользователя?');"><span
                            class="glyphicon glyphicon-share-alt"></span></a>
                <a class="btn btn-warning" title="Проверить страницу" href="?check=<?= $user->userId; ?>"><span
                            class="glyphicon glyphicon-repeat"></span></a>
                <a class="btn btn-success"
                   onclick="$('#i_modal_balance_userId').val(<?= $user->userId; ?>); $('#i_modal_balance').modal();"
                   href="javascript:void(0)"><span class="glyphicon glyphicon-rub"></span></a>
                <a class="btn btn-danger" href="?del=<?= $user->userId; ?>" onClick="return window.confirm('Вы действительно хотите удалить этого пользователя?');"><span
                            class="glyphicon glyphicon-alert"></span></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo STPL::PagesLink([
    'pageslink' => $vars['pageslink'],
    'showtitle' => false,
]); ?>

<div class="modal fade" id="i_modal_user" tabindex="-1" role="dialog" aria-labelledby="i_modal_user">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_modal_user_label">Пользователь</h5>
            </div>
            <div class="modal-body" id="i_modal_user_container">
                <div id="i_modal_user_data"></div>
                <div id="i_modal_user_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span id="i_modal_user_progress_span" class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_modal_user_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена
                </button>
                <button id="i_modal_user_save" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="i_modal_activate" tabindex="-1" role="dialog" aria-labelledby="i_modal_activate">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_modal_activate_label">Добавить/убрать баллы</h5>
            </div>
            <div class="modal-body" id="i_modal_activate_container">
                <form method="post" class="form-horizontal" role="form" id="i_modal_activate_form"></form>
                <div id="i_modal_activate_data"></div>
                <div id="i_modal_activate_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_modal_activate_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена
                </button>
                <button id="i_modal_activate_active" type="button" class="btn btn-primary">Активировать</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="i_modal_balance" tabindex="-1" role="dialog" aria-labelledby="i_modal_balance">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_modal_balance_label">Добавить/убрать баллы</h5>
            </div>
            <div class="modal-body" id="i_modal_balance_container">
                <form method="post" class="form-horizontal" role="form" id="i_modal_balance_form">
                    <input type="hidden" name="action" value="addBalance"/>
                    <input type="hidden" id="i_modal_balance_userId"/>
                    <div class="input-group">
                        <span class="input-group-addon">Добавить</span>
                        <input id="i_modal_balance_sum" type="text" class="form-control"
                               placeholder="Укажите количество">
                        <span class="input-group-addon">баллов</span>
                    </div>
                </form>
                <div id="i_modal_balance_data"></div>
                <div id="i_modal_balance_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_modal_balance_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена
                </button>
                <button id="i_modal_balance_add" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="i_modal_karma" tabindex="-1" role="dialog" aria-labelledby="i_modal_karma">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_modal_karma_label">Добавить/убрать карму</h5>
            </div>
            <div class="modal-body" id="i_modal_karma_container">
                <form method="post" class="form-horizontal" role="form" id="i_modal_karma_form">
                    <input type="hidden" name="action" value="addBalance"/>
                    <input type="hidden" id="i_modal_karma_userId"/>
                    <div class="input-group">
                        <span class="input-group-addon">Добавить</span>
                        <input id="i_modal_karma_sum" type="text" class="form-control" placeholder="Укажите количество">
                        <span class="input-group-addon">%</span>
                    </div>
                </form>
                <div id="i_modal_karma_data"></div>
                <div id="i_modal_karma_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_modal_karma_cancel" type="button" class="btn btn-default" data-dismiss="modal">Отмена
                </button>
                <button id="i_modal_karma_add" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#i_modal_balance').on('show.bs.modal', function () {
        $('#i_modal_balance_add').show();
        $('#i_modal_balance_cancel').html('Отмена');
    });

    $('#i_modal_user_save').click(function() {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "saveUserType",
                userId: $('#i_user_detail_userId').val(),
                userType: $('#i_user_detail_userType').val(),
            },
            beforeSend: function () {
                $('#i_modal_user_progress').show();
            },
            complete: function () {
                $('#i_modal_user_progress').hide();
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    $('#i_modal_karma_data').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                    $('#i_modal_karma_add').hide();
                }
            },
            error: function () {
                $('#i_form_abuse_data').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    });

    $('#i_modal_balance_add').click(function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "addBalance",
                userId: $('#i_modal_balance_userId').val(),
                sum: $('#i_modal_balance_sum').val()
            },
            beforeSend: function () {
                $('#i_modal_balance_data').html('');
                $('#i_modal_balance_progress').show();
            },
            complete: function () {
                $('#i_modal_balance_progress').hide();
            },
            success: function (data) {
                if (data.success) {
                    $('#i_user_balance_' + data.userId).html(data.balanceText);
                    $('#i_modal_balance_add').hide();
                    $('#i_modal_balance_cancel').html('Закрыть');
                    $('#i_modal_balance').modal('hide');
                } else {
                    $('#i_modal_balance_data').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                    $('#i_modal_balance_add').hide();
                }
            },
            error: function () {
                $('#i_form_abuse_data').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    });

    $('#i_modal_karma_add').click(function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: {
                action: "addKarma",
                userId: $('#i_modal_karma_userId').val(),
                sum: $('#i_modal_karma_sum').val()
            },
            beforeSend: function () {
                $('#i_modal_karma_data').html('');
                $('#i_modal_karma_progress').show();
            },
            complete: function () {
                $('#i_modal_karma_progress').hide();
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    $('#i_modal_karma_data').html('<div class="alert alert-danger">' + data.errorText + '</div>');
                    $('#i_modal_karma_add').hide();
                }
            },
            error: function () {
                $('#i_form_abuse_data').html('<div class="alert alert-danger">Не удалось выполнить запрос</div>');
            }
        });
    });
</script>

<?php
/** @var Model_Autos_Groups_Group $group */
$group = $vars['group'];
/** @var Model_Autos_Auto $auto */
$auto = $vars['auto'];
$slots = $auto->getSlots();
$list = [];

foreach ($slots as $months) {
    if (!isset($list[$months])) {
        $list[$months] = 1;
    } else {
        ++$list[$months];
    }
}

use Service\Auto\Model_Autos_Auto;
use Service\Auto\Model_Autos_Groups_Group;

?>
<div class="pull-right">
    <a href="/faq/list/6" target="_blank" style="padding: 7px; display: block;" title="Помощь по сервису">
        <img src="/img/icons/32/icon-help.png" width="32"/>
    </a>
</div>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-auto.png" width="30"/>
        <a href="/auto">Автоведение</a>
    </li>
    <li>
        <img class="img-circle" src="<?= $group->photo; ?>" width="30">
        <a href="<?= $group->url; ?>" target="_blank" rel="nofollow"><?= $group->title; ?></a>
    </li>
</ul>
<?php if ($group->dateValid < time()): ?>
    <div class="alert alert-danger">
        Срок действия для группы "<strong><?= $group->title; ?></strong>" истек.
    </div>

    <div id="i_auto_group_add" class="row c-task-detail c-auto-group-add">
        <div class="col-sm-2">
            <img src="https://vk.com/images/community_100.png" class="img-thumbnail"/>
        </div>
        <div class="col-sm-8">
            <h3>Купить или активировать слот</h3>
        </div>
    </div>
<?php else: ?>

    <div class="c-task-my-detail">
        <ul id="i-group-settings-nav" class="nav nav-pills nav-justified">
            <li role="presentation" class="">
                <a id="i-link-template-add" href="javascript:void(0);" data-div="i_template_add">Добавить шаблон</a>
            </li>
            <li role="presentation" class="active">
                <a id="i_template_link_list" href="javascript:void(0);" data-div="i_templates_list">Текущие шаблоны</a>
            </li>
            <li role="presentation">
                <a href="javascript:void(0);" data-div="i_templates_list_archive">Архив шаблонов</a>
            </li>
        </ul>
    </div>
    <div id="i_template_add" class="c_templates_class c-task-my-detail" style="display: none;">

    </div>
    <div id="i_templates_list" class="c_templates_class"></div>
    <div id="i_templates_list_archive" class="c_templates_class" style="display: none;"></div>

    <script>
        $('#i-group-settings-nav > li > a').click(function () {
            var id = $(this).data('div');
            if (id == 'i_template_add') {
                auto.getTemplateAdd();
            }
            if (id == 'i_templates_list') {
                auto.getTemplatesList();
            }
            if (id == 'i_templates_list_archive') {
                auto.getTemplatesListArchive();
            }
            $('#i-group-settings-nav > li').removeClass('active');
            $(this).parent().addClass('active');
            $('.c_templates_class').hide();
            $('#' + $(this).data('div')).show();
        });
        auto.groupId = <?= $group->autoGroupId; ?>;
        auto.getTemplatesList();
    </script>
<?php endif; ?>
<div class="modal fade" id="i_dialog_group_add" tabindex="-1" role="dialog" aria-labelledby="i_dialog_group_add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="i_dialog_group_add_label">Добавление группы в автоведение</h5>
            </div>
            <div class="modal-body" id="i_dialog_group_add_container">
                <div id="i_dialog_group_add_data"></div>
                <div id="i_dialog_group_add_error"></div>
                <div id="i_dialog_group_add_progress" class="progress progress-striped active" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">&nbsp;</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button id="i_dialog_group_add_button_cancel" type="button" class="btn btn-default"
                        data-dismiss="modal">Отмена
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <?php $vars['list'] = [];
        $vars['max_count'] = 10;
        \STPL::Display('controls/upload', $vars); ?>
    </div>
    <div class="col-sm-6">
        <input type="text" placeholder="Введите URL: http://example.com/image.jpg"
               class="modal-url-image input-sm form-control">
        <div class="clearfix">
            <a class="btn-add-url-input noselect" href="javascript:void(0);">
                + Добавить еще
            </a>
            <a class="btn btn-sm btn-success btn-upload-url pull-right">Загрузить</a>
        </div>
    </div>
</div>
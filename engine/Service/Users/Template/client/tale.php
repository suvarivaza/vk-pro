<?php
/** @var \Service\Catalog\Model_Items_Item $book */
$book = $vars['book'];
/** @var \Service\Catalog\Model_Comments_Comment[] $comments */
$comments = $vars['comments'];
?>
<ul class="breadcrumb">
    <li><a href="/users/"><strong>Наши авторы</strong></a></li>
    <?php $total = count($vars['chain']);
    $i = 0;

    foreach ($vars['chain'] as $data): $i++; ?>
        <li<?php if ($i == $total): ?> class="active"<?php endif; ?>>
            <?php if ($i != $total): ?><a
                    href="<?= $data['url']; ?>"><?php endif; ?><?= $data['title']; ?><?php if ($i != $total): ?></a><?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<h1 class="item_h1"><?= $book->title; ?></h1>
<?php $photos = $book->getPhotos(); ?>
<div>
    <?php foreach ($photos as $photo): ?>
        <img style="max-width: 30%; margin: 40px; max-height: 400px;" src="/images/catalog/big/<?= $photo['path']; ?>">
    <?php endforeach; ?>

    <?= $book->text; ?>
</div>
<h4><small class="pull-right" style="color: #cc0000;">Просмотров: <?= $book->count; ?></small></h4>
<button type="button" class="btn btn-success comment_add">Добавить комментарий</button>
<div class="c_comments" data-unique-id="<?= $book->alias; ?>">
    <?php if (!count($comments)): ?>
        <div class="alert alert-warning">Еще нет ни одного комментария. Вы можете добавить его первым!</div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                    <h5 class="alert alert-success">
                        <?= $comment->userName; ?>
                        <small class="pull-right"><?= \Lib_TimeStamp::createFromTimestamp($comment->dateCreate)->format(); ?></small>
                    </h5>
                    <div><?= $comment->textQuestion; ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<button type="button" class="btn btn-success comment_add">Добавить комментарий</button>
<div class="modal fade" id="i_div_comment_add_dialog" tabindex="-1" role="dialog"
     aria-labelledby="messageSendDialogSuccess">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="i_div_comment_add_dialog_title">Добавить комментарий</h4>
            </div>
            <div class="modal-body">
                <div id="i_div_comment_add_dialog_text"></div>
            </div>
            <div class="modal-footer">
                <button id="i_comment_add_dialog_apply" type="button" class="btn btn-success">Добавить</button>
                <button id="i_div_history_close" type="button" class="btn btn-default" data-dismiss="modal">Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
<div id="i_div_comment_add_dialog_form" style="display: none;">
    <form class="form-horizontal" id="i_form_comment">
        <input type="hidden" name="action" value="commentAdd"/>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Ваше имя</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" name="userName" placeholder="Введите Ваше имя"/>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Текст комментария</label>
            <div class="col-sm-7">
                <textarea name="textQuestion" class="form-control"></textarea>
            </div>
        </div>
    </form>
</div>
<div id="i_div_comment_add_dialog_progress" style="display: none;">
    Подождите, Ваш комментарий отправляется
    <div id="i_progress" class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
             aria-valuemax="100" style="width: 100%">
        </div>
    </div>
</div>
<div id="i_div_comment_add_dialog_done" style="display: none;">
    <div class="alert alert-success">Спасибо! Ваш комментарий принят, и будет размещен на сайте сразу после проверки
        модератором.
    </div>
</div>
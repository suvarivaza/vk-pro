<?php
/** @var \Service\Posting\Model_Posts_Post $post */
$post = $vars['post'];
$group = $vars['group'];
$emoji = $vars['emoji'];
$attachments = $post->getAttachments();
$link = '';
$polls = [];
$videos = [];
$hour = date('H', $post->datePost);
$minute = date('i', $post->datePost);

foreach ($attachments as $attachment) {
    switch ($attachment['type']) {
        case 'video':
            $videos[] = $attachment;
            break;
        case 'poll':
            $polls[] = $attachment;
            break;
        case 'url':
            $link = $attachment;
            break;
        case 'link':
            $link = $attachment['link'];
            break;
        case 'doc':
            if ($attachment['doc']['ext'] == 'gif') {
                $gifs[] = $attachment;
            } else {
                $docs[] = $attachment;
            }
    }
}

?>
<form method="post" class="form-horizontal" role="form" id="i_post_form<?= $post->postId; ?>">
    <input id="i_post_action<?= $post->postId; ?>" type="hidden" name="action" value="<?= $vars['action']; ?>"/>
    <input id="i_post_uuid<?= $post->postId; ?>" type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
    <input id="i_postId<?= $post->postId; ?>" type="hidden" name="postId" value="<?= $post->postId; ?>"/>
    <textarea name="text" id="i_text<?= $post->postId; ?>" style="display: none;"></textarea>
    <div class="page_block">
        <img src="<?= $group->photo; ?>" class="img-circle pull-left" style="width: 28px;">
        <img onclick="$('#i_div_emoji<?= $post->postId; ?>').toggle();" src="/img/icons/32/vk/smile_icon.png"
             class="pull-right"/>
        <div class="c_div_emoji" id="i_div_emoji<?= $post->postId; ?>" style="display: none;">
            <div class="c_div_emoji_angle" id="i_div_emoji_angle<?= $post->postId; ?>">
                <span class="glyphicon glyphicon-menu-up"></span>
            </div>
            <div class="c_div_emoji_container" id="i_div_emoji_container_<?= $post->postId; ?>">
                <div class="c_div_emoji_scroll" id="i_div_emoji_scroll">
                    <?php foreach ($emoji as $emojiList): ?>
                        <div><?= $emojiList['title']; ?></div>
                        <?php foreach ($emojiList['texts'] as $text => $code): ?>
                            <a href="javascript:void(0)"
                               onclick="grabber.emojiAdd('<?= $code; ?>', <?= $post->postId; ?>)">
                                <img src="/img/emoji/<?= $code; ?>.png"/>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="page_block_add">
            <div class="c_post_field" id="post_field<?= $post->postId; ?>" title="Что у Вас нового?"
                 contenteditable="true"><?= \Lib_Html::ChangeBR(\Service\Posting\Model_Config::EmojiToHtml($post->text)); ?></div>
        </div>

        <div id="i_link_add_container<?= $post->postId; ?>" class="submit_post c_link_add_container"
             style="<?php if (!$link): ?>display: none;<?php endif; ?> margin-top: 10px;">
            <input class="form-control c_link_add_input" name="url[]" value="<?= $link['url']; ?>"
                   placeholder="Укажите ссылку на внешний ресурс"/>
        </div>

        <div class="submit_post">
            <div id="i_form_attachments_progress<?= $post->postId; ?>" class="progress progress-striped active"
                 style="display: none;">
                <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                     style="width: 100%">
                    <span class="sr-only">&nbsp;</span>
                </div>
            </div>
            <div id="i_div_attachments<?= $post->postId; ?>"></div>
        </div>

        <div class="c_link_ads submit_post" <?php if (!$post->ads): ?>style="display: none"<?php endif; ?>>
            <input type="checkbox" name="ads" id="i_link_ads<?= $post->postId; ?>"
                   <?php if (!$post->ads): ?>style="display: none"<?php endif; ?> />
            <div class="close pull-right"
                 onclick="$(this).parent().slideUp('slow', function(){$('#i_link_ads').prop('checked', false)});">
                <span class="glyphicon glyphicon-remove"></span>
            </div>
            <h5 class="c_ads_h5">Это реклама</h5>
        </div>

        <?php $index = 0;

        foreach ($polls as $poll): ?>
            <div data-index="<?= $index; ?>">
                <div class="close pull-right"
                     onclick="$(this).parent().slideUp('slow', function(){$(this).remove();});">
                    <span class="glyphicon glyphicon-remove"></span>
                </div>
                <div class="submit_post">
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <label>Тема опроса</label>
                            <?php if (isset($poll['poll'])): ?>
                                <input data-index="0" type="text" name="poll[<?= $index; ?>][title]"
                                       class="form-control c_poll_title" value="<?= $poll['poll']['question']; ?>">
                            <?php else: ?>
                                <input data-index="0" type="text" name="poll[<?= $index; ?>][title]"
                                       class="form-control c_poll_title" value="<?= $poll['title']; ?>">
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="row form-group">
                        <?php if (isset($poll['poll'])): ?>
                            <?php $first = true;

                            foreach ($poll['poll']['answers'] as $answer): ?>
                                <div class="col-sm-12">
                                    <label><?php if ($first): ?>Варианты ответа<?php endif; ?></label>
                                    <input data-index="<?= $index; ?>" type="text"
                                           name="poll[<?= $index; ?>][answers][]" class="form-control c_poll_answer"
                                           value="<?= $answer['text']; ?>">
                                </div>
                                <?php $first = false; endforeach; ?>

                            <div class="col-sm-12">
                                <label></label>
                                <input data-index="<?= $index; ?>" type="text" name="poll[<?= $index; ?>][answers][]"
                                       class="form-control" onclick="grabber.addAnswer(this);"
                                       placeholder="Добавить вариант">
                            </div>
                        <?php else: ?>
                            <?php $first = true;

                            foreach ($poll['answers'] as $answer): ?>
                                <div class="col-sm-12">
                                    <label><?php if ($first): ?>Варианты ответа<?php endif; ?></label>
                                    <input data-index="<?= $index; ?>" type="text"
                                           name="poll[<?= $index; ?>][answers][]" class="form-control c_poll_answer"
                                           value="<?= $answer; ?>">
                                </div>
                                <?php $first = false; endforeach; ?>

                            <div class="col-sm-12">
                                <label></label>
                                <input data-index="<?= $index; ?>" type="text" name="poll[<?= $index; ?>][answers][]"
                                       class="form-control" onclick="grabber.addAnswer(this);"
                                       placeholder="Добавить вариант">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php $index++; endforeach; ?>
        <script>
            $('#i_poll_example').data('index', <?= $index; ?>);
        </script>
        <div id="submit_post<?= $post->postId; ?>" class="submit_post">
            <div class="post_buttons">
                <button type="button" class="btn btn-primary pull-right" onclick="grabber.addPost()">Сохранить</button>&nbsp;
                <button type="button" class="btn btn-default pull-right" style="margin-right: 10px;"
                        onclick="grabber.cancelEditPost()">Отмена
                </button>&nbsp;
                <div class="pull-right" style="margin-right: 10px; padding-top: 2px;">
                    <input id="i_ownerId_<?= $post->postId; ?>" type="checkbox"
                           name="signature"<?php if ($post->signature): ?> checked="checked"<?php endif; ?>
                           style="margin-top: 2px;"/>
                    <label for="i_ownerId_<?= $post->postId; ?>" style="margin-top: -2px; display: inline-block;">Подпись</label>
                </div>

                <div class="c_div_icon c_tooltip_bottom" data-content="Добавить фото" data-placement="bottom"
                     id="i_div_add_photo_<?= $post->postId; ?>" onclick="$('#i_form_photos').modal('show');"><span
                            class="post_button_photo"></span></div>
                <div class="c_div_icon c_tooltip_bottom" data-content="Добавить видео" data-placement="bottom"
                     id="i_div_add_video_<?= $post->postId; ?>" onclick="$('#i_form_video').modal('show');"><span
                            class="post_button_video"></span></div>
                <div class="c_div_icon c_tooltip_bottom" data-content="Добавить ссылку" data-placement="bottom"
                     id="i_div_add_link_<?= $post->postId; ?>"
                     onclick="$('#i_link_add_container<?= $post->postId; ?>').show();"><span
                            class="post_button_link"></span></div>
                <div class="c_div_icon c_tooltip_bottom" data-content="Добавить опрос" data-placement="bottom"
                     id="i_div_add_poll_<?= $post->postId; ?>" onclick="grabber.addPoll();"><span
                            class="post_button_poll"></span></div>
                <div class="c_div_icon c_tooltip_bottom" data-content="Это реклама" data-placement="bottom"
                     id="i_div_ads_<?= $post->postId; ?>"
                     onclick="$('#i_link_ads_<?= $post->postId; ?>').prop('checked', true); $('.c_link_ads').slideDown();">
                    <span class="post_button_ads"></span></div>
            </div>
        </div>
    </div>
</form>
<script>
    $('#i_post_date_<?= $post->postId; ?>').datetimepicker({
        format: 'dd.mm.yyyy',
        startDate: new Date(),
        minView: 2,
        language: 'ru'
    });
    post_edit.postId = <?= $post->postId; ?>;
    grabber.postId = <?= $post->postId; ?>;
    uploader.postId = <?= $post->postId; ?>;

    post_edit.load_attachments();
    refreshLabels();
</script>
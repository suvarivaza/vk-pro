<?php
/** @var \Service\Posting\Model_Posts_Post $post */
$post = $vars['post'];
$attachments = $post->getAttachments();
$photos = [];
$videos = [];
$polls = [];
$url = '';

foreach ($attachments as $attachment) {
    switch ($attachment['type']) {
        case 'photo':
            $photos[] = $attachment;
            break;
        case 'video':
            $videos[] = $attachment;
            break;
        case 'poll':
            $polls[] = $attachment;
            break;
        case 'url':
            $url = $attachment;
            break;
    }
}
/** @var \Service\Posting\Model_Groups_Group $group */
$group = $vars['group'];
usort($photos, function ($a, $b) {
    if ($a['type'] == 'photo' && $b['type'] != 'photo') {
        return 1;
    }

    if ($a['type'] == 'photo' && $b['type'] == 'photo') {
        return $a['sort'] > $b['sort'];
    }

    return 0;
});
?>

<div class="page_block" id="i_post_detail_<?= $post->postId; ?>">
    <div class="pull-right" style="position: relative;">
        <div class="post_actions pull-right" onclick="$(this).toggleClass('active')">
            <span class="glyphicon glyphicon-option-horizontal"></span>
            <div class="post_actions_extend c_div_button_popup">
                <div class="c_div_button_popup_angle">
                    <span class="glyphicon glyphicon-menu-up"></span>
                </div>
                <div class="c_div_button_popup_container">
                    <br/>
                    <a class="c-user-menu-link" href="javascript:void();"
                       onclick="posting.postEdit(<?= $post->postId; ?>)">Редактировать</a>
                    <a class="c-user-menu-link" href="javascript:void();"
                       onclick="posting.postDel(<?= $post->postId; ?>); return false;">Удалить</a>
                    <hr/>
                    <a class="c-user-menu-link" href="javascript:void();"
                       onclick="posting.postPublish(<?= $post->postId; ?>)">Разместить</a>
                </div>
            </div>
        </div>
    </div>

    <div class="post_header">
        <a class="post_image" href="<?= $group->url; ?>" target="_blank">
            <img src="<?= $group->photo; ?>" class="img-circle pull-left"/>
        </a>
        <div class="post_header_info">
            <h5 class="post_author">
                <a href="<?= $group->url; ?>" target="_blank">
                    <?= $group->title; ?>
                </a>
                <small><br/><?= date('d.m.Y H:i', $post->datePost); ?></small>
            </h5>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div class="post_content">
        <div><?= \Lib_Html::ChangeBR(\Service\Posting\Model_Config::EmojiToHtml($post->text)); ?></div>
        <?php if (count($photos)): ?>
            <div class="c_attachments_photos">
                <?php foreach ($photos as $photo): ?>
                    <a class="c_gallery_link" target="_blank" href="<?= $photo['big']['url']; ?>"><img
                                class="c_attachments_photo" src="<?= $photo['big']['url']; ?>"/></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($videos)): ?>
            <div class="c_attachments_videos">
                <div class="row">
                    <?php $i = 0;

                    foreach ($videos
                    as $video):
                    $i++; ?>
                    <?php if ($i == 3):
                    $i = 1; ?></div>
                <div class="row"><?php endif; ?>
                    <div class="col-sm-6" style="position: relative; margin-top: 10px;">
                        <div class="c_gallery_link" id="i_video_preview_<?= $video['id']; ?>" data-iframe="true"
                             data-poster="<?= $video['img']; ?>" data-src="<?= $video['player']; ?>">
                            <img src="<?= $video['img']; ?>" style="width: 100%; height: 150px; object-fit: cover;"/>
                        </div>
                        <a href="https://vk.com/video<?= $video['owner_id']; ?>_<?= $video['id']; ?>"><?= $video['title']; ?></a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($url): ?>
            <div class="c_attachments_url">
                <a href="<?= $url['url']; ?>"><?= $url['url']; ?></a>
            </div>
        <?php endif; ?>
        <?php if (count($polls)): ?>
            <div class="c_attachments_polls">
                <?php foreach ($polls as $poll): ?>
                    <div class="c_attachments_poll">
                        <div class="c_attachments_poll_open pull-right">Открытое голосование</div>
                        <div class="c_attachments_poll_title"><?= $poll['title']; ?></div>
                        <?php foreach ($poll['answers'] as $answer): ?>
                            <div class="c_attachments_poll_answer">
                                <span class="glyphicon glyphicon-record"></span>
                                <?= $answer; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($post->ads): ?>
            <div class="c_link_ads submit_post">
                <div class="c_ads_div">Реклама в сообществе «<?= $group->title; ?>»</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="i_post_edit_<?= $post->postId; ?>"></div>
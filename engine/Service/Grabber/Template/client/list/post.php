<?php
/** @var \Service\Grabber\Model_Posts_Post $post */
$post = $vars['post'];
$attachments = $post->getAttachments();

if (!is_array($attachments)) {
    $attachments = [];
}
$photos = [];
$videos = [];
$polls = [];
$docs = [];
$gifs = [];
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
        case 'link':
            $url = $attachment['link'];
            break;
        case 'doc':
            if ($attachment['doc']['ext'] == 'gif') {
                $gifs[] = $attachment;
            } else {
                $docs[] = $attachment;
            }
    }
}
/** @var \Service\Posting\Model_Groups_Group $group */
$group = $vars['group'];



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
                       onclick="grabber.postEdit(<?= $post->postId; ?>)">Редактировать</a>
                    <a class="c-user-menu-link" href="javascript:void();"
                       onclick="grabber.postDel(<?= $post->postId; ?>); return false;">Удалить</a>
                    <hr/>
                    <a class="c-user-menu-link" href="javascript:void();"
                       onclick="grabber.postPublish(<?= $post->postId; ?>)">Разместить</a>
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
                <small><br/>Скопировано: <?= date('d.m.Y H:i', $post->dateCreate); ?> <?= $post->postId; ?></small>
            </h5>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div class="post_content">
        <div><?= \Lib_Html::ChangeBR(\Service\Posting\Model_Config::EmojiToHtml($post->text)); ?></div>
        <?php if (count($photos)): ?>
            <div class="c_attachments_photos">
                <?php

                foreach ($photos as $photo): ?>
                    <?php
                    $photoUrl = '';

                    //старый вариант
                    if (isset($photo['photo']['photo_2560'])) {
                        $photoUrl = $photo['photo']['photo_2560'];
                    } elseif (isset($photo['photo']['photo_1280'])) {
                        $photoUrl = $photo['photo']['photo_1280'];
                    } elseif (isset($photo['photo']['photo_807'])) {
                        $photoUrl = $photo['photo']['photo_807'];
                    } elseif (isset($photo['photo']['photo_604'])) {
                        $photoUrl = $photo['photo']['photo_604'];
                    } elseif (isset($photo['photo']['photo_130'])) {
                        $photoUrl = $photo['photo']['photo_130'];
                    }


                    //условие для нового API
                    if (!$photoUrl) {
                        $photo = max($photo['photo']['sizes']); //Выбираем изображение с максимальным разрешением
                        $photoUrl = $photo['url'];
                    }


                    ?>
                    <a class="c_gallery_link" target="_blank" href="<?= $photoUrl; ?>">
                        <img class="c_attachments_photo" src="<?= $photoUrl; ?>"/>
                    </a>
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
                    <?php
                    $photoUrl = '';

                    //старый вариант
                    if (isset($video['video']['photo_800'])) {
                        $photoUrl = $video['video']['photo_800'];
                    } elseif (isset($video['video']['photo_320'])) {
                        $photoUrl = $video['video']['photo_320'];
                    } elseif (isset($video['video']['photo_130'])) {
                        $photoUrl = $video['video']['photo_130'];
                    }


                    //условие для нового API
                    if(!$photoUrl){
                        $photo = max($video['video']['image']); //Выбираем изображение с максимальным разрешением
                        $photoUrl = $photo['url'];
                    }



                    ?>
                    <?php if ($i == 3):
                    $i = 1; ?></div>
                <div class="row"><?php endif; ?>

                    <div class="col-sm-6" style="position: relative; margin-top: 10px;">

                        <?php if (isset($video['video']['player'])): ?>
                            <div class="c_gallery_link" id="i_video_preview_<?= $video['video']['id']; ?>"
                                 data-iframe="true" data-poster="<?= $video['video']['img']; ?>"
                                 data-src="<?= $video['video']['player']; ?>">
                                <img src="<?= $video['video']['img']; ?>"
                                     style="width: 100%; height: 150px; object-fit: cover;"/>
                            </div>

                        <?php else: ?>
                            <a class="c_gallery_link" target="_blank" href="<?= $photoUrl; ?>">
                                <img class="c_attachments_photo" src="<?= $photoUrl; ?>" alt=""/></a>
                        <?php endif; ?>
                        <div>
                            <a href="https://vk.com/video<?= $video['video']['owner_id']; ?>_<?= $video['video']['id']; ?>"><?= \Lib_Text::Truncate($video['video']['title']); ?></a>
                        </div>
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
                    <?php if (isset($poll['poll'])): ?>
                        <div class="c_attachments_poll">
                            <div class="c_attachments_poll_open pull-right">Открытое голосование</div>
                            <div class="c_attachments_poll_title"><?= $poll['poll']['question']; ?></div>
                            <?php foreach ($poll['poll']['answers'] as $answer): ?>
                                <div class="c_attachments_poll_answer">
                                    <span class="glyphicon glyphicon-record"></span>
                                    <?= $answer['text']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($gifs)): ?>
            <div class="c_attachments_photos">
                <?php foreach ($gifs as $gif): ?>
                    <div class="c_gallery_link" data-iframe="true"
                         data-poster="<?= $gif['doc']['preview']['photo']['sizes'][0]['src']; ?>"
                         data-src="<?= $gif['doc']['url']; ?>">
                        <img class="c_attachments_photo"
                             src="<?= $gif['doc']['preview']['photo']['sizes'][0]['src']; ?>"/>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (count($docs)): ?>
            <?php foreach ($docs as $doc): ?>
                <div class="c_attachments_url">
                    <a href="<?= $doc['doc']['url']; ?>"><?= $doc['doc']['title']; ?></a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($post->ads): ?>
            <div class="c_link_ads submit_post">
                <div class="c_ads_div">Реклама в сообществе «<?= $group->title; ?>»</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="i_post_edit_<?= $post->postId; ?>"></div>
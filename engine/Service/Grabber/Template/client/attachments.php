<?php
$attachments = $vars['attachments'];
?>

<?php foreach ($attachments as $attachment): $uuid = \Lib_Uuid::getNext(); ?>
    <?php if ($attachment['type'] == 'photo'):
        $photoUrl = '';

        //старый вариант
        if (isset($attachment['photo']['id'])) {
            if (isset($attachment['photo']['photo_2560'])) {
                $photoUrl = $attachment['photo']['photo_2560'];
            } elseif (isset($attachment['photo']['photo_1280'])) {
                $photoUrl = $attachment['photo']['photo_1280'];
            } elseif (isset($attachment['photo']['photo_807'])) {
                $photoUrl = $attachment['photo']['photo_807'];
            } elseif (isset($attachment['photo']['photo_604'])) {
                $photoUrl = $attachment['photo']['photo_604'];
            } elseif (isset($attachment['photo']['photo_130'])) {
                $photoUrl = $attachment['photo']['photo_130'];
            }

            //условие для нового API
            if (!$photoUrl) {
                $photo = max($attachment['photo']['sizes']); //Выбираем изображение с максимальным разрешением
                $photoUrl = $photo['url'];
            }
        }



        ?>
        <?php if (isset($attachment['photo']['id'])): ?>
        <div class="uploader_file" id="<?= $uuid; ?>">
            <a href="<?= $photoUrl; ?>" target="_blank">
                <img style="width: 50px;" src="<?= $attachment['photo']['photo_130']; ?>">
            </a>
            <div class="uploader_close" title="Удалить"
                 onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $attachment['photo']['id']; ?>')"></div>
        </div>
    <?php else: ?>
        <div class="uploader_file" id="<?= $uuid; ?>">
            <a href="<?= $attachment['big']['url']; ?>" target="_blank">
                <img style="width: 50px;" src="<?= $attachment['small']['url']; ?>">
            </a>
            <div class="uploader_close" title="Удалить"
                 onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $attachment['id']; ?>')"></div>
        </div>
    <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>

<div class="row" style="margin-top: 20px;">
    <?php $i = 0;

    foreach ($vars['videos'] as $video):
    $i++;
    $uuid = \Lib_Uuid::getNext(); ?>
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
    $i = 1; ?></div><div class="row"><?php endif; ?>
<div class="col-sm-4" id="<?= $uuid; ?>" style="position: relative; margin-top: 10px;">
<?php if (isset($video['video']['player'])): ?>
<div class="c_gallery_link" id="i_video_preview_<?= $video['video']['id']; ?>" data-iframe="true"
     data-poster="<?= $video['video']['img']; ?>" data-src="<?= $video['video']['player']; ?>">
    <img src="<?= $video['video']['img']; ?>" style="width: 100%; height: 150px; object-fit: cover;"/>
    <div class="uploader_close" title="Удалить"
         onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $video['id']; ?>')"></div>
</div>
</div>
<?php else: ?>
    <a class="c_gallery_link" target="_blank" href="<?= $photoUrl; ?>"><img class="c_attachments_photo"
                                                                            src="<?= $video['video']['photo_130']; ?>"/></a>
<?php endif; ?>
<div>
    <a href="https://vk.com/video<?= $video['video']['owner_id']; ?>_<?= $video['video']['id']; ?>"><?= \Lib_Text::Truncate($video['video']['title']); ?></a>
</div>
<div class="uploader_close" title="Удалить"
     onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $video['id']; ?>')"></div></div>
    </div>
<?php endforeach; ?>
</div>
<?php foreach ($attachments as $attachment): if ($attachment['type'] != 'doc') {
        continue;
    }
    $uuid = \Lib_Uuid::getNext(); ?>
    <?php if ($attachment['doc']['ext'] == 'gif'): ?>
        <div class="c_attachments_url" id="<?= $uuid; ?>">
            <div class="close pull-right"
                 onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $attachment['doc']['id']; ?>')">
                <span class="glyphicon glyphicon-remove"></span>
            </div>
            <a href="<?= $attachment['doc']['url']; ?>" target="_blank">
                <img class="c_attachments_photo"
                     src="<?= $attachment['doc']['preview']['photo']['sizes'][0]['src']; ?>"/>
            </a>
        </div>
    <?php else: ?>
        <div class="c_attachments_url" id="<?= $uuid; ?>">
            <div class="close pull-right"
                 onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $attachment['doc']['id']; ?>')">
                <span class="glyphicon glyphicon-remove"></span>
            </div>
            <a href="<?= $attachment['doc']['url']; ?>" target="_blank"><?= $attachment['doc']['title']; ?></a>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

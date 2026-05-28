<?php
$attachments = $vars['attachments'];
$sortable = \Lib_Uuid::getNext();
?>
<div id="i_attachments_photos_<?= $sortable; ?>">
    <?php foreach ($attachments as $attachment): $uuid = \Lib_Uuid::getNext(); ?>
        <?php if ($attachment['type'] == 'photo'): ?>
            <div class="uploader_file" id="<?= $uuid; ?>">
                <a href="<?= $attachment['big']['url']; ?>" target="_blank">
                    <img style="width: 50px;" src="<?= $attachment['small']['url']; ?>">
                </a>
                <div class="uploader_close" title="Удалить"
                     onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $attachment['name']; ?>')"></div>
                <input type="hidden" name="sort[]" value="<?= $attachment['name']; ?>"/>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<div class="row" style="margin-top: 20px;">
    <?php $i = 0;

    foreach ($vars['videos'] as $video):
    $i++;
    $uuid = \Lib_Uuid::getNext(); ?>
    <?php if ($i == 4):
    $i = 1; ?></div><div class="row"><?php endif; ?>
<div class="col-sm-4" style="position: relative;" id="<?= $uuid; ?>">
    <div class="c_video_preview" id="<?= $video['id']; ?>">
        <img src="<?= $video['img']; ?>" style="width: 100%; height: 150px; object-fit: cover;"/>
        <div class="play_button btn btn-danger"
             onclick="$('#i_video_preview_<?= $video['id']; ?>').hide().after('<iframe type=text/html&quot; width=&quot;100%&quot; src=&quot;<?= $video['player']; ?>&quot; frameborder=&quot;0&quot;/>')">
            <span class="glyphicon glyphicon-play-circle" style="font-size: 18px;"></span>
        </div>
    </div>
    <a href="https://vk.com/video<?= $video['owner_id']; ?>_<?= $video['id']; ?>"><?= $video['title']; ?></a>
    <div class="uploader_close" title="Удалить"
         onclick="uploader.fileDelete('<?= $uuid; ?>', '<?= $video['id']; ?>')"></div>
</div>
</div>
<?php endforeach; ?>
</div>
<script>
    $('#i_attachments_photos_<?= $sortable; ?>').sortable();
</script>
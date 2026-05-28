<script>
    post_edit.videos = <?= json_encode($vars['items']); ?>;
</script>
<style>
    .c_video_add {
        position: absolute;
        top: 5px;
        right: 20px;
        font-size: 24px;
    }

    .play_button {
        display: none;
    }

    .c_video_preview:hover .play_button {
        display: block;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-top: -40px;
        margin-left: -20px;
    }
</style>

<div style="overflow-y: auto; height: 500px;">
    <div class="row">
        <?php $i = 0;

        foreach ($vars['items'] as $video):
        $i++; ?>
        <?php if ($i == 3):
        $i = 1; ?></div>
    <div class="row"><?php endif; ?>
        <div class="col-sm-6" style="position: relative;">
            <div class="c_video_add">
                <button class="btn btn-primary" style="color: #ffffff;" data-video-id="<?= $video['id']; ?>"
                        onclick="post_edit.video_add($(this))">
                    <span class="glyphicon glyphicon-plus-sign" style="font-size: 18px;"></span>
                </button>
            </div>
            <div class="c_video_preview" id="i_video_preview_<?= $video['id']; ?>">
                <img src="<?= $video['photo_320']; ?>" style="width: 100%; height: 150px; object-fit: cover;"/>
                <div class="play_button btn btn-danger"
                     onclick="$('#i_video_preview_<?= $video['id']; ?>').hide().after('<iframe type=text/html&quot; width=&quot;100%&quot; src=&quot;<?= $video['player']; ?>&quot; frameborder=&quot;0&quot;/>')">
                    <span class="glyphicon glyphicon-play-circle" style="font-size: 18px;"></span>
                </div>
            </div>
            <a href="https://vk.com/video<?= $video['owner_id']; ?>_<?= $video['id']; ?>"><?= $video['title']; ?></a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

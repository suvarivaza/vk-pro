<?php
$params = [
    'button' => $vars['button'] ?? 'uploader_pickfiles',
    'container' => $vars['container'] ?? 'uploader_container',
    'url' => $vars['url'],
    'max_file_size' => $vars['max_file_size'] ?? 10,
    'resize' => [
        'width' => $vars['resize']['width'] ?? 600,
        'height' => $vars['resize']['height'] ?? 400,
    ],
    'list' => $vars['list'] ?? [],
    'uuid' => $vars['uuid'],
];
$count = 0;
?>
<div class="uploader_container" id="<?= $params['container']; ?>">
    <?php $sorting = [];

    foreach ($params['list'] as $i => $file): $count++;
        $sorting[] = $file['key']; ?>
        <div class="uploader_file" id="<?= $i; ?>"><a href="<?= $file['url']; ?>" target="_blank"><img
                        src="<?= $file['url_preview']; ?>"/></a>
            <div class="uploader_close" title="Удалить"
                 onclick="uploader.fileDelete('<?= $i; ?>', '<?= $file['key']; ?>')"></div>
        </div>
    <?php endforeach; ?>
    <div class="uploader_button btn btn-default" id="<?= $params['button']; ?>" style="z-index: 100;">
        <div class="uploader_button_text">Добавить фото</div>
    </div>
</div>
<?php mb_convert_variables('utf-8', 'windows-1251', $params); ?>
<script type="text/javascript">
    uploader.count = <?= $count; ?>;
    uploader.max_count = <?= isset($vars['max_count']) && $vars['max_count'] ? $vars['max_count'] : 10; ?>;
    uploader.init(<?= json_encode($params); ?>);
</script>
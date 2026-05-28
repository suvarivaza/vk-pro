<ul class="breadcrumb">
    <li><strong><a href="/news">Новости</a></strong></li>
    <li><strong><?= $vars['page']['title']; ?></strong></li>
</ul>
<h1></h1>
<?= $vars['page']['text']; ?>
<?php if ($vars['page']['alias'] == 'contacts'): ?>
    <div id="map">
        <script type="text/javascript" charset="utf-8" async
                src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A4d20c2df56d0d64ef5ec5113f4cb1b7201bedf33aea793fcb0d5a887c3a378f0&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=true"></script>
    </div>
<?php endif; ?>
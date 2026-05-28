<?php
/** @var \Service\Users\Model_Users_Karma_Karma[] $list */
$list = $vars['list'];
?>
<ul class="breadcrumb">
    <li>
        <img src="/img/icons/32/icon-karma.png" width="30"/>
        <a href="/users/karma">Карма</a>
    </li>
</ul>

<div style="background-color: #ffffff">
    <div style="height: 30px;"></div>
    <div class="row">
        <div class="col-sm-5 text-center">
            <img src="/img/karma/users.png"/>
        </div>
        <div class="col-sm-6" style="background-color: #f4f4f4;">
            <p>Каждый пользователь сервиса <strong>vk-pro.top</strong> имеет собственный уровень кармы в системе</p>
        </div>
    </div>
    <div style="margin: 20px 30px;">
        <p>Карма формируется на основе качества и количества выполненных заданий</p>
    </div>

    <div class="row">
        <div class="col-sm-3 col-sm-offset-1">
            <div class="row">
                <div class="col-sm-6">
                    <img src="/img/karma/up.png"/>
                </div>
                <div class="col-sm-6">
                    <img src="/img/karma/down.png"/>
                </div>
            </div>
        </div>
        <div class="col-sm-7" style="background-color: #f4f4f4;">
            <p>Растет, когда пользователь выполняет задания, и падает, когда пользователь самовольно отменяет уже
                выполненное им задание</p>
        </div>
    </div>
    <div style="height: 30px;"></div>
    <div class="row">
        <div class="col-sm-2 text-right">
            <img src="/img/karma/attention.png" style="margin-top: 5px;"/>
        </div>
        <div class="col-sm-10">
            <p style="margin-top: 0; padding-top: 0;">Если, пользователь нарушает правила, такие как – убрал лайк, вышел
                из группы, отписался от страницы, удалил репост со стены, удалил свой комментарий, нарушил лимиты
                системы - получил бан аккаунта ВКонтакте и стал “собакой”, уровень его кармы стремительно падает</p>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-4 col-sm-offset-1" style="background-color: #f4f4f4; padding: 20px;">
            Иметь высокий уровень<br/>
            кармы в системе выгодно!
        </div>
        <div class="col-sm-5 col-sm-offset-1 text-center"
             style="background-color: #f4f4f4; color: #4f7299; font-size: 24px; padding: 20px;">
            Почему?
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-4 col-sm-offset-1">
            <img src="/img/karma/box1.png" style="border-bottom: 2px solid #4f7299; padding-bottom: 10px;"/>
        </div>
        <div class="col-sm-5 col-sm-offset-1">
            <img src="/img/karma/box2.png" style="border-bottom: 2px solid #4f7299; padding-bottom: 10px;"/>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-4 col-sm-offset-1">
            Пользователи с высоким уровнем кармы, имеют доступ к бо́льшему количеству заданий, имею доступ к выполнению
            спецзаданий и получают больше баллов за выполнение заданий
        </div>
        <div class="col-sm-5 col-sm-offset-1">
            Пользователи с низким уровнем кармы имеют доступ к значительно меньшему количеству заданий, т.к. каждый
            создатель задания имеет возможность не допускать его к выполнению, пользователей с уровнем кармы ниже
            определенного уровня
        </div>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="col-sm-2 col-sm-offset-1" style="text-align: center;">
            <img src="/img/karma/face.png"/>
            <br/>
            <img src="/img/karma/line.png">
        </div>
        <div class="col-sm-8">
            Если карма Вашего аккаунта имеет отрицательное значение, создатели заданий доверяют Вам значительно меньше,
            чем пользователям с высоким уровнем кармы, или не доверяют вообще. А потому, список доступных заданий может
            быть сильно ограничен
        </div>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="col-sm-2 col-sm-offset-1 text-center">
            <img src="/img/karma/karma.png"/>
        </div>
        <div class="col-sm-8">
            Выходом из сложившейся ситуации будет – поднимать уровень кармы вручную и выполнять доступные задания, не
            нарушая правил. Либо, использовать функцию – Очистка Кармы, и пообещать, более не нарушать правила сервиса
        </div>
    </div>
    <div style="height: 20px;"></div>
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th>Дата</th>
            <th>Карма</th>
            <th>До</th>
            <th>После</th>
            <th>Комментарий</th>
        </tr>
        <?php foreach ($list as $karma): ?>
            <tr>
                <td>
                    <?= date('d.m.Y H:i:s', $karma->dateCreate); ?>
                </td>
                <td>
                    <?= $karma->karma; ?>
                </td>
                <td>
                    <?= $karma->karmaFrom; ?>
                </td>
                <td>
                    <?= $karma->karmaTo; ?>
                </td>
                <td>
                    <?= $karma->comment; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
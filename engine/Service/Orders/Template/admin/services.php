<style>
    .small-box > .c_div_more_info {
        max-height: 0;
        transition: max-height 0.35s ease-out;
        overflow: hidden;
    }

    .small-box.active > .c_div_more_info {
        font-size: 14px;
        max-height: 500px;
        transition: max-height 0.25s ease-in;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Сервисы
        <small>Общая статистика</small>
    </h1>
</section>
<section>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua" id="i_div_auto">
                <div class="inner">
                    <h3><?= $vars['total']['auto']; ?></h3>
                    <p>Автоведение</p>
                </div>
                <div class="icon">
                    <img src="/img/icons/32/icon-auto-white.png" width="50"/>
                </div>
                <div class="c_div_more_info">
                    <table class="table">
                        <tr>
                            <td>Купленные</td>
                            <td><?= $vars['total']['auto'] - $vars['free']['auto']; ?></td>
                        </tr>
                        <tr>
                            <td>Тестовый период</td>
                            <td><?= $vars['free']['auto']; ?></td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:$('#i_div_auto').toggleClass('active')" class="small-box-footer">Подробная
                    информация <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green" id="i_div_grabber">
                <div class="inner">
                    <h3><?= $vars['total']['grabber']; ?></h3>
                    <p>Граббер</p>
                </div>
                <div class="icon">
                    <img src="/img/icons/32/icon-grabber-white.png" width="50"/>
                </div>
                <div class="c_div_more_info">
                    <table class="table">
                        <tr>
                            <td>Купленные</td>
                            <td><?= $vars['total']['grabber'] - $vars['free']['grabber']; ?></td>
                        </tr>
                        <tr>
                            <td>Тестовый период</td>
                            <td><?= $vars['free']['grabber']; ?></td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:$('#i_div_grabber').toggleClass('active')" class="small-box-footer">Подробная
                    информация <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow" id="i_div_posting">
                <div class="inner">
                    <h3><?= $vars['total']['posting']; ?></h3>
                    <p>Автопостинг</p>
                </div>
                <div class="icon">
                    <img src="/img/icons/32/icon-post-white.png" width="50"/>
                </div>
                <div class="c_div_more_info">
                    <table class="table">
                        <tr>
                            <td>Купленные</td>
                            <td><?= $vars['total']['posting'] - $vars['free']['posting']; ?></td>
                        </tr>
                        <tr>
                            <td>Тестовый период</td>
                            <td><?= $vars['free']['posting']; ?></td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:$('#i_div_posting').toggleClass('active')" class="small-box-footer">Подробная
                    информация <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple" id="i_div_special">
                <div class="inner">
                    <h3><?= $vars['total']['special']; ?></h3>
                    <p>Спецзадания</p>
                </div>
                <div class="icon">
                    <img src="/img/icons/32/icon-special-white.png" width="50"/>
                </div>
                <div class="c_div_more_info">
                    <table class="table">
                        <tr>
                            <td>Купленные</td>
                            <td><?= $vars['total']['special'] - $vars['free']['special']; ?></td>
                        </tr>
                        <tr>
                            <td>Тестовый период</td>
                            <td><?= $vars['free']['special']; ?></td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:$('#i_div_special').toggleClass('active')" class="small-box-footer">Подробная
                    информация <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
</section>
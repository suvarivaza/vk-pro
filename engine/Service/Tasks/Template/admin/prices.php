<h1>
    Прайсы заданий
    <small class="pull-right">
        <a class="btn btn-primary" href="/admin/tasks/prices/percents">Таргетинг</a>
    </small>
</h1>
<form method="post">
    <table class="<?= DEFAULT_TABLE_CLASS; ?>">
        <tr>
            <th rowspan="2" class="text-center" style="vertical-align: middle;">Тип задания</th>
            <th rowspan="2" class="text-center" style="vertical-align: middle;">Создание</th>
            <th colspan="2" class="text-center">Выполнение</th>
        </tr>
        <tr>
            <th class="text-center">Положительная карма</th>
            <th class="text-center">Отрицательная карма</th>
            <th class="text-center">Карма от 75%</th>
        </tr>
        <tr>
            <td>Лайки</td>
            <td>
                <input class="form-control" name="price_likes_buy" value="<?= $vars['settings']['price_likes_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_likes_sell"
                       value="<?= $vars['settings']['price_likes_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_likes_sell_negative"
                       value="<?= $vars['settings']['price_likes_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_likes_sell_positive"
                       value="<?= $vars['settings']['price_likes_sell_positive']; ?>">
            </td>
        </tr>
        <tr>
            <td>Репосты</td>
            <td>
                <input class="form-control" name="price_reposts_buy"
                       value="<?= $vars['settings']['price_reposts_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_reposts_sell"
                       value="<?= $vars['settings']['price_reposts_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_reposts_sell_negative"
                       value="<?= $vars['settings']['price_reposts_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_reposts_sell_positive"
                       value="<?= $vars['settings']['price_reposts_sell_positive']; ?>">
            </td>
        </tr>
        <tr>
            <td>Комментарии</td>
            <td>
                <input class="form-control" name="price_comments_buy"
                       value="<?= $vars['settings']['price_comments_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_comments_sell"
                       value="<?= $vars['settings']['price_comments_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_comments_sell_negative"
                       value="<?= $vars['settings']['price_comments_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_comments_sell_positive"
                       value="<?= $vars['settings']['price_comments_sell_positive']; ?>">
            </td>
        </tr>
        <tr>
            <td>Подписки</td>
            <td>
                <input class="form-control" name="price_join_buy" value="<?= $vars['settings']['price_join_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_join_sell" value="<?= $vars['settings']['price_join_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_join_sell_negative"
                       value="<?= $vars['settings']['price_join_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_join_sell_positive"
                       value="<?= $vars['settings']['price_join_sell_positive']; ?>">
            </td>
        </tr>

        <tr>
            <td>Друзья</td>
            <td>
                <input class="form-control" name="price_friends_buy"
                       value="<?= $vars['settings']['price_friends_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_friends_sell"
                       value="<?= $vars['settings']['price_friends_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_friends_sell_negative"
                       value="<?= $vars['settings']['price_friends_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_friends_sell_positive"
                       value="<?= $vars['settings']['price_friends_sell_positive']; ?>">
            </td>
        </tr>

        <tr>
            <td>Голосования</td>
            <td>
                <input class="form-control" name="price_polls_buy" value="<?= $vars['settings']['price_polls_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_polls_sell"
                       value="<?= $vars['settings']['price_polls_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_polls_sell_negative"
                       value="<?= $vars['settings']['price_polls_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_polls_sell_positive"
                       value="<?= $vars['settings']['price_polls_sell_positive']; ?>">
            </td>
        </tr>
        <tr>
            <td>Просмотры постов</td>
            <td>
                <input class="form-control" name="price_views_buy" value="<?= $vars['settings']['price_views_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_views_sell"
                       value="<?= $vars['settings']['price_views_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_views_sell_negative"
                       value="<?= $vars['settings']['price_views_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_views_sell_positive"
                       value="<?= $vars['settings']['price_views_sell_positive']; ?>">
            </td>
        </tr>
        <tr>
            <td>Просмотры видео</td>
            <td>
                <input class="form-control" name="price_video_buy" value="<?= $vars['settings']['price_video_buy']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_video_sell"
                       value="<?= $vars['settings']['price_video_sell']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_video_sell_negative"
                       value="<?= $vars['settings']['price_video_sell_negative']; ?>">
            </td>
            <td>
                <input class="form-control" name="price_video_sell_positive"
                       value="<?= $vars['settings']['price_video_sell_positive']; ?>">
            </td>
        </tr>
    </table>
    <button class="btn btn-primary btn-lg">Сохранить</button>
</form>
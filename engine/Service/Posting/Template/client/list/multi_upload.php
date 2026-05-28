<form id="i-form-multi" method="post" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="action" value="multi_upload"/>
    <input type="hidden" name="uuid" value="<?= $vars['uploader']['uuid']; ?>"/>
    <div class="form-group">
        <div class="col-sm-4">
            <select id="i-multi-count" name="count" class="form-control form-span"
                    placeholder="Количество фото на пост">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </div>
        <div class="col-sm-4">
            <select name="frequency" id="i-multi-frequency" class="form-control form-span"
                    placeholder="Частота постов в ВК">
                <option value="5">Каждые 5 минут</option>
                <option value="10">Каждые 10 минут</option>
                <option value="15">Каждые 15 минут</option>
                <option value="20">Каждые 20 минут</option>
                <option value="30">Каждые 30 минут</option>
                <option value="45">Каждые 45 минут</option>
                <option value="60">Каждый час</option>
                <option value="90">Каждые полтора часа</option>
                <option value="120">Каждые два часа</option>
            </select>
        </div>
        <div class="col-sm-4">
            <select class="form-control form-span" id="i-select-hidden" placeholder="Ежедневное время публикации"
                    style="display: none;"></select>
            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td>с</td>
                    <td style="width: 50%;">
                        <select id="i_multi_from" name="from" class="form-control form-span">
                            <option value="00">00</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08" selected="selected">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                        </select>
                    </td>
                    <td>&nbsp;до</td>
                    <td style="width: 50%;">
                        <select id="i_multi_to" name="to" class="form-control">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20" selected="selected">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12 text-right">
            <input id="i_multi_ownerId" type="checkbox" name="signature" style="margin-top: 2px;">
            <label for="i_multi_ownerId" style="margin-top: -2px; display: inline-block;">Добавить подпись к
                постам</label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php \STPL::Display('controls/upload_dialog_multi', $vars['uploader']); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            <button class="btn btn-danger" type="button" id="i-button-multi-cancel">Отмена</button>
        </div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-primary" id="i-button-multi-submit" type="button">Создать посты</button>
        </div>
    </div>
</form>
<script>
    $('#i-button-multi-submit').click(function () {
        $.ajax({
            type: "post",
            dataType: "json",
            data: $('#i-form-multi').serialize(),
            beforeSend: function () {
                $('#i_dialog').modal('show');
                $('#i_dialog_data').hide();
                $('#i_dialog_error').hide();
                $('#i_dialog_progress').show();
            },
            complete: function () {
                $('#i_dialog_progress').hide();
            },
            error: function () {
                $('#i_dialog_data').show();
                $('#i_dialog_error').html('<div class="alert alert-danger">Не удалось выполнить запрос.</div>').show();
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                } else {
                    $('#i_dialog_data').show();
                    $('#i_dialog_error').html('<div class="alert alert-danger">' + data.errorText + '</div>').show();
                }
            }
        });
    });
    refreshLabels();
</script>
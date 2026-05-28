<?php
/** @var \Service\Tasks\Model_Specials_Groups_Group $group */
$group = $vars['group'];
?>
<form method="get" class="form-horizontal" role="form" style="text-align: left;">
    <input type="hidden" name="action" value="setGroup"/>
    <input type="hidden" name="months" value="<?= $vars['months']; ?>"/>
    <?php if ($vars['isFree']): ?>
        <input type="hidden" name="isFree" value="true"/>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-sm-12">
            <select class="form-control" name="groupId" id="i_groupId">
                <option value="0">Выберите группу</option>
                <?php foreach ($vars['groups']['items'] as $item): ?>
                    <option data-image="<?= $item['photo_50']; ?>"
                            value="<?= $item['id']; ?>"<?php if (isset($group) && $group->ownerId == $item['id']): ?> selected="selected"<?php endif; ?>>
                        <?= $item['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="text-right">
        <button onclick="special.saveClick();" type="button" class="button-green btn-block">Активировать группу</button>
    </div>
</form>
<script>
    $('#i_groupId').msDropDown();
</script>
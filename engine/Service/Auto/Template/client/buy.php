<?php
/** @var Model_Groups_Group $group */
use Service\Posting\Model_Groups_Group;

?>
<h1>Мои сообщества</h1>

<form method="get" class="form-horizontal" role="form">
    <div class="form-group">
        <div class="col-sm-10">
            <select class="form-control" name="groupId" id="i_groupId">
                <option value="0">Выберите группу</option>
                <?php foreach ($vars['groups']['items'] as $item): ?>
                    <option data-image="<?= $item['photo_50']; ?>" value="<?= $item['id']; ?>">
                        <?= $item['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-2">
            <button type="submit" class="btn btn-primary">Перейти</button>
        </div>
    </div>
</form>
<?php if (count($vars['my'])): ?>
    <h2>Быстрый переход</h2>
    <ul class="list-group">
        <?php foreach ($vars['my'] as $group): ?>
            <li class="list-group-item">
                <?php if ($group->photo): ?>
                    <img class="img-circle" src="<?= $group->photo; ?>" style="width: 50px;"/>
                <?php endif; ?>
                <a href="/posting/<?= $group->groupId; ?>"><?= $group->title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<script>
    $('#i_groupId').msDropDown();
</script>
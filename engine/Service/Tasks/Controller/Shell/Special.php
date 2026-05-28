<?php

namespace Service\Tasks;

/**
 * @property \Lib_Curl $_curl
 */
class Controller_Shell_Special extends Controller_Shell
{
    public function A_UpdateActive()
    {
        $query = $this->factoryTasks->specialGroups->query()->limit(10000)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', true)
            ->fieldValue('dateValid', '<', time());

        $it = $query->iteratorForSave();
        /** @var Model_Specials_Groups_Group $group */
        foreach ($it as $group) {
            $group->isActive = false;
            $this->factoryTasks->specialGroups->save($group);
        }

        $query = $this->factoryTasks->specialGroups->query()->limit(10000)->sort('groupId', 'ASC');
        $query->filter->fieldValue('isActive', '=', false)
            ->fieldValue('dateValid', '>', time());

        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $group->isActive = true;
            $this->factoryTasks->specialGroups->save($group);
        }
    }
}

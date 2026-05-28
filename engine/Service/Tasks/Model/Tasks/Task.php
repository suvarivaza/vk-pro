<?php

namespace Service\Tasks;

/**
 * Class Model_Tasks_Task
 *
 * @package Service\News
 *
 * @property int $taskId
 * @property string $type
 * @property int $userId
 * @property int $dateCreate
 * @property int $dateLast
 * @property string $url
 * @property string $vkType
 * @property string $ownerId
 * @property int $ownerType
 * @property string $itemId
 * @property string $commentId
 * @property string $reason
 * @property bool $isSpecial
 * @property bool $isSpecialInvite
 * @property int $specialId
 * @property bool $isTemplate
 * @property int $templateId
 * @property int $pollId
 * @property bool $isAnonymous
 * @property int $answerId
 * @property string $answerTitle
 * @property string $answerIds
 * @property string $title
 * @property int $minKarma
 * @property int $count
 * @property int $countReady
 * @property int $countReadyBot
 * @property int $countMinute
 * @property int $count10Min
 * @property int $countHour
 * @property int $countDay
 * @property int $countRemain
 * @property bool $followersOnly
 * @property bool $newFollowers
 * @property bool $prior
 * @property bool $active
 * @property bool $isDel
 * @property int $isDelDate
 * @property int $sex
 * @property int $ageFrom
 * @property int $ageTo
 * @property int $cityId
 * @property int $countryId
 * @property int $relation
 * @property int $avatarCount
 * @property int $filled
 * @property int $pageAge
 * @property int $followersCount
 * @property int $interestingPage
 * @property int $frequencyPost
 * @property bool $targeting
 * @property int $commentType
 * @property float $price
 * @property float $sum
 * @property int $age_limits
 */
class Model_Tasks_Task extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Tasks */
    protected $_factory;

    public function __construct(Model_Tasks $factory)
    {
        parent::__construct();
        $this->_factory = $factory;
    }

    /**
     * @return array
     */
    public static function GetPropertiesTypes()
    {
        if (null === self::$_PropertiesTypes) {
            self::$_PropertiesTypes = [
                'taskId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'type' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateLast' => self::TYPE_TIMESTAMP,
                'url' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'vkType' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'ownerId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'ownerType' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'itemId' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'commentId' => self::TYPE_STRING | self::FLAG_NOT_NULL,

                'isSpecial' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isSpecialInvite' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'specialId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isTemplate' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'templateId' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'pollId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'isAnonymous' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'answerId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'answerTitle' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'answerIds' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'minKarma' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'count' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countReady' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countReadyBot' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'countMinute' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'count10Min' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countHour' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countDay' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'countRemain' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'followersOnly' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'newFollowers' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'prior' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'active' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDel' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'isDelDate' => self::TYPE_TIMESTAMP,
                'photo' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'sex' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'ageFrom' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'ageTo' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'cityId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countryId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'relation' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'avatarCount' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'filled' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'pageAge' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'followersCount' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'interestingPage' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'frequencyPost' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'targeting' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'commentType' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'comments' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'price' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'sum' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'age_limits' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'reason' => self::TYPE_STRING | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function setPhoto($photo)
    {
        if (!is_array($photo)) {
            $photo = [];
        }
        $this->photo = json_encode($photo, JSON_UNESCAPED_UNICODE);
    }

    public function getPhoto()
    {
        $photo = json_decode($this->photo, true);

        if (!is_array($photo)) {
            $photo = [];
        }

        return $photo;
    }

    public function getComments()
    {
        $comments = json_decode($this->comments, true);

        if (!is_array($comments)) {
            $comments = [];
        }

        return $comments;
    }

    public function setComments($comments)
    {
        $this->comments = json_encode($comments, JSON_UNESCAPED_UNICODE);
    }

    public function getUser()
    {
        $factoryUsers = new \Service\Users\Model_Factory();
        $user = $factoryUsers->users->getById($this->userId);

        return $user;
    }
}

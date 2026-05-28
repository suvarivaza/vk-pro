<?php

namespace Service\Auto;

use Lib_ORM_Object;

/**
 * Class Model_Faq_Faq
 *
 * @package Service\Faq
 *
 * @property int $templateId
 * @property int $autoId
 * @property int $userId
 * @property int $groupId
 * @property string $type
 * @property int $minKarma
 * @property bool $prior
 * @property bool $fromGroupOnly
 * @property int $attachmentType
 * @property bool $adsOut
 * @property string $title
 * @property int $dateCreate,
 * @property int $dateValid,
 * @property bool $isActive
 * @property bool $isArchive
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
 * @property float $balanceLimit
 * @property float $balanceRemain
 * @property int $weekDay
 * @property int $weekDate
 * @property int $hourFrom
 * @property int $hourTo
 * @property int $hourMax
 * @property int $postId
 * @property int $countFrom
 * @property int $countTo
 * @property int $specialId
 */
class Model_Autos_Templates_Template extends Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Autos_Groups */
    protected $_factory;

    public function __construct(Model_Autos_Templates $factory)
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
                'templateId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'autoId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'groupId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,

                'type' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'minKarma' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'prior' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'fromGroupOnly' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'attachmentType' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'adsOut' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'title' => self::TYPE_STRING | self::FLAG_NOT_NULL,

                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateValid' => self::TYPE_TIMESTAMP,
                'isActive' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isArchive' => self::TYPE_BOOL | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
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
                'balanceLimit' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'balanceRemain' => self::TYPE_FLOAT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,

                'weekDay' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'weekDate' => self::TYPE_TIMESTAMP,
                'hourFrom' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'hourTo' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'hourMax' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'postId' => self::TYPE_INT | self::FLAG_NOT_NULL,

                'countFrom' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'countTo' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'specialId' => self::TYPE_INT | self::FLAG_NOT_NULL,
            ];
        }

        return self::$_PropertiesTypes;
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
}

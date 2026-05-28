<?php

namespace Service\Users;

/**
 * Class Model_Users_User
 *
 * @package Service\Users
 *
 * @property int $userId
 * @property int $parentId
 * @property int $pParentId
 * @property int $ppParentId
 * @property int $uid
 * @property string $identity
 * @property string $network
 * @property string $login
 * @property string $password
 * @property string $ceed
 * @property string $token
 * @property int $userType
 * @property string $name
 * @property string $lastName
 * @property string $firstName
 * @property string $secondName
 * @property string $email
 * @property string $phone
 * @property string $icq
 * @property string $skype
 * @property int $confirmed
 * @property string $restore
 * @property int $dateCreate
 * @property int $dateUpdate
 * @property string $year
 * @property int $sex
 * @property bool $visible
 * @property int $countryId
 * @property int $cityId
 * @property string $country
 * @property string $city
 * @property string $profile
 * @property string $access_token
 * @property int $access_token_expire
 * @property bool $token_require
 * @property string $scope
 * @property int $vkDateCreate
 * @property int $relation
 * @property int $followersCount
 * @property int $avatarCount
 * @property int $partCount
 * @property int $pagesCount
 * @property int $frequency
 * @property int $age
 * @property int $pageAge
 * @property float $balance
 * @property float $balanceRef
 * @property float $karma
 * @property int $lastLogin
 * @property int $lastCheck
 * @property int $pollsCountDay
 * @property int $pollsCountHour
 * @property int $pollsCount10Min
 * @property int $pollsCountMinute
 * @property int $joinCountDay
 * @property int $joinCountHour
 * @property int $joinCount10Min
 * @property int $joinCountMinute
 * @property int $friendsCountDay
 * @property int $friendsCountHour
 * @property int $friendsCount10Min
 * @property int $friendsCountMinute
 * @property int $likesCountDay
 * @property int $likesCountHour
 * @property int $likesCount10Min
 * @property int $likesCountMinute
 * @property int $repostsCountDay
 * @property int $repostsCountHour
 * @property int $repostsCount10Min
 * @property int $repostsCountMinute
 * @property int $commentsCountDay
 * @property int $commentsCountHour
 * @property int $commentsCount10Min
 * @property int $commentsCountMinute
 * @property bool $isRefferer
 * @property string $referrerUrl
 * @property string $qiwi_prefix
 * @property string $qiwi
 * @property int $karmaMinus
 * @property int $isFree
 * @property bool $ban
 * @property int $bad
 * @property int $banDate
 * @property int $bonus
 * @property int $isBot
 * @property int $age_limits
 * @property bool $badEmail
 */
class Model_Users_User extends \Lib_ORM_Object
{
    private static $_PropertiesTypes;
    public $passwordConfirm = '';
    /** @var Model_Users */
    protected $_factory;
    private $karmaPrice = 0;

    public function __construct(Model_Users $factory)
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
                'userId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL | self::FLAG_AUTOINCREMENT,
                'parentId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pParentId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'ppParentId' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'uid' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'identity' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'network' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'login' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'password' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'ceed' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'token' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'userType' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'name' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'lastName' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'firstName' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'secondName' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'email' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'phone' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'icq' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'skype' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'dateCreate' => self::TYPE_TIMESTAMP,
                'dateUpdate' => self::TYPE_TIMESTAMP,
                'confirmed' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'restore' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'year' => self::TYPE_STRING,
                'sex' => self::TYPE_INT,
                'photos' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'visible' => self::TYPE_BOOL,
                'countryId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'cityId' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'country' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'city' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'profile' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'access_token' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'access_token_expire' => self::TYPE_TIMESTAMP,
                'token_require' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'scope' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'vkDateCreate' => self::TYPE_TIMESTAMP,
                'relation' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'followersCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'avatarCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'partCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pagesCount' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'frequency' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'age' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pageAge' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'balance' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'balanceRef' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'karma' => self::TYPE_FLOAT | self::FLAG_NOT_NULL,
                'lastLogin' => self::TYPE_TIMESTAMP,
                'lastCheck' => self::TYPE_TIMESTAMP,

                'pollsCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pollsCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pollsCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'pollsCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'joinCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'joinCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'joinCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'joinCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'friendsCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'friendsCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'friendsCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'friendsCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'likesCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'likesCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'likesCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'likesCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'repostsCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'repostsCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'repostsCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'repostsCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'commentsCountDay' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'commentsCountHour' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'commentsCount10Min' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'commentsCountMinute' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,

                'isRefferer' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'referrerUrl' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'qiwi_prefix' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'qiwi' => self::TYPE_STRING | self::FLAG_NOT_NULL,
                'karmaMinus' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,
                'isFree' => self::TYPE_INT | self::FLAG_UNSIGNED | self::FLAG_NOT_NULL,

                'ban' => self::TYPE_BOOL | self::FLAG_NOT_NULL,
                'bad' => self::TYPE_INT | self::FLAG_NOT_NULL,
                'banDate' => self::TYPE_TIMESTAMP,

                'bonus' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'isBot' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'age_limits' => self::TYPE_INT | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
                'badEmail' => self::TYPE_BOOL | self::FLAG_NOT_NULL | self::FLAG_UNSIGNED,
            ];
        }

        return self::$_PropertiesTypes;
    }

    public function check()
    {
        $errors = [];

        if (!$this->password || $this->password == 'd41d8cd98f00b204e9800998ecf8427e') {
            $errors[] = 'Необходимо указать пароль';
        }

        if ($this->password !== $this->passwordConfirm) {
            $errors[] = 'Пароль и подтверждение не совпадают';
        }

        if (!$this->name) {
            $errors[] = 'Укажите Ваше имя';
        }

        return $errors ?: null;
    }

    public function getCookie()
    {
        if ($this->autoCookie) {
            return unserialize($this->autoCookie);
        }

        return [];
    }

    public function setCookie($cookie)
    {
        $this->autoCookie = serialize($cookie);
    }

    public function getShortName()
    {
        $name = ucfirst($this->lastName) . ' ' . ucfirst(mb_substr($this->firstName, 0,
                1)) . '. ' . ucfirst(mb_substr($this->secondName, 0, 1)) . '.';

        return $name;
    }

    public function setPhotos($photos)
    {
        $this->photos = serialize($photos);
    }

    public function getScopes()
    {
        return explode(',', $this->scope);
    }

    public function getQuality()
    {
        $photos = $this->getPhotos();

        if (!isset($photos['big']['url'])) {
            $result[] = 'Необхиодимо наличие аватара';
        }

        if ($this->followersCount < 5) {
            $result[] = 'Необходимо минимум 5 друзей';
        }
    }

    public function getPhotos()
    {
        return unserialize($this->photos);
    }

    public function getKarmaPrice()
    {
        if (!$this->karmaPrice) {
            if ($this->karmaMinus >= 3) {
                $this->karmaMinus = 3;
            }

            if ($this->karmaMinus <= 1) {
                $this->karmaMinus = 1;
            }

            $settings = \Service\Orders\Model_Config::getSettings();

            $this->karmaPrice = $settings['karma' . $this->karmaMinus]['price'];
        }

        return $this->karmaPrice;
    }
}

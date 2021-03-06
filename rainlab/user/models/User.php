<?php namespace RainLab\User\Models;

use URL;
use Mail;
use October\Rain\Auth\Models\User as UserBase;
use RainLab\User\Models\Settings as UserSettings;

class User extends UserBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'users';

    /**
     * Validation rules
     */
    public $rules = [
        'username' => 'required|between:2,64|unique:users',
        'email' => 'required|between:3,64|email|unique:users',
        'password' => 'required:create|between:4,64|confirmed',
        'password_confirmation' => 'required_with:password|between:4,64'
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'group' => ['RainLab\User\Models\Group', 'table' => 'rainlab_user_groups_relation', 'order' => 'created_at desc']
    ];

    public $belongsTo = [
        'country' => ['RainLab\User\Models\Country'],
        'state'   => ['RainLab\User\Models\State'],
    ];

    public $attachOne = [
        'avatar' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'surname',
        'login',
        'email',
        'password',
        'password_confirmation',
        'company',
        'phone',
        'street_addr',
        'city',
        'zip',
        'country',
        'state'
    ];

    /**
     * Purge attributes from data set.
     */
    protected $purgeable = ['password_confirmation'];

    public static $loginAttribute = null;

    /**
     * @var array List of attribute names which are json encoded and decoded from the database.
     */
    // protected $jsonable = ['permissions'];

    /**
     * @return string Returns the name for the user's login.
     */
    public function getLoginName()
    {
        if (static::$loginAttribute !== null)
            return static::$loginAttribute;

        return static::$loginAttribute = UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    /**
     * Before validation event
     * @return void
     */
    public function beforeValidate()
    {
        /*
         * When the username is not used, the email is substituted.
         */
        if (
            (!$this->username) ||
            ($this->isDirty('email') && $this->getOriginal('email') == $this->username)
        ) {
            $this->username = $this->email;
        }
    }

    public function getCountryOptions()
    {
        return Country::getNameList();
    }

    public function getStateOptions()
    {
        return State::getNameList($this->country_id);
    }

    /**
     * Gets a code for when the user is persisted to a cookie or session which identifies the user.
     * @return string
     */
    public function getPersistCode()
    {
        if (!$this->persist_code)
            return parent::getPersistCode();

        return $this->persist_code;
    }

    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getAvatarThumb($size = 25, $default = null)
    {
        if (!$default)
            $default = 'mm'; // Mystery man

        if ($this->avatar)
            return $this->avatar->getThumb($size, $size);
        else
            return '//www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s='.$size.'&d='.urlencode($default);
    }

    /**
     * Sends the confirmation email to a user, after activating
     * @param  string $code
     * @return void
     */
    public function attemptActivation($code)
    {
        $result = parent::attemptActivation($code);
        if ($result === false) {
            return false;
        }

        if ($mailTemplate = UserSettings::get('welcome_template')) {
            $data = [
                'name' => $this->name,
                'email' => $this->email
            ];

            Mail::send($mailTemplate, $data, function($message) {
                $message->to($this->email, $this->name);
            });
        }

        return true;
    }

    /**
     * Looks up a user by their email address.
     * @return self
     */
    public static function findByEmail($email)
    {
        if (!$email) return;
        return self::where('email', $email)->first();
    }

    /**
     * fields.yaml文件中type=radio触发，返回数据库中的字段，可能需要返回form对象
     */
    public function getGroupOptions($param)
    {
        echo '<pre>';
        var_dump($param);
        exit;
    }

    // public function beforeSave()
    // {
    //     echo '<pre>';
    //     print_r($_REQUEST);
    //     exit;
    // }

    /**
     * Validate any set permissions.
     * @param array $permissions
     * @return void
     */
    public function setPermissionsAttribute($permissions)
    {
        $permissions = json_decode($permissions, true);
        foreach($permissions as $module => &$pms) {
            if( $module == 'superuser' ) {
                unset($permissions['superuser']);
                continue;
            }
            foreach ($pms as $permission => &$value) {
                if (!in_array($value = (int)$value, $this->allowedPermissionsValues))
                    throw new InvalidArgumentException(sprintf('Invalid value "%s" for permission "%s" given.', $value, $permission));

                if ($value === 0)
                    unset($permissions[$module][$permission]);
            }
        }


        $this->attributes['permissions'] = (!empty($permissions)) ? json_encode($permissions) : '';
    }
}

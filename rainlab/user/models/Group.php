<?php namespace RainLab\User\Models;

use URL;
use Mail;
use October\Rain\Auth\Models\User as UserBase;
use RainLab\User\Models\Settings as UserSettings;

class Group extends UserBase
{

    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_user_groups';

    /**
     * @var array Fillable fields
     */
    // protected $fillable = ['name', 'description'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required',
    ];

    /**
     * @var array Auto generated slug
     */
    protected $slugs = ['slug' => 'name'];

    public $belongsToMany = [
        'user' => ['RainLab\User\Models\user', 'table' => 'rainlab_user_groups_relation', 'order' => 'created_at desc'],
        'permission' => ['Amazingsurge\Permissions\Models\Permission', 'table' => 'amazingsurge_permissions_groups_relation', 'key' => 'group_id']
    ];

    public function getUserCountAttribute()
    {
        return $this->user()->count();
    }
}

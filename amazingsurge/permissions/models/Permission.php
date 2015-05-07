<?php namespace Amazingsurge\Permissions\Models;

use Auth;
use Model;

/**
 * Permission Model
 */
class Permission extends Model
{

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'amazingsurge_permissions_permissions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'module' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * 验证以及格式化内容
     */
    public function beforeSave()
    {
        // 大小写转换
        $this->name = strtolower($this->name);
        $this->module = ucfirst(strtolower($this->module));

        // 验证格式
        $pattern = '/[a-z0-9]+.[a-z0-9]+/';
        return $pattern;
    }

    // public function afterFetch()
    // {
    //     echo '<pre>';
    //     print_r( $this );
    //     echo '</pre>';
    // }

    /**
     * 验证是否拥有权限
     */
    public static function hasPermission($permission)
    {
        if (!Auth::check()) {
            return null;
        }

        // 优先检查个人权限设定
        $user             = Auth::getUser();
        $permissions      = $user->permissions;
        $permissionsArray = [];
        if(is_array($permissions)){
            foreach($permissions as $modules) {
                foreach($modules as $k => $v) {
                    $permissionsArray[$k] = $v;
                }
            }

            if(isset($permissionsArray[$permission])) {
                return $permissionsArray[$permission] == 1 ? true : false;
            }
        }

        // 检查用户组权限
        $groups = [];
        foreach($user->group as $group) {
            $groups[] = $group->id;
        }

        $array = self::leftJoin('amazingsurge_permissions_groups_relation', 'id', '=', 'permission_id', $type = 'inner')
                    ->whereIn('group_id', $groups)
                    ->groupBy('id')
                    ->get()
                    ->toArray();

        foreach($array as $v) {
            if($v['name'] == $permission) return true;
        }

        return false;
    }
}
<?php namespace Amazingsurge\Permissions\Models;

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
}
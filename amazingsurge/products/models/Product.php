<?php namespace Amazingsurge\Products\Models;

use Auth;
use Model;

/**
 * Product Model
 */
class Product extends Model
{

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'amazingsurge_products_products';

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
        'slug' => 'required',
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
     * 检查用户是否购买了产品
     * 
     * @param  string $slug 产品标识
     * @return bool
     */
    public static function hasProduct($slug)
    {
        if (!Auth::check()) {
            return null;
        }

        // 获取产品与用户关联
        $user = Auth::getUser();
        $ret  = self::leftJoin('amazingsurge_products_product_user', 'id', '=', 'product_id', $type = 'inner')
                    ->where('slug', $slug)
                    ->where('user_id', $user->id)
                    ->get()
                    ->toArray();
        if(empty($ret)) return false;
        return true;
    }
}
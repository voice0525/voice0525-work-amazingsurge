<?php namespace Amazingsurge\Permissions\Models;

use Model;

/**
 * Group Model
 */
class Group extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'amazingsurge_permissions_groups_relation';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'group' => ['RainLab\User\Models\Group'],
        'permission' => ['Amazingsurge\Permissions\Models\Permission']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
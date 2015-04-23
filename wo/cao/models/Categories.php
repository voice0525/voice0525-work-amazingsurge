<?php namespace Wo\Cao\Models;

use Model;

/**
 * categories Model
 */
class Categories extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wo_cao_categories';

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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
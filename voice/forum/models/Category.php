<?php namespace Voice\Forum\Models;

use Model;
use ApplicationException;
use VoIce\Forum\Models\Channel;

/**
 * Category Model
 */
class Category extends Model
{

    // use \October\Rain\Database\Traits\Sluggable;
    // use \October\Rain\Database\Traits\Validation;
    // use \October\Rain\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_forum_category';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['title', 'description', 'parent_id'];

    /**
     * @var array The attributes that should be visible in arrays.
     */
    // protected $visible = ['title', 'description'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required'
    ];

    /**
     * @var array Auto generated slug
     */
    protected $slugs = ['slug' => 'title'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = ['title', 'description'];

    public $belongsToMany = [
        'channels' => ['Voice\Forum\Models\Channel', 'table' => 'rainlab_forum_channels_categories', 'order' => 'created_at desc']
    ];

    /**
     * Add translation support to this model, if available.
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (!class_exists('RainLab\Translate\Behaviors\TranslatableModel'))
            return;

        self::extend(function($model){
            $model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
        });
    }

    /**
     * 根据频道获取分类
     *
     * @access public
     * @param  string $channel 频道
     * @return array
     */
    public static function getCategoriesByChannel($channel)
    {
        $channelId  = Channel::getIdByTitle($channel);
        $categories = self::leftJoin('rainlab_forum_channels_categories', 'id', '=', 'category_id', $type = 'inner')
            ->where('channel_id', $channelId)
            ->get();

        $options = array(0 => array('id' => 0, 'name' => 'Select Cotegory'));
        foreach($categories as $v)
        {
            $options[] = $v->toArray();
        }

        return $options;
    }
}
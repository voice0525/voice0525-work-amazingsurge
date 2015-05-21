<?php namespace Voice\Forum\Models;

use Mail;
use Model;

/**
 * Topic watching model
 */
class TopicActive extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_forum_topic_active_score_conf';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name', 'description'];
}
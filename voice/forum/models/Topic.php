<?php namespace Voice\Forum\Models;

use Event;
use Auth;
use App;
use Model;
use Db;
use ApplicationException;
use Voice\Forum\Models\Post as PostModel;

/**
 * Topic Model
 */
class Topic extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_forum_topics';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['subject'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'subject'         => 'required',
        'channel_id'      => 'required',
        'start_member_id' => 'required'
    ];

    /**
     * @var array The attributes that should be visible in arrays.
     */
    // protected $visible = ['id', 'slug', 'subject', 'channel', 'created_at', 'updated_at'];

    /**
     * @var array Date fields
     */
    public $dates = ['last_post_at'];

    /**
     * @var array Auto generated slug
     */
    protected $slugs = ['slug' => 'subject'];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'posts' => ['Voice\Forum\Models\Post'],
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [
        'first_post' => ['Voice\Forum\Models\Post', 'order' => 'created_at asc']
    ];

    public $belongsTo = [
        'channel'          => ['Voice\Forum\Models\Channel'],
        'start_member'     => ['Voice\Forum\Models\Member'],
        'last_post'        => ['Voice\Forum\Models\Post'],
        'last_post_member' => ['Voice\Forum\Models\Member'],
    ];

    public $belongsToMany = [
        'followers' => ['Voice\Forum\Models\Member', 'table' => 'rainlab_forum_topic_followers', 'timestamps' => true]
    ];

    /**
     * @var boolean Topic has new posts for member, set by TopicWatch model
     */
    public $hasNew = true;

    /**
     * Creates a topic and a post inside a channel
     * @param  Channel $channel
     * @param  Member $member
     * @param  array $data Topic and post data: subject, content.
     * @return self
     */
    public static function createInChannel($channel, $member, $data)
    {
        $topic = new static;
        $topic->subject = array_get($data, 'subject');
        $topic->channel = $channel;
        $topic->start_member = $member;
        $topic->category_id  = array_get($data, 'category');

        $post = new Post;
        $post->topic = $topic;
        $post->member = $member;
        $post->content = array_get($data, 'content');

        Db::transaction(function() use ($topic, $post) {
            $topic->save();
            $post->save();
        });

        TopicFollow::follow($topic, $member);
        $member->touchActivity();

        return $topic;
    }

    public function scopeForEmbed($query, $channel, $code)
    {
        return $query
            ->where('embed_code', $code)
            ->where('channel_id', $channel->id);
    }

    /**
     * Auto creates a topic based on embed code and channel
     * @param  string $code        Embed code
     * @param  string $channel     Channel to create the topic in
     * @param  string $subject     Title for the topic (if created)
     * @return self
     */
    public static function createForEmbed($code, $channel, $subject = null)
    {
        $topic = self::forEmbed($channel, $code)->first();

        if (!$topic) {
            $topic = new self;
            $topic->subject = $subject;
            $topic->embed_code = $code;
            $topic->channel = $channel;
            $topic->start_member_id = 0;
            $topic->save();
        }

        return $topic;
    }

    /**
     * Lists topics for the front end
     * @param  array $options Display options
     *                        - page      Page number
     *                        - perPage   Results per page
     *                        - sort      Sorting field
     *                        - channels  Topics in channels
     *                        - search    Search query
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 20,
            'sort'       => 'created_at',
            'channels'   => null,
            'search'     => ''
        ], $options));

        /*
         * Sorting
         */
        $allowedSortingOptions = ['created_at', 'updated_at', 'subject'];
        if (!in_array($sort, $allowedSortingOptions))
            $sort = $allowedSortingOptions[0];

        $query->orderBy('is_sticky', 'desc');
        $query->orderBy($sort, in_array($sort, ['created_at', 'updated_at']) ? 'desc' : 'asc');

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->where(function($query) use ($search) {
                $query->whereHas('posts', function($query) use ($search){
                    $query->searchWhere($search, ['subject', 'content']);
                });

                $query->orSearchWhere($search, 'subject');
            });
        }

        /*
         * Channels
         */
        if ($channels !== null) {
            if (!is_array($channels))
                $channels = [$channels];

            $query->whereIn('channel_id', $channels);
        }

        return $query->paginate($perPage, $page);
    }

    public function moveToChannel($channel)
    {
        $oldChannel = $this->channel;
        $this->timestamps = false;
        $this->channel = $channel;
        $this->save();
        $this->timestamps = true;
        $oldChannel->rebuildStats()->save();
        $channel->rebuildStats()->save();
    }

    public function increaseViewCount()
    {
        $this->timestamps = false;
        $this->increment('count_views');
        $this->timestamps = true;
    }

    public function afterCreate()
    {
        $this->start_member()->increment('count_topics');
        $this->channel()->increment('count_topics');
    }

    public function afterDelete()
    {
        $this->start_member()->decrement('count_topics');
        $this->channel()->decrement('count_topics');
        $this->channel()->decrement('count_posts', $this->posts()->count());
        $this->posts()->delete();
        $this->followers()->detach();
        TopicWatch::where('topic_id', $this->id)->delete();
    }

    public function canPost($member = null)
    {
        if (!$member)
            $member = Member::getFromUser();

        if (!$member)
            return false;

        if ($member->is_banned)
            return false;

        if ($this->is_locked && !$member->is_moderator)
            return false;

        return true;
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function stickyTopic()
    {
        $this->is_sticky = ($this->is_sticky == 1 ? 0 : 1);
        $this->save();
    }

    public function lockTopic()
    {
        $this->is_locked = ($this->is_locked == 1 ? 0 : 1);
        $this->save();
    }

    /**
     * 根据状态获取主题列表
     */
    public static function getTopicsByState()
    {
        $statusConf  = ['Newest', 'Active', 'Unanswered', 'Resolved'];
        $state       = ucfirst(post('state'));
        $channelId   = intval(post('channel'));
        if(!in_array($state, $statusConf)) return [];

        switch ($state) {
            case 'Newest':
                $topics = self::with('last_post_member')->listFrontEnd([
                    'page'     => 1,
                    'sort'     => 'updated_at',
                    'channels' => $channelId,
                ]);
                break;
            case 'Active':
                $topics = self::orderBy('active_score', 'desc')->get();
                break;
            case 'Unanswered':
                $topics = self::whereIn('id', PostModel::select('topic_id')->groupBy('topic_id')->havingRaw('count(id) = 1')->get()->toArray())->get();
                break;
            case 'Resolved':
                $topics = self::where('has_best', 1)->orderBy('updated_at', 'desc')->get();
                break;
            default:
                $topics = [];
                break;
        }

        if(!empty($topics)) return $topics;
        return [];
    }

    public function beforeSave()
    {
        // 兼容调整，避免更新时涉及到is_author字段
        if(isset($this->is_author)) unset($this->is_author);
    }

    public function afterSave()
    {
        // 设定初始活跃值
        if($this->created_at == $this->updated_at) {
            Event::fire('voice.forum.topic.active', [$this->id, 'create_topic']);
        }
        // exit;
    }

    public function afterFetch() 
    {
        // 检测当前登录用户是否是楼主
        static $user;
        $this->is_author = 0;
        if($user == false) {
            $user = Auth::getUser();

            if(!$user) $user = null;
        }
        if(!$user) return;

        if($this->start_member_id == $user->id) $this->is_author = 1;
        return;
    }

    /**
     * 设置帖子活跃度
     *
     * @param  integer $topicId 主题ID
     * @param  string  $action  操作名称
     * @return boolean
     */
    public static function setActiveScore($topicId, $action)
    {
        $action = trim($action);
        if(!$action) return false;

        $conf = new Model();
        $conf->setTable('rainlab_forum_topic_active_score_conf');
        $ret  = $conf->where('title', $action)->get()->toArray();
        if(!$ret) return;

        $score = $ret[0]['score'];
        $topic = new Model();
        $topic->setTable('rainlab_forum_topics');
        $data  = $topic->where('id', $topicId)->get()->toArray()[0];
        $f = fopen('event_score.txt', 'a');
        fwrite($f, "{$topicId} -> {$action}: {$data['active_score']} + {$score}\r\n");
        $topic->where('id', $topicId)->update(['active_score' => $data['active_score'] + $score]);
    }
}
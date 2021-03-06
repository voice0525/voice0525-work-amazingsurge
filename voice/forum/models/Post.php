<?php namespace Voice\Forum\Models;

use Event;
use Auth;
use Model;
use Carbon\Carbon;
use Markdown;
use October\Rain\Html\Helper as HtmlHelper;

/**
 * Post Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_forum_posts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['subject', 'content'];

    /**
     * @var array The attributes that should be visible in arrays.
     */
    // protected $visible = ['subject', 'content', 'member', 'topic'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'topic_id' => 'required',
        'member_id' => 'required',
        'content' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'topic' => ['Voice\Forum\Models\Topic'],
        'member' => ['Voice\Forum\Models\Member'],
    ];

    // public $hasMany = [
    //     'post_like_log' => [''],
    // ]

    /**
     * Creates a postinside a topic
     * @param  Topic $topic
     * @param  Member $member
     * @param  array $data Post data: subject, content.
     * @return self
     */
    public static function createInTopic($topic, $member, $data)
    {
        $post = new static;
        $post->topic = $topic;
        $post->member = $member;
        $post->subject = array_get($data, 'subject', $topic->subject);
        $post->content = array_get($data, 'content');
        $post->save();

        TopicFollow::follow($topic, $member);
        $member->touchActivity();
        return $post;
    }

    /**
     * Lists topics for the front end
     * @param  array $options Display options
     *                        - page      Page number
     *                        - perPage   Results per page
     *                        - sort      Sorting field
     *                        - topic     Posts in topic (id)
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
            'perPage'    => 30,
            'sort'       => 'created_at',
            'topic'      => null,
            'search'     => ''
        ], $options));

        /*
         * Sorting
         */
        $allowedSortingOptions = ['created_at', 'updated_at'];
        if (!in_array($sort, $allowedSortingOptions))
            $sort = $allowedSortingOptions[0];

        $query->orderBy($sort, 'asc');

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, ['subject', 'content']);
        }

        /*
         * Topic
         */
        if ($topic !== null) {
            $query->where('topic_id', $topic);
        }

        return $query->paginate($perPage, $page);
    }

    public function canEdit($member = null)
    {
        if ($member === null)
            $member = Member::getFromUser();

        if (!$member)
            return false;

        if ($member->is_moderator)
            return true;

        return $this->member_id == $member->id;
    }

    //
    // Events
    //

    public function beforeSave()
    {
        $this->content_html = HtmlHelper::clean(Markdown::parse(trim($this->content)));
    }

    public function afterCreate()
    {
        $this->member()->increment('count_posts');

        $this->topic->count_posts++;
        $this->topic->last_post_at = new Carbon;
        $this->topic->last_post = $this;
        $this->topic->last_post_member = $this->member;
        $this->topic->save();
        $this->topic->channel()->increment('count_posts');
    }

    public function afterDelete()
    {
        $this->member()->decrement('count_posts');
        $this->topic()->decrement('count_posts');
        $this->topic->channel()->decrement('count_posts');

        // If the topic has no more posts, delete it
        if ($this->topic->count_posts <= 0)
            $this->topic->delete();
    }

    /**
     * 点赞
     */
    public static function likePost()
    {
        $user = Auth::getUser();
        if(!$user) return false;

        $userId = $user->id;
        $postId = intval(post('post_id'));

        $model = new Model();
        $model->setTable('rainlab_forum_posts_like_logs');
        $model->user_id = $userId;
        $model->post_id = $postId;
        $model->save();

        $post = self::where('id', $postId)->get();
        $post[0]->increment('like');
        $like = $post[0]->like;

        // 设置事件
        Event::fire('voice.forum.topic.active', [$post[0]->topic_id, 'like']);

        return ['action' => 'like', 'id' => $postId, 'like' => $like];
    }

    /**
     * 取消点赞
     */
    public static function unLikePost()
    {
        $user = Auth::getUser();
        if(!$user) return false;

        $userId = $user->id;
        $postId = intval(post('post_id'));

        $model = new Model();
        $model->setTable('rainlab_forum_posts_like_logs');
        $model->user_id = $userId;
        $model->post_id = $postId;
        $obj = $model->get()[0];
        $obj->setTable('rainlab_forum_posts_like_logs');
        $obj->delete();

        $post = self::where('id', $postId)->get();
        $post[0]->decrement('like');
        $like = $post[0]->like;

        return ['action' => 'unlike', 'id' => $postId, 'like' => $like];
    }

    /**
     * 获取post是否被当前用户点赞的信息
     */
    public function afterFetch()
    {
        static $user;
        $this->user_like = 0;
        if($user == false) {
            $user = Auth::getUser();

            if(!$user) $user = null;
        }
        if(!$user) return;

        $userId = $user->id;
        $postId = $this->id;

        $model = new Model();
        $model->setTable('rainlab_forum_posts_like_logs');
        $obj = $model->where('user_id', $userId)->where('post_id', $postId)->get();
        if($obj->toArray()) $this->user_like = 1;
        return;
    }

    /**
     * 设置最佳答案
     */
    public static function setBest()
    {
        $postId = intval(post('post_id'));
        $model  = self::where('id', $postId);
        $post   = $model->get()->toArray()[0];

        // 更新post信息
        $model->update(['is_best' => 1]);
        $model = self::where('id', $postId);
        // 更新topic信息
        $model = new Model();
        $model->setTable('rainlab_forum_topics');
        $model->where('id', $post['topic_id'])->update(['has_best' => 1]);

        // 设置事件
        Event::fire('voice.forum.topic.active', [$post['topic_id'], 'set_best']);
        return;
    }
}
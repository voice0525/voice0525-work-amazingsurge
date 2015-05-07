<?php namespace Voice\Forum\Components;

use Auth;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Voice\Forum\Models\TopicWatch;
use Voice\Forum\Models\ChannelWatch;
use Voice\Forum\Models\Topic as TopicModel;
use Voice\Forum\Models\Channel as ChannelModel;
use Voice\Forum\Models\Member as MemberModel;

/**
 * Channel component
 * 
 * Displays a list of posts belonging to a channel.
 */
class Channel extends ComponentBase
{
    /**
     * @var boolean Determine if this component is being used by the EmbedChannel component.
     */
    public $embedMode = false;

    /**
     * @var string If this channel is embedded, pass the topic slug to this route parameter for linking to topics.
     */
    public $embedTopicParam = 'topicSlug';

    /**
     * @var Voice\Forum\Models\Member Member cache
     */
    protected $member = null;

    /**
     * @var Voice\Forum\Models\Channel Channel cache
     */
    protected $channel = null;

    /**
     * @var string Reference to the page name for linking to members.
     */
    public $memberPage;

    /**
     * @var string Reference to the page name for linking to topics.
     */
    public $topicPage;

    /**
     * @var Collection Topics cache for Twig access.
     */
    public $topics = null;

    public function componentDetails()
    {
        return [
            'name'           => 'voice.forum::lang.channel.component_name',
            'description'    => 'voice.forum::lang.channel.component_description',
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'voice.forum::lang.slug.name',
                'description' => 'voice.forum::lang.slug.desc',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'memberPage' => [
                'title'       => 'voice.forum::lang.member.page_name',
                'description' => 'voice.forum::lang.member.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
            'topicPage' => [
                'title'       => 'voice.forum::lang.topic.page_name',
                'description' => 'voice.forum::lang.topic.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
            'category' => [
                'title'       => 'Category',
                'description' => 'Category',
                'default'     => '{{ :category }}',
                'type'        => 'string',
            ],
            'status' => [
                'title'       => 'Status',
                'description' => 'Status',
                'default'     => '{{ :status }}',
                'type'        => 'string',
            ],
        ];
    }

    public function getPropertyOptions($property)
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->addCss('assets/css/forum.css');

        $this->prepareVars();
        $this->page['channel'] = $this->getChannel();
        return $this->prepareTopicList();
    }

    protected function prepareVars()
    {
        /*
         * Page links
         */
        $this->topicPage = $this->page['topicPage'] = $this->property('topicPage');
        $this->memberPage = $this->page['memberPage'] = $this->property('memberPage');
    }

    public function getChannel()
    {
        if ($this->channel !== null)
            return $this->channel;

        if (!$slug = $this->property('slug'))
            return null;

        
        echo "category: ".$this->property('category').'<br/>';
        echo "status: ".$this->property('status').'<br/>';

        return $this->channel = ChannelModel::whereSlug($slug)->first();
    }

    protected function prepareTopicList()
    {
        /*
         * If channel exists, load the topics
         */
        if ($channel = $this->getChannel()) {

            $currentPage = input('page');
            $searchString = trim(input('search'));
            $topics = TopicModel::with('last_post_member')->listFrontEnd([
                'page'     => $currentPage,
                'sort'     => 'updated_at',
                'channels' => $channel->id,
                'search'   => $searchString,
            ]);

            /*
             * Add a "url" helper attribute for linking to each topic
             */
            $topics->each(function($topic) {
                if ($this->embedMode)
                    $topic->url = $this->pageUrl($this->topicPage, [$this->embedTopicParam => $topic->slug]);
                else
                    $topic->setUrl($this->topicPage, $this->controller);

                if ($topic->last_post_member)
                    $topic->last_post_member->setUrl($this->memberPage, $this->controller);
            });

            /*
             * Signed in member
             */
            $this->page['member'] = $this->member = MemberModel::getFromUser();
            if ($this->member) {
                $this->member->setUrl($this->memberPage, $this->controller);
                $topics = TopicWatch::setFlagsOnTopics($topics, $this->member);
                ChannelWatch::flagAsWatched($channel, $this->member);
            }

            $this->page['topics'] = $this->topics = $topics;

            /*
             * Pagination
             */
            if ($topics) {
                $queryArr = [];
                if ($searchString) $queryArr['search'] = $searchString;
                $queryArr['page'] = '';
                $paginationUrl = Request::url() . '?' . http_build_query($queryArr);

                if ($currentPage > ($lastPage = $topics->lastPage()) && $currentPage > 1)
                    return Redirect::to($paginationUrl . $lastPage);

                $this->page['paginationUrl'] = $paginationUrl;
            }
        }


        $this->page['isGuest'] = !Auth::check();
    }

}
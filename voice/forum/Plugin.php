<?php namespace Voice\Forum;

use Event;
use Backend;
use RainLab\User\Models\User;
use Voice\Forum\Models\Member;
use System\Classes\PluginBase;
use RainLab\User\Controllers\Users as UsersController;
use Voice\Forum\Models\Topic as TopicModel;

/**
 * Forum Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'voice.forum::lang.plugin.name',
            'description' => 'voice.forum::lang.plugin.description',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-comments',
            'homepage'    => 'https://github.com/rainlab/forum-plugin'
        ];
    }

    public function boot()
    {
        User::extend(function($model) {
            $model->hasOne['forum_member'] = ['Voice\Forum\Models\Member'];
        });

        UsersController::extendFormFields(function($widget, $model, $context) {
            if ($context != 'update') return;
            if (!Member::getFromUser($model)) return;

            $widget->addFields([
                'forum_member[username]' => [
                    'label'   => 'voice.forum::lang.settings.username',
                    'tab'     => 'Forum',
                    'comment' => 'voice.forum::lang.settings.username_comment'
                ],
                'forum_member[is_moderator]' => [
                    'label'   => 'voice.forum::lang.settings.moderator',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'voice.forum::lang.settings.moderator_comment'
                ],
                'forum_member[is_banned]' => [
                    'label'   => 'voice.forum::lang.settings.banned',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'voice.forum::lang.settings.banned_comment'
                ],
            ], 'primary');
        });

        UsersController::extendListColumns(function($widget, $model) {
            if (!$model instanceof \RainLab\User\Models\User) return;

            // 列表页的字段
            $widget->addColumns([
                'forum_member_username' => [
                    'label'      => 'voice.forum::lang.settings.forum_username',
                    'relation'   => 'forum_member',
                    'select'     => 'username',
                    'searchable' => false
                ]
            ]);
        });

        // 回帖的事件声明
        Event::listen('voice.forum.topic.post', function($obj, $post, $postUrl){
            // 增加活跃度
            Event::fire('voice.forum.topic.active', [$post->topic_id, 'post']);
        });
        // 声明帖子活跃度相关的事件
        Event::listen('voice.forum.topic.active', function($topicId, $action){
            TopicModel::setActiveScore($topicId, $action);
        });
    }

    public function registerComponents()
    {
        return [
           '\Voice\Forum\Components\Channels'     => 'forumChannels',
           '\Voice\Forum\Components\Channel'      => 'forumChannel',
           '\Voice\Forum\Components\Topic'        => 'forumTopic',
           '\Voice\Forum\Components\Topics'       => 'forumTopics',
           '\Voice\Forum\Components\Member'       => 'forumMember',
           '\Voice\Forum\Components\EmbedTopic'   => 'forumEmbedTopic',
           '\Voice\Forum\Components\EmbedChannel' => 'forumEmbedChannel'
        ];
    }

    /**
     * 设置后台侧边栏
     *
     * @return array
     */
    // public function registerSettings()
    // {
    //     return [
    //         'settings' => [
    //             'label'       => 'voice.forum::lang.settings.channels',
    //             'description' => 'voice.forum::lang.settings.channels_desc',
    //             'icon'        => 'icon-comments',
    //             'url'         => Backend::url('rainlab/forum/channels'),
    //             'category'    => 'Forum',
    //             'order'       => 500
    //         ]
    //     ];
    // }

    public function registerMailTemplates()
    {
        return [
            'voice.forum::mail.topic_reply' => 'Notification to followers when a post is made to a topic.',
            'voice.forum::mail.member_report' => 'Notification to moderators when a member is reported to be a spammer.'
        ];
    }

    /** 
     * 注册权限
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'voice.forum.channels'  => ['tab' => 'Forum', 'label' => 'Manage Channels'],
            'voice.forum.categories'  => ['tab' => 'Forum', 'label' => 'Manage Categories'],
        ];
    }

    /**
     * 注册后台导航栏
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'forum' => [
                'label'       => 'voice.forum::lang.plugin.name',
                'url'         => Backend::url('voice/forum/channels'),
                'icon'        => 'icon-comments',
                'permissions' => ['voice.forum.*'],
                'order'       => 500,

                'sideMenu' => [
                    'channels' => [
                        'label'       => 'voice.forum::lang.backendmenu.channels',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('voice/forum/channels'),
                        'permissions' => ['voice.forum.channels']
                    ],
                    'categories' => [
                        'label'       => 'voice.forum::lang.backendmenu.categories',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('voice/forum/categories'),
                        'permissions' => ['voice.forum.categories']
                    ],
                    'topicactive' => [
                        'label'       => 'voice.forum::lang.backendmenu.topicactive',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('voice/forum/topicactive'),
                        'permissions' => ['voice.forum.topicactive']
                    ]
                ]
            ]
        ];
    }
}

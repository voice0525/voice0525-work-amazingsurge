<?php

return [
    'topics' => [
        'component_name' => 'Topic List',
        'component_description' => 'Displays a list of all topics.',
        'per_page' => 'Topics per page',
        'per_page_validation' => 'Invalid format of the topics per page value'
    ],
    'topic' => [
        'page_name' => 'Topic Page',
        'page_help' => 'Page name to use for clicking on a conversation topic.'
    ],
    'member' => [
        'page_name' => 'Member Page',
        'page_help' => 'Page name to use for clicking on a Member.'
    ],
    'channel' => [
        'component_name' => 'Channel',
        'component_description' => 'Displays a list of posts belonging to a channel.',
        'page_name' => 'Channel Page',
        'page_help' => 'Page name to use for clicking on a Channel.'
    ],
    'channels' => [
        'new_channel' => 'New Channel',
        'sure' => 'Are you sure?',
        'delete' => 'Delete',
        'manage' => 'Manage Channel Order',
        'return' => 'Return to Channels',
        'name' => 'Channels',
        'create' => 'Create Channels',
        'update' => 'Edit Channels',
        'preview' => 'Preview Channels',
        'manage' => 'Manage Channels',
        'creating' => 'Creating Channel...',
        'create' => 'Create',
        'createnclose' => 'Create and Close',
        'cancel' => 'Cancel',
        'or' => 'or',
        'returnlist' => 'Return to channels list',
        'saving' => 'Saving Channel...',
        'save' => 'Save',
        'savenclose' => 'Save and Close',
        'deleting' => 'Deleting Channel...',
        'really' => 'Do you really want to delete this channel?',
        'list_name' => 'Channel List',
        'list_desc' => 'Displays a list of all visible channels.'
    ],
    'slug' => [
        'name' => 'Slug param name',
        'desc' => 'The URL route parameter used for looking up the channel by its slug. A hard coded slug can also be used.'
    ],
    'frontend' => [
        'notopic' => 'There are no topics in this channel.'
    ],

    'plugin' => [
        'name' => 'Forum',
        'description' => 'A simple embeddable forum'
    ],
    'data' => [
        'title' => 'Title',
        'desc' => 'Description',
        'slug' => 'Slug',
        'parent' => 'Parent',
        'noparent' => '-- No parent --',
        'moderated' => 'Moderated',
        'is_mod' => 'Only moderators can post to this channel.',
        'hidden' => 'Hidden',
        'is_hidden' => 'Hide this channel from the main channel list.'
    ],
    'settings' => [
        'username' => 'Username',
        'username_comment' => 'The display to represent this user on the forum.',
        'moderator' => 'Forum moderator',
        'moderator_comment' => 'Place a tick in this box if this user can moderate the entire forum.',
        'banned' => 'Banned from forum',
        'banned_comment' => 'Place a tick in this box if this user is banned from posting to the forum.',
        'forum_username' => 'Forum Username',
        'channels' => 'Forum channels',
        'channels_desc' => 'Manage available forum channels.'
    ],
    'embedch' => [
        'channel_name' => 'Embed Channel',
        'channel_self_desc' => 'Attach a channel to any page.',
        'channel_title' => 'Parent Channel',
        'channel_desc' => 'Specify the channel to create the new channel in',
        'embed_title' => 'Embed code param',
        'embed_desc' => 'A unique code for the generated channel. A routing parameter can also be used.',
        'topic_name' => 'Topic code param',
        'topic_desc' => 'The URL route parameter used for looking up a topic by its slug.'
    ],
    'embedtopic' => [
        'topic_name' => 'Embed Topic',
        'topic_self_desc' => 'Attach a topic to any page.',
        'target_name' => 'Target Channel',
        'target_desc' => 'Specify the channel to create the new topic or channel in',
        'embed_title' => 'Embed Code',
        'embed_desc' => 'A unique code for the generated topic or channel. A routing parameter can also be used.'
    ],
    'memberpage' => [
        'name' => 'Member',
        'self_desc' => 'Displays form member information and activity.',
        'slug_name' => 'Slug param name',
        'slug_desc' => 'The URL route parameter used for looking up the forum member by their slug. A hard coded slug can also be used.',
        'view_title' => 'View mode',
        'view_desc' => 'Manually set the view mode for the member component.',
        'ch_title' => 'Channel page',
        'ch_desc' => 'Page name to use for clicking on a channel.',
        'topic_title' => 'Topic page',
        'topic_desc' => 'Page name to use for clicking on a conversation topic.'
    ],
    'topicpage' => [
        'name' => 'Topic',
        'self' => 'Displays a topic and posts.',
        'slug_name' => 'Slug param name',
        'slug_desc' => 'The URL route parameter used for looking up the topic by its slug. A hard coded slug can also be used.',
        'channel_title' => 'Channel Page',
        'channel_desc' => 'Page name to use for clicking on a channel.'
    ],
    /**
     * 扩展内容...
     */
    'backendmenu' => [
        'channels' => 'Channels',
        'categories' => 'Categories',
        'topicactive' => 'Active Score Configuration'
    ],
    'data-categories' => [
        'title' => 'Category',
        'parent' => 'Parent',
        'noparent' => '-- No parent --',
        'slug'   => 'Slug',
        'description' => 'Description',
        'create_at' => 'Create At',
        'desc' => 'Description of Category',
        'moderated' => 'Moderated',
        'hidden' => 'Hidden',
        'is_mod' => 'Only moderators can post to this category.',
        'is_hidden' => 'Hide this category from the main category list.'
    ],
    'categories' => [
        'new_category' => 'New Category',
        'sure' => 'Are you sure?',
        'delete' => 'Delete',
        'manage' => 'Manage Categories',
        'return' => 'Return to Category',
        'name' => 'Categories',
        'create' => 'Create Category',
        'update' => 'Edit Category',
        'preview' => 'Preview Category',
        'creating' => 'Creating Category...',
        'create' => 'Create',
        'createnclose' => 'Create and Close',
        'cancel' => 'Cancel',
        'or' => 'or',
        'returnlist' => 'Return to Categories list',
        'saving' => 'Saving Category...',
        'save' => 'Save',
        'savenclose' => 'Save and Close',
        'deleting' => 'Deleting Category...',
        'really' => 'Do you really want to delete this category?',
        'list_name' => 'Category List',
        'list_desc' => 'Displays a list of all visible Categories.'
    ],
    'topicactive' => [
        'manage' => 'Topic Active Config Manage',
        'name' => 'Topic Active Config',
        'create' => 'Create',
        'update' => 'Update',
        'createnclose' => 'Create and Close',
        'creating' => 'Creating Config...',
        'or' => 'or',
        'cancel' => 'Cancel',
        'new_config' => 'New Config',
        'sure' => 'Sure to delete this config?',
        'delete' => 'Delete',
        'saving' => 'Saving Config...',
        'save' => 'Save',
        'savenclose' => 'Save and Close',
    ]
];
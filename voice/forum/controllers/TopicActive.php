<?php namespace Voice\Forum\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Voice\Forum\Models\TopicActive as TopicActiveModel;
use System\Classes\SettingsManager;

/**
 * Channels Back-end Controller
 */
class TopicActive extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['voice.forum.topicactive'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Voice.Forum', 'forum', 'topicactive');
        SettingsManager::setContext('Voice.Forum', 'settings');
    }

    public function index_onDelete()
    {
        if (($topicIds = post('checked')) && is_array($topicIds) && count($topicIds)) {

            foreach ($topicIds as $topicId) {
                if (!$topic = Category::find($topicId))
                    continue;

                $topic->delete();
            }

            Flash::success('Successfully deleted those topic.');
        }

        return $this->listRefresh();
    }

}
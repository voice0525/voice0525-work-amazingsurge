<?php namespace Voice\Forum\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Voice\Forum\Models\Category;
use System\Classes\SettingsManager;

/**
 * Channels Back-end Controller
 */
class Categories extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['voice.forum.category'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Voice.Forum', 'forum', 'category');
        SettingsManager::setContext('Voice.Forum', 'settings');
    }

    // public function index()
    // {
    //     $this->asExtension('ListController')->index();
    // }

    public function index_onDelete()
    {
        if (($cateIds = post('checked')) && is_array($cateIds) && count($cateIds)) {

            foreach ($cateIds as $cateId) {
                if (!$category = Category::find($cateId))
                    continue;

                $category->delete();
            }

            Flash::success('Successfully deleted those categories.');
        }

        return $this->listRefresh();
    }

    public function reorder()
    {
        $this->pageTitle = 'Reorder Channels';

        $toolbarConfig = $this->makeConfig();
        $toolbarConfig->buttons = '~/plugins/voice/forum/controllers/channels/_reorder_toolbar.htm';

        $this->vars['toolbar'] = $this->makeWidget('Backend\Widgets\Toolbar', $toolbarConfig);
        $this->vars['records'] = Channel::make()->getEagerRoot();
    }

    public function reorder_onMove()
    {
        $sourceNode = Channel::find(post('sourceNode'));
        $targetNode = post('targetNode') ? Channel::find(post('targetNode')) : null;

        if ($sourceNode == $targetNode)
            return;

        switch (post('position')) {
            case 'before': $sourceNode->moveBefore($targetNode); break;
            case 'after': $sourceNode->moveAfter($targetNode); break;
            case 'child': $sourceNode->makeChildOf($targetNode); break;
            default: $sourceNode->makeRoot(); break;
        }

        // $this->vars['records'] = Channel::make()->getEagerRoot();
        // return ['#reorderRecords' => $this->makePartial('reorder_records')];
    }
}
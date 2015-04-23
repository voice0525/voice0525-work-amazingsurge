<?php namespace RainLab\User\Controllers;

use Lang;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use RainLab\User\Models\Group as GroupModel;
use RainLab\User\Models\Settings as UserSettings;

class Groups extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['rainlab.users.access_groups'];

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'groups');
        SettingsManager::setContext('RainLab.User', 'settings');
    }

    /**
     * Deleted checked users.
     */
    public function onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $userId) {
                if (!$group = GroupModel::find($userId)) continue;
                $group->delete();
            }

            Flash::success(Lang::get('rainlab.user::lang.groups.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('rainlab.user::lang.groups.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}

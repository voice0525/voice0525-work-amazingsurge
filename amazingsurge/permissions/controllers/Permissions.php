<?php namespace Amazingsurge\Permissions\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Amazingsurge\Permissions\Models\Permission;

/**
 * Permissions Back-end Controller
 */
class Permissions extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Amazingsurge.Permissions', 'permissions', 'permissions');
    }

    public function onDelete()
    {
        if (($permissionIds = post('checked')) && is_array($permissionIds) && count($permissionIds)) {

            foreach ($permissionIds as $permissionId) {
                if (!$permission = Permission::find($permissionId))
                    continue;

                $permission->delete();
            }

            Flash::success('Successfully deleted those permissions.');
        }

        return $this->listRefresh();
    }
}
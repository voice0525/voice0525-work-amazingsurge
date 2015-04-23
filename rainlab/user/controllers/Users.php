<?php namespace RainLab\User\Controllers;

use Lang;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use RainLab\User\Models\User;
use RainLab\User\Models\Settings as UserSettings;
use Amazingsurge\Permissions\Models\Permission;

class Users extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['rainlab.users.access_users'];

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');
        SettingsManager::setContext('RainLab.User', 'settings');
    }

    /**
     * Manually activate a user
     */
    public function update_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->attemptActivation($model->activation_code);

        Flash::success(Lang::get('rainlab.user::lang.users.activated_success'));

        if ($redirect = $this->makeRedirect('update', $model)) {
            return $redirect;
        }
    }

    /**
     * Display username field if settings permit
     */
    protected function formExtendFields($form)
    {
        $collection = Permission::all();
        $datas      = $collection->toArray();
        $modules    = [];
        foreach($datas as $data)
        {
            $permissionFields["permission[{$data['module']}][{$data['name']}]"] = [
                'label' => $data['name'],
                'comment' => $data['description'],
                'type' => 'balloon-selector',
                'options' => [
                    1 => 'backend::lang.user.allow',
                    0 => 'backend::lang.user.inherit',
                    -1 => 'backend::lang.user.deny',
                ],
                'attributes' => [
                    'data-trigger-action' => 'disable',
                    'data-trigger' => "input[name='User[permissions][superuser]']",
                    'data-trigger-condition' => 'checked',
                ],
                'span' => 'auto',
                'tab' => 'Permission ' . $data['module']
            ];

            // $modules[$data['module']][] = [
            //     'name'   => "permission[{$data['module']}][{$data['name']}]",
            //     'config' => [
            //         'label' => $data['name'],
            //         'comment' => $data['description'],
            //         'type' => 'balloon-selector',
            //         'options' => [
            //             1 => 'backend::lang.user.allow',
            //             0 => 'backend::lang.user.inherit',
            //             -1 => 'backend::lang.user.deny',
            //         ],
            //         'attributes' => [
            //             'data-trigger-action' => 'disable',
            //             'data-trigger' => "input[name='User[permissions][superuser]']",
            //             'data-trigger-condition' => 'checked',
            //         ],
            //         'span' => 'auto',
            //         'tab' => 'Permission'
            //     ]
            // ];
        }
        unset($datas);

        // foreach($modules as $key => $module)
        // {
        //     foreach($module as $permission)
        //     {
        //         $permissionFields[$permission['name']] = $permission['config'];
        //     }
        // }

        
        $form->addTabFields($permissionFields);
        // echo '<pre>';
        // print_r($permissionFields);
        exit;
        $loginAttribute = UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
        if ($loginAttribute != UserSettings::LOGIN_USERNAME) {
            return;
        }

        if (array_key_exists('username', $form->getFields())) {
            $form->getField('username')->hidden = false;
        }


    }

    /**
     * Deleted checked users.
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $userId) {
                if (!$user = User::find($userId)) continue;
                $user->delete();
            }

            Flash::success(Lang::get('rainlab.user::lang.users.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('rainlab.user::lang.users.delete_selected_empty'));
        }

        return $this->listRefresh();
    }
}

<?php namespace Amazingsurge\Permissions;

use Flash;
use Backend;
use System\Classes\PluginBase;

/**
 * Permissions Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Permissions',
            'description' => 'No description provided yet...',
            'author'      => 'Amazingsurge',
            'icon'        => 'icon-leaf'
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
            'amazingsurge.permissions.permissions'  => ['tab' => 'Permissions', 'label' => 'Manage Permissions'],
            'amazingsurge.permissions.gourp_permission'  => ['tab' => 'Gourp Permission', 'label' => 'Manage Gourp Permission'],
            'amazingsurge.permissions.user_permission'  => ['tab' => 'User Permission', 'label' => 'Manage User Permission'],
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
            'permissions' => [
                'label'       => 'Permissions',
                'url'         => Backend::url('amazingsurge/permissions/permissions'),
                'icon'        => 'icon-lock',
                'permissions' => ['amazingsurge.permissions.*'],
                'order'       => 500,

                // 侧边栏
                'sideMenu' => [
                    'permissions' => [
                        'label'       => 'Permissions Defined',
                        'icon'        => 'icon-lock',
                        'url'         => Backend::url('amazingsurge/permissions/permissions'),
                        'permissions' => ['amazingsurge.permissions.permissions']
                    ],
                    'group' => [
                        'label'       => 'Group Permissions',
                        'icon'        => 'icon-lock',
                        'url'         => Backend::url('amazingsurge/permissions/group'),
                        'permissions' => ['amazingsurge.permissions.group']
                    ],
                    'user' => [
                        'label'       => 'User Permissions',
                        'icon'        => 'icon-lock',
                        'url'         => Backend::url('amazingsurge/permissions/user'),
                        'permissions' => ['amazingsurge.permissions.user']
                    ],
                ]


            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'Amazingsurge\Permissions\Components\Session' => 'sessionss',
        ];
    }
}

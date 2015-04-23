<?php namespace Amazingsurge\Products;

use Backend;
use System\Classes\PluginBase;

/**
 * Products Plugin Information File
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
            'name'        => 'Products',
            'description' => 'Manage Products',
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
            'amazingsurge.products.products'  => ['tab' => 'Products', 'label' => 'Manage Products'],
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
            'products' => [
                'label'       => 'Products',
                'url'         => Backend::url('amazingsurge/products/products'),
                'icon'        => 'icon-comments',
                'permissions' => ['amazingsurge.products.*'],
                'order'       => 500,

                // 侧边栏
                'sideMenu' => [
                    'products' => [
                        'label'       => 'Products',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('amazingsurge/products/products'),
                        'permissions' => ['amazingsurge.products.products']
                    ],
                ]
            ]
        ];
    }
}

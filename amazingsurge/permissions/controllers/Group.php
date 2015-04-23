<?php namespace Amazingsurge\Permissions\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Group Back-end Controller
 */
class Group extends Controller
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

        BackendMenu::setContext('Amazingsurge.Permissions', 'permissions', 'group');
    }
}
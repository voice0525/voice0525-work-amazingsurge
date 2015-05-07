<?php namespace Amazingsurge\Permissions\Components;

use Auth;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use ValidationException;

class Permission extends ComponentBase
{
    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    public function componentDetails()
    {
        return [
            'name'        => 'Permission',
            'description' => 'Permission Components'
        ];
    }

    // public function defineProperties()
    // {
    //     return [
    //         'aaa' => [
    //             'title'       => 'aaa',
    //             'description' => 'aaa',
    //             'type'        => 'dropdown',
    //             'default'     => 'all',
    //             'options'     => [
    //                 'all'   => 'rainlab.user::lang.session.all',
    //                 'user'  => 'rainlab.user::lang.session.users',
    //                 'guest' => 'rainlab.user::lang.session.guests'
    //             ]
    //         ],
    //         'bbb' => [
    //             'title'       => 'bbb',
    //             'description' => 'bbb',
    //             'type'        => 'dropdown',
    //             'default'     => ''
    //         ]
    //     ];
    // }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {

    }

    /**
     * Log out the user
     *
     * Usage:
     *   <a data-request="onLogout">Sign out</a>
     *
     * With the optional redirect parameter:
     *   <a data-request="onLogout" data-request-data="redirect: '/good-bye'">Sign out</a>
     *
     */
    public function onLogout()
    {
        Auth::logout();
        $url = post('redirect', Request::fullUrl());
        return Redirect::to($url);
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }
}
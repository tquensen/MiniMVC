<?php
//add css files (default media type is "screen")
$MiniMVC_view['css']['APP.reset'] = array('file' => 'reset.css');
$MiniMVC_view['css']['APP.layout'] = array('file' => 'layout.css');
$MiniMVC_view['css']['APP.typo'] = array('file' => 'typo.css');
//$MiniMVC_view['css']['APP.print'] = array('file' => 'layout.css', 'media' => 'print');

//unset the modules default css files
//unset($MiniMVC_view['css']['my.screen'])

//define navigation menu(s)
$MiniMVC_view['navi']['main'] = array(
    array(
        'title' => 'Home',
        'route' => $this->get('config/defaultRoute')
    ),
    array(
        'title' => array('naviAbout'), //set an array as title for i18n array(i18nString) or array(i18nString, Module)
        'route' => 'my.about',
        'submenu' => array(
            array('route' => 'my.about.subpage1'), //submenuitems without title won't be shown, but
            array('route' => 'my.about.subpage2')  //the parent menu item will be marked as active if one of this routes is active
        )
    ),
    array( //real world example
        'title' => array('NaviTitle', 'News'),
        'route' => 'news.defaultIndex',
        'submenu' => array( //"invisible" submenu items to make this menu item also active on single/edit/create pages
            array('route' => 'news.defaultShow'),
            array('route' => 'news.defaultCreate'),
            array('route' => 'news.defaultEdit')
        )
    ),
    array(
        'title' => 'Interesting links', //route or url is not required
        'submenu' => array(
            array(
                'title' => 'Google.com',
                'url' => 'http://www.google.com'
            ),
            array(
                'title' => 'Example #2',
                'url' => 'http://www.example.com'
            )
        )
    )
);

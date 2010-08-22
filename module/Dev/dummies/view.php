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
        'title' => array('navi.about'), //set an array as title for i18n array(i18nString) or array(i18nString, Module)
        'route' => 'my.about'
    ),
    array(
        'title' => 'Example Menu', //route or url is not required
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

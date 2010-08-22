<?php
$MiniMVC_view['css']['my.default'] = array('file' => 'default.css', 'module' => 'My');
$MiniMVC_view['css']['frontend.default'] = array('file' => 'default.css');

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
            ),
            array(
                'title' => 'Baum default',
                'route' => 'baum'
            ),
            array(
                'title' => 'Baum foo=bar',
                'route' => 'baum',
                'parameter' => array('foo' => 'bar')
            ),
            array(
                'title' => 'Baum foo=baZ',
                'route' => 'baum',
                'parameter' => array('foo' => 'baZ')
            )
        )
    )
);
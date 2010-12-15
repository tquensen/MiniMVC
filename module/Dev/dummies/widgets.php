<?php
$MiniMVC_widgets['MODLC.exampleWidget'] = array(
    'controller' => 'MODULE_Default',
    'action' => 'widget',
    'parameter' => array(),
    'rights' => false,  // use false for no restrictions,
                        // a right as string (e.g. 'user') th require that right,
                        // an array of rights to require ALL of them (AND)
                        //   e.g. array('user', 'publish') = user AND publish
                        // a dimensional array to require at least one right (OR)
                        //   e.g. array(array('user', 'guest')) = user OR guest
                        // a combination of both (each new level switches logic between AND and OR)
                        //   e.g. array('user', array('publish', 'administrate')) = user AND (publish OR administrate) = (user AND publish) OR (user AND administrate)
                        //        array(array('administrate', array('moderate', 'publish'))) = administrate OR (moderate AND publish)

    'layout' => 'all', //default to "all" (this should only be set in the app config)
    'format' => 'html', //defaults to "html" (this should only be set in the app config)
    'show' => 'home', //only show on these routes (string or array)
    'hide' => 'user.login', //hide widget on these routes (string or array)
);
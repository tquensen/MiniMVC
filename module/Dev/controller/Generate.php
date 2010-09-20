<?php

class Dev_Generate_Controller extends MiniMVC_Controller
{

    public function appAction($params)
    {
        if (!$params['app']) {
            return 'Keinen Appnamen angegeben!' . "\n";
        }

        if (file_exists(APPPATH . $params['app'])) {
            return 'App existiert bereits!' . "\n";
        }

        if (!is_writable(APPPATH)) {
            return 'Keine Schreibrechte im App-Ordner!' . "\n";
        }

        $path = APPPATH . $params['app'];
        $dummy = MODULEPATH . 'Dev/dummies';

        mkdir($path);
        mkdir($path . '/i18n');
        mkdir($path . '/settings');
        mkdir($path . '/view');
        mkdir($path . '/web');
        mkdir($path . '/web/js');
        mkdir($path . '/web/css');
        mkdir($path . '/web/images');
        mkdir($path . '/lib');

        file_put_contents($path . '/view/default.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_view.php')));
        file_put_contents($path . '/view/default.json.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_view.json.php')));
        file_put_contents($path . '/i18n/de.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/de_app.php')));
        file_put_contents($path . '/i18n/en.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/en_app.php')));
        //file_put_contents($path . '/settings/slots.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/slots_app.php')));
        file_put_contents($path . '/settings/view.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/view.php')));
        file_put_contents($path . '/settings/config.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/config_app.php')));
        file_put_contents($path . '/settings/routes.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/routes_app.php')));
        file_put_contents($path . '/settings/widgets.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/widgets_app.php')));

        if ($params['w']) {
            file_put_contents(WEBPATH . $params['app'] . '.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_web.php')));
            file_put_contents(WEBPATH . $params['app'] . '_dev.php', str_replace('APP', $params['app'], file_get_contents($dummy . '/app_web_dev.php')));
        }

        return 'App was generated successfully!'."\n"
              .'Make sure to define the $MiniMVC_apps[\''.$params['app'].'\'][\'baseurl\'] in your settings/apps.php and settings/apps_dev.php files!'."\n"
              .'Optionally, you can modify the web/.htaccess file to enable pretty URLs.';
    }

    public function moduleAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        if (file_exists(MODULEPATH . $params['module'])) {
            return 'Modul existiert bereits!' . "\n";
        }

        if (!is_writable(MODULEPATH)) {
            return 'Keine Schreibrechte im Module-Ordner!' . "\n";
        }

        $path = MODULEPATH . $params['module'];
        $dummy = MODULEPATH . 'Dev/dummies';

        mkdir($path);
        mkdir($path . '/controller');
        mkdir($path . '/model');
        //mkdir($path . '/Model/Schema');
        mkdir($path . '/settings');
        mkdir($path . '/i18n');
        mkdir($path . '/view');
        mkdir($path . '/view/default');
        mkdir($path . '/web');
        mkdir($path . '/web/js');
        mkdir($path . '/web/css');
        mkdir($path . '/web/images');
        mkdir($path . '/lib');
        //mkdir($path . '/Lib/Migrations');

        file_put_contents($path . '/controller/Default.php', str_replace(array('MODLC', 'MODULE', 'MODULELCFIRST'), array(strtolower($params['module']), $params['module'], strtolower(substr($params['module'], 0, 1)) . substr($params['module'], 1)), file_get_contents($dummy . '/Default.php')));
        file_put_contents($path . '/Installer.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/Installer.php')));
        file_put_contents($path . '/i18n/de.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/de.php')));
        file_put_contents($path . '/i18n/en.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/en.php')));
        file_put_contents($path . '/view/default/index.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.php')));
        file_put_contents($path . '/view/default/index.json.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.json.php')));
        file_put_contents($path . '/view/default/widget.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widget.php')));
        file_put_contents($path . '/view/default/create.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/create.php')));
        file_put_contents($path . '/view/default/show.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/show.php')));
        file_put_contents($path . '/view/default/edit.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/edit.php')));
        file_put_contents($path . '/settings/config.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/config.php')));
        file_put_contents($path . '/settings/routes.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/routes.php')));
        file_put_contents($path . '/settings/widgets.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widgets.php')));
        //file_put_contents($path . '/settings/slots.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/slots.php')));
        //file_put_contents($path . '/Model/Schema/schema.yml', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/schema.yml')));

        return 'Modul wurde erfolgreich generiert!';
    }

    public function modelAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['model']) {
            return 'Kein Modelnamen angegeben!' . "\n";
        }

        $model = ucfirst($params['model']);

        $search = array(
            '{name}',
            '{table}',
            '{module}',
            '{modlc}',
            '{namelcfirst}'
        );
        $replace = array(
            $model,
            strtolower(preg_replace('/(?!^)[[:upper:]]+/', '_$0', $model)),
            $params['module'],
            strtolower($params['module']),
            strtolower(substr($model, 0, 1)) . substr($model, 1)
        );

        $path = MODULEPATH . $params['module'].'/model';
        $dummy = MODULEPATH . 'Dev/dummies';

        $message = 'Erstelle Models...'."\n";
        if (!file_exists($path . '/'.$model.'.php')) {
            file_put_contents($path . '/'.$model.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Model.php')));
            $message .= '-> Datei '.$model.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'.php existiert bereits'."\n";
        }
        if (!file_exists($path . '/'.$model.'Table.php')) {
            file_put_contents($path . '/'.$model.'Table.php', str_replace($search, $replace, file_get_contents($dummy . '/Table.php')));
            $message .= '-> Datei '.$model.'Table.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$model.'Table.php existiert bereits'."\n";
        }
        return $message;
    }

    public function controllerAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['controller']) {
            return 'Kein Controllername angegeben!' . "\n";
        }

        $params['module'] = ucfirst($params['module']);
        $controller = ucfirst($params['controller']);

        $search = array(
            'MODLC',
            'MODULE',
            'MODULELCFIRST',
            'CONTROLLERLC',
            'CONTROLLER'
        );
        $replace = array(
            strtolower($params['module']),
            $params['module'],
            strtolower(substr($params['module'], 0, 1)) . substr($params['module'], 1),
            strtolower($controller),
            $controller
        );

        $path = MODULEPATH . $params['module'].'/controller';
        $dummy = MODULEPATH . 'Dev/dummies';

        $message = 'Erstelle Controller...'."\n";
        if (!file_exists($path . '/'.$controller.'.php')) {
            mkdir($path . '/view/'.strtolower($controller));
            file_put_contents($path . '/'.$controller.'.php', str_replace($search, $replace, file_get_contents($dummy . '/Controller.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/index.php', str_replace($search, $replace, file_get_contents($dummy . '/index.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/index.json.php', str_replace($search, $replace, file_get_contents($dummy . '/index.json.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/widget.php', str_replace($search, $replace, file_get_contents($dummy . '/widget.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/create.php', str_replace($search, $replace, file_get_contents($dummy . '/create.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/show.php', str_replace($search, $replace, file_get_contents($dummy . '/show.php')));
            file_put_contents($path . '/view/'.strtolower($controller).'/edit.php', str_replace($search, $replace, file_get_contents($dummy . '/edit.php')));

            $message .= '-> Datei '.$controller.'.php erstellt'."\n";
        } else {
            $message .= '-> Datei '.$controller.'.php existiert bereits'."\n";
        }
        return $message;
    }


}
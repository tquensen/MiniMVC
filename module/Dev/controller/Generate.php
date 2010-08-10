<?php

class Dev_Generate_Controller extends MiniMVC_Controller
{

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

        file_put_contents($path . '/controller/Default.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/Default.php')));
        file_put_contents($path . '/Installer.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/Installer.php')));
        file_put_contents($path . '/i18n/de.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/de.php')));
        file_put_contents($path . '/i18n/en.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/en.php')));
        file_put_contents($path . '/view/default/index.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.php')));
        file_put_contents($path . '/view/default/index.json.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.json.php')));
        file_put_contents($path . '/view/default/widget.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widget.php')));
        file_put_contents($path . '/settings/config.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/config.php')));
        file_put_contents($path . '/settings/routes.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/routes.php')));
        file_put_contents($path . '/settings/widgets.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widgets.php')));
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
            '{table}'
        );
        $replace = array(
            $model,
            strtolower(preg_replace('/(?!^)[[:upper:]]+/', '_$0', $model))
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


}
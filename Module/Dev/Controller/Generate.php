<?php

class Dev_Generate_Controller extends MiniMVC_Controller
{

    public function moduleAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        if (file_exists(BASEPATH . 'Module/' . $params['module'])) {
            return 'Modul existiert bereits!' . "\n";
        }

        if (!is_writable(BASEPATH . 'Module/')) {
            return 'Keine Schreibrechte im Module-Ordner!' . "\n";
        }

        $path = BASEPATH . 'Module/' . $params['module'];
        $dummy = BASEPATH . 'Module/Dev/Dummies';

        mkdir($path);
        mkdir($path . '/Controller');
        mkdir($path . '/Model');
        //mkdir($path . '/Model/Schema');
        mkdir($path . '/Settings');
        mkdir($path . '/I18n');
        mkdir($path . '/View');
        mkdir($path . '/View/default');
        mkdir($path . '/Web');
        mkdir($path . '/Web/js');
        mkdir($path . '/Web/css');
        mkdir($path . '/Web/images');
        mkdir($path . '/Lib');
        //mkdir($path . '/Lib/Migrations');

        file_put_contents($path . '/Controller/Default.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/Default.php')));
        file_put_contents($path . '/Installer.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/Installer.php')));
        file_put_contents($path . '/I18n/de.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/de.php')));
        file_put_contents($path . '/I18n/en.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/en.php')));
        file_put_contents($path . '/View/default/index.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.php')));
        file_put_contents($path . '/View/default/index.json.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/index.json.php')));
        file_put_contents($path . '/View/default/widget.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widget.php')));
        file_put_contents($path . '/Settings/config.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/config.php')));
        file_put_contents($path . '/Settings/routes.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/routes.php')));
        file_put_contents($path . '/Settings/widgets.php', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/widgets.php')));
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

        $path = BASEPATH . 'Module/' . $params['module'].'/Model';
        $dummy = BASEPATH . 'Module/Dev/Dummies';

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
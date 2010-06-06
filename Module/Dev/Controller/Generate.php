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
        mkdir($path . '/Model/Schema');
        mkdir($path . '/Settings');
        mkdir($path . '/I18n');
        mkdir($path . '/View');
        mkdir($path . '/View/default');
        mkdir($path . '/Web');
        mkdir($path . '/Web/js');
        mkdir($path . '/Web/css');
        mkdir($path . '/Web/images');
        mkdir($path . '/Lib');
        mkdir($path . '/Lib/Migrations');

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
        file_put_contents($path . '/Model/Schema/schema.yml', str_replace(array('MODLC', 'MODULE'), array(strtolower($params['module']), $params['module']), file_get_contents($dummy . '/schema.yml')));

        return 'Modul wurde erfolgreich generiert!';
    }

    public function modelAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        $options = array(
            'baseClassName' => 'MiniMVC_Model',
            'generateBaseClasses' => true,
            'generateTableClasses' => true,
            'classPrefix' => '',
            'classPrefixFiles' => false,
            'baseClassPrefix' => 'Base_',
            'baseClassesDirectory' => 'Base'
        );

        try {
            Doctrine_Core::generateModelsFromYaml(BASEPATH . 'Module/' . $params['module'] . '/Model/Schema', BASEPATH . 'Module/' . $params['module'] . '/Model', $options);
        } catch (Exception $e) {
            return 'Fehler: ' . $e->getMessage() . "\n";
        }

        foreach (scandir(BASEPATH . 'Module/' . $params['module'] . '/Model/Base') as $file) {
            echo substr($file, 0, 5) . '|' . substr($file, 5, -4) . '|' . substr($file, -4) . '<br />';
            if (is_file(BASEPATH . 'Module/' . $params['module'] . '/Model/Base/' . $file) && substr($file, 0, 5) == 'Base_' && substr($file, -4) == '.php') {
                $modelName = substr($file, 5, -4);
                if (is_file(BASEPATH . 'Module/' . $params['module'] . '/Model/' . $modelName . 'Form.php')) {
                    continue;
                }
                $formFile = BASEPATH . 'Module/' . $params['module'] . '/Model/' . $modelName . 'Form.php';
                file_put_contents($formFile, $this->getFormDummy($modelName . 'Form'));
            }
        }

        return 'Models erfolgreich generiert!' . "\n";
    }

    public function migrationAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }
        if (!$params['from']) {
           $params['from'] = 'diff';
        }

        if ($params['from'] == 'diff') {
            try {
                Doctrine_Core::generateMigrationsFromDiff(BASEPATH . 'Module/' . $params['module'] . '/Lib/Migrations', BASEPATH . 'Module/' . $params['module'] . '/Model', BASEPATH . 'Module/' . $params['module'] . '/Model/Schema/schema.yml');

            } catch (Exception $e) {
                return 'Fehler: ' . $e->getMessage() . "\n";
            }
        } elseif ($params['from'] == 'models') {
            try {
                Doctrine_Core::generateMigrationsFromModels(BASEPATH . 'Module/' . $params['module'] . '/Lib/Migrations', BASEPATH . 'Module/' . $params['module'] . '/Model');

            } catch (Exception $e) {
                return 'Fehler: ' . $e->getMessage() . "\n";
            }
        }
    }

    protected function getFormDummy($name)
    {
        $content = '<?php
class ' . $name . ' extends MiniMVC_Form
{
    public function __construct($record = false, $options = array())
    {
        parent::__construct($record, $options);
        $this->setName("' . $name . '");

        //add your elements here

        $this->setValues();
    }
}
';
        return $content;
    }

}
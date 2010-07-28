<?php

class MiniMVC_Doctrine2
{
    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     *
     */
	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
	}
    
    public function init()
    {
        if (!isset($this->registry->settings->db['doctrine2']['connection']))
        {
            throw new Exception('No database commection options found!');
        }
        $cacheClass = (isset($this->registry->settings->db['doctrine2']['cacheClass'])) ? $this->registry->settings->db['doctrine2']['cacheClass'] : '\Doctrine\Common\Cache\ArrayCache';
        $proxy = (isset($this->registry->settings->db['doctrine2']['cacheClass'])) ? $this->registry->settings->db['doctrine2']['cacheClass'] : false;
        $connectionOptions = $this->registry->settings->db['doctrine2']['connection'];

        $cache = new $cacheClass;

        $config = new Doctrine\ORM\Configuration;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $entityPaths = $this->getEntityPaths('Model/Schema');
        
        $driverImpl = new Doctrine\ORM\Mapping\Driver\YamlDriver($entityPaths);
        $config->setMetadataDriverImpl($driverImpl);

        $config->setProxyDir(BASEPATH . '/DoctrineProxies');
        $config->setProxyNamespace('MiniMVC\DoctrineProxies');

        $config->setAutoGenerateProxyClasses($proxy);

        $this->em = Doctrine\ORM\EntityManager::create($connectionOptions, $config);

        $this->registerModelPaths();
    }

    protected function registerModelPaths()
    {
        $config = MiniMVC_Registry::getInstance()->settings->config;

        if (isset($config['modelPathsLoaded']) && $config['modelPathsLoaded']) {
            return;
        }

        foreach (array_reverse(MiniMVC_Registry::getInstance()->settings->modules) as $module) {
            if (!in_array('Module/' . $module . '/Model', $config['autoloadPaths'])) {
                $config['autoloadPaths'][] = 'Module/' . $module . '/Model';
            }
        }
        $config['modelPathsLoaded'] = true;
        MiniMVC_Registry::getInstance()->settings->saveToCache('config', $config);
    }

    protected function getEntityPaths($path = '')
    {
        $paths = array();
        foreach ($this->registry->settings->modules as $module)
        {
            if (is_dir(BASEPATH . 'Module/'.$module.'/'.$path)) {
            $paths[] = BASEPATH . 'Module/'.$module.'/'.$path;
            }
        }
        return $paths;
    }

    public function __get($key) {
        if ($key == 'em') {
            return $this->em;
        }
        return null;
    }
}
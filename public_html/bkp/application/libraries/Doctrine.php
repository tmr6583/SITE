<?php
use Doctrine\ORM\EntityManager,
	Doctrine\ORM\Configuration;

define('DEBUGGING', FALSE);

class Doctrine {

	public $em = null;

	public function __construct()
	{
		// load database configuration and custom config from CodeIgniter
		require APPPATH . 'config/database.php';

		// Set up class loading.
		require_once APPPATH . 'libraries/Doctrine/Common/ClassLoader.php';

		$doctrineClassLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPPATH . 'libraries');
		$doctrineClassLoader->register();

		$entitiesClassLoader = new \Doctrine\Common\ClassLoader('models', rtrim(APPPATH, '/'));
		$entitiesClassLoader->register();

		$proxiesClassLoader = new \Doctrine\Common\ClassLoader('Proxies', APPPATH . 'models');
		$proxiesClassLoader->register();

		$symfonyClassLoader = new \Doctrine\Common\ClassLoader('Symfony', APPPATH . 'libraries/Doctrine');
		$symfonyClassLoader->register();

		// Choose caching method based on application mode
		if (ENVIRONMENT != 'production')
		{
			$cache = new \Doctrine\Common\Cache\ApcCache;
		}
		else
		{
			$cache = new \Doctrine\Common\Cache\ArrayCache;
		}

		// Set some configuration options
		$config = new Configuration;

		// Metadata driver
		$driverImpl = $config->newDefaultAnnotationDriver(APPPATH . 'models');	
		$config->setMetadataDriverImpl($driverImpl);

		// Caching
		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);

		// Proxies
		$config->setProxyDir(APPPATH . 'models/Proxies');
		$config->setProxyNamespace('Proxies');

		if (ENVIRONMENT == 'development') {
			$config->setAutoGenerateProxyClasses(TRUE);
		} else {
			$config->setAutoGenerateProxyClasses(FALSE);
		}

		// SQL query logger
		if (DEBUGGING)
		{
			$logger = new \Doctrine\DBAL\Logging\EchoSQLLogger;
			$config->setSQLLogger($logger);
		}

		if (defined('ENVIRONMENT') && ENVIRONMENT === 'development')
			$active_group = 'development';
		else
			$active_group = 'default';

		// Database connection information
		$connectionOptions = array(
			'driver'		=> 'pdo_mysql',
			'user'			=> $db[$active_group]['username'],
			'password'		=> $db[$active_group]['password'],
			'host'			=> $db[$active_group]['hostname'],
			'dbname'		=> $db[$active_group]['database'],
			'charset'		=> $db[$active_group]['char_set'],
			'driverOptions'	=> array( 
				'charset' 	=> $db[$active_group]['char_set'] 
			)
		);

		// Create EntityManager
		$this->em = EntityManager::create($connectionOptions, $config);
	}
}
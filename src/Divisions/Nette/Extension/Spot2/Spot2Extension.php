<?php

namespace Divisions\Nette\Extension\Spot2;

use Nette;

/**
 * Class Spot2Extension
 * @package Divisions\Nette\Extension\Spot2
 */
class Spot2Extension
	extends Nette\DI\CompilerExtension{


	/**
	 *
	 */
	public function loadConfiguration(){
		$config = $this->getConfig();

		if(empty($config['connections'])){
			return;
		}

		$container  = $this->getContainerBuilder();
		$spotConfig = $container->addDefinition($this->prefix('config'))->setClass('Spot\Config');

		foreach($config['connections'] AS $name => $values){
			$configuration = null;
			if(isset($values['debug']) AND $values['debug'] === true){
				$logger = $container->addDefinition($this->prefix($name.'.connection.logger'))//->setClass('Doctrine\DBAL\Logging\DebugStack');
				                    ->setClass('Divisions\Doctrine\DBAL\Logging\Tracy\Spot2Panel');

				$configuration = $container->addDefinition($this->prefix($name.'.connection.configuration'))
				                           ->setClass('Doctrine\DBAL\Configuration')
				                           ->addSetup('setSQLLogger', [$logger]);

			}

			$connection = $container->addDefinition($this->prefix($name.'.connection'))
			                        ->setClass('Doctrine\DBAL\Connection')
			                        ->setFactory('Doctrine\DBAL\DriverManager::getConnection',
			                                     [$values, $configuration]);

			$spotConfig->addSetup('addConnection', [$name, $connection]);
		}
	}

	/**
	 * @param Nette\PhpGenerator\ClassType $class
	 */
	public function afterCompile(Nette\PhpGenerator\ClassType $class){
		$container = $this->getContainerBuilder();

		foreach($container->findByType('Divisions\Doctrine\DBAL\Logging\Tracy\Spot2Panel') as $name => $val){
			$class->methods['initialize']->addBody('Tracy\Debugger::getBar()->addPanel($this->getService(?));',
			                                       [$name]);
		}
	}
}

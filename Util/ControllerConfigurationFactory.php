<?php

namespace Flexix\ControllerConfigurationBundle\Util;

use Flexix\ConfigurationBundle\Util\ConfigurationInterface;
use Flexix\MapperBundle\Util\EntityMapperInterface;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactoryInterface;


class ControllerConfigurationFactory implements ControllerConfigurationFactoryInterface {

    const BASE_CONFIG = 'base';
    const PATH = 'path';

    protected $configurations = [];
    protected $baseConfiguration;
    protected $configuration;
    protected $mapper;


    public function __construct(ConfigurationInterface $baseConfig,EntityMapperInterface $mapper) {

        $this->baseConfiguration = $baseConfig;
        $this->mapper=$mapper;
    }

    public function createConfiguration(ConfigurationInterface $controllerConfiguration, $action, $alias, $module = null, $id = null) {

        $this->configuration = $controllerConfiguration;
        $analyzeSection = $this->getAnalyzeSection($action, $alias,$module);
        $this->mergeToConfiguration($this->baseConfiguration, $action);
        $this->mergeConfigurations($action, $alias, $module);

        $this->configuration->merge($analyzeSection);
        $controllerConfiguration->setAction($action);


        return $controllerConfiguration;
    }

    protected function mergeToConfiguration($configuration, $action) {
        $baseSection = $this->getBaseSection($configuration);
        $actionSection = $this->getActionSection($configuration, $action);
        return $this->mergeSections($baseSection, $actionSection);
    }

    protected function mergeSections() {

        $sections = func_get_args();
        foreach ($sections as $section) {
            if ($section) {
                $this->configuration->merge($section);
            }
        }
        return $this->configuration;
    }

    protected function getBaseSection($configuration) {

        if ($configuration->has(self::BASE_CONFIG)) {
            return $configuration->get(self::BASE_CONFIG);
        }
    }

    protected function getActionSection($configuration, $action) {

        $actionAddress = sprintf('actions.%s', $action);
        if ($configuration->has($actionAddress)) {
            return $configuration->get($actionAddress);
        }
    }

    protected function getAnalyzeSection($action, $alias, $module = null) {
       
        $analyzeConfiguration = [];
        $analyzeConfiguration[self::PATH] = ['class'=>$this->mapper->getEntityClass($alias), 'action'=>$action, 'alias'=>$alias, 'module'=>$module];

        return $analyzeConfiguration;
    }

    protected function mergeConfigurations($action, $alias, $module = null) {

        $configuration = $this->findSpecializedConfiguration( $alias, $module);
        if ($configuration) {
            $this->mergeToConfiguration($configuration, $action);
        }
        return $this->configuration;
    }

    protected function findSpecializedConfiguration( $alias, $module = null) {
        
        if ($module) {
            if (array_key_exists($alias, $this->configurations) && array_key_exists($module, $this->configurations[$alias])) {
                return $this->configurations[$alias][$module];
            }
        } else {
            if ( array_key_exists($alias, $this->configurations)) {
                return $this->configurations[$alias];
            }
        }
    }

    public function addConfiguration(ConfigurationInterface $configuration, /*$action,*/ $alias, $module = null) {

        if ($module) {
            $this->configurations/*[$action]*/[$alias][$module] = $configuration;
        } else {
            $this->configurations/*[$action]*/[$alias] = $configuration;
        }
    }

}

<?php

namespace Flexix\ControllerConfigurationBundle\Util;

use Flexix\ConfigurationBundle\Util\ConfigurationInterface;
use Flexix\PathAnalyzerBundle\Util\PathAnalyzerInterface;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactoryInterface;

//@to do: change mergin model
class ControllerConfigurationFactory implements ControllerConfigurationFactoryInterface {

    const BASE_CONFIG = 'base';
    const PATH = 'path';

    protected $configurations = [];
    protected $baseConfiguration;
    protected $configuration;
    protected $pathAnalyzer;

    public function __construct(ConfigurationInterface $baseConfig, PathAnalyzerInterface $pathAnalyzer) {

        $this->baseConfiguration = $baseConfig;
        $this->pathAnalyzer = $pathAnalyzer;
    }

    public function createConfiguration(ConfigurationInterface $controllerConfiguration, $action, $module, $alias, $id = null) {

        $this->configuration = $controllerConfiguration;

        $analyze = $this->pathAnalyzer->analyze($module, $alias, $id);
        $analyzeSection = $this->getAnalyzeSection($analyze);

        $entityAlias = $analyze->getEntityAlias();

        $this->mergeToConfiguration($this->baseConfiguration, $action);
        $this->mergeConfigurations($module, $entityAlias, $action);

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

    protected function getAnalyzeSection($analyze) {

        $analyzeConfiguration = [];
        $analyzeConfiguration[self::PATH] = $analyze->dump();

        return $analyzeConfiguration;
    }

    protected function mergeConfigurations($applicationPath, $entityAlias, $action) {

        $configuration = $this->findSpecializedConfiguration($applicationPath, $entityAlias);
        if ($configuration) {
            $this->mergeToConfiguration($configuration, $action);
        }
        return $this->configuration;
    }

    protected function findSpecializedConfiguration($action, $alias, $module = null) {

        if ($module) {
            if (array_key_exists($action, $this->configurations) && array_key_exists($alias, $this->configurations[$action]) && array_key_exists($module, $this->configurations[$action][$alias])) {
                return $this->configuration[$action][$alias][$module];
            }
        } else {
            if (array_key_exists($action, $this->configurations) && array_key_exists($alias, $this->configurations[$action])) {
                return $this->configuration[$action][$alias];
            }
        }
    }

    public function addConfiguration(ConfigurationInterface $configuration, $action, $alias, $module = null) {

        if ($module) {
            $this->configurations[$action][$alias][$module] = $configuration;
        } else {
            $this->configurations[$action][$alias] = $configuration;
        }
    }

}

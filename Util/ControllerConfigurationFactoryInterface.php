<?php

namespace Flexix\ControllerConfigurationBundle\Util;

use Flexix\ConfigurationBundle\Util\ConfigurationInterface;
use Flexix\PathAnalyzerBundle\Util\PathAnalyzerInterface;

//@to do: change mergin model
interface ControllerConfigurationFactoryInterface {

    public function createConfiguration( ConfigurationInterface $controllerConfiguration, $action,$alias,$module=null,$id=null);
    
    public function addConfiguration(ConfigurationInterface $configuration, $alias, $module);

}

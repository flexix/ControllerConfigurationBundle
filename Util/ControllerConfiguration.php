<?php

//Flexix\PrototypeBundle\Util\ControllerConfiguration

namespace Flexix\ControllerConfigurationBundle\Util;


use Flexix\ConfigurationBundle\Util\Configuration;

class ControllerConfiguration extends Configuration {

    protected $action;

    function getAction() {
        return $this->action;
    }

    function setAction($action) {
        $this->action = $action;
    }

    

}

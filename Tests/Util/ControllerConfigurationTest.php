<?php

namespace Flexix\ControllerConfigurationBundle\Tests\Util;

use Flexix\PathAnalyzerBundle\Util\PathAnalyzer;
use Symfony\Component\Yaml\Yaml;
use Flexix\MapperBundle\Util\ApplicationMapper;
use Flexix\MapperBundle\Util\EntityMapper;
use \PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;

/**
 * Description of EntityAnalyzer
 * php  app/phpunit.phar   --bootstrap=./app/autoload.php ./src/Flexix/PrototypeBundle/Tests/Util/PathAnalyzerTest.php
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>
 */
class ControllerConfigurationTest extends TestCase {

    protected static $controllerConfiguration;
    
    public static function setupBeforeClass() {

        self::$controllerConfiguration = new ControllerConfiguration();
    }

    public function testGetAction() {

        self::$controllerConfiguration->setAction('aaaa');
        $this->assertEquals('aaaa', self::$controllerConfiguration->getAction());
    }

    public function testSetAction() {

        self::$controllerConfiguration->setAction('aaaa');
        $this->assertEquals('aaaa', self::$controllerConfiguration->getAction());
    }

}

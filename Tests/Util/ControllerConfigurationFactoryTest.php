<?php

namespace Flexix\ControllerConfigurationBundle\Tests\Util;

use Flexix\PathAnalyzerBundle\Util\PathAnalyzer;
use \PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Flexix\MapperBundle\Util\ApplicationMapper;
use Flexix\MapperBundle\Util\EntityMapper;
use Flexix\ConfigurationBundle\Util\Configuration;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration;
use Flexix\ControllerConfigurationBundle\Util\ControllerConfigurationFactory;

/**
 * Description of EntityAnalyzer
 * php  app/phpunit.phar   --bootstrap=./app/autoload.php ./src/Flexix/ControllerConfigurationBundle/Tests/Util/ControllerConfigurationFactoryTest.php
 * @author Mariusz Piela <mariuszpiela@tmsolution.pl>
 */
class ControllerConfigurationFactoryTest extends TestCase {

    const _ALIAS = 'payment-frequency';
    const _applicationPath = 'admin/some/other/path';
    const _entitiesPath = 'discount/2/measure-unit/3/payment-frequency';
    const _ID = '7';

    static protected $request;
    static protected $mapperConfiguration;
    static protected $prototypeConfiguration;
    static protected $developerConfiguration;

    public static function setupBeforeClass() {

        self::$request = new Request(['id' => self::_ID], [/* post */], [
            'applicationPath' => self::_applicationPath,
            'entitiesPath' => self::_entitiesPath
        ]);

        self::$mapperConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testMapper.yml');
        self::$prototypeConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testPrototypeConfiguration.yml');
        self::$developerConfiguration = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testDeveloperConfiguration.yml');
    }

    protected function createConfiguration() {
        
        $mapperConfiguration = Yaml::parse(self::$mapperConfiguration);

        $appliactionMapper = new ApplicationMapper($mapperConfiguration['flexix_mapper']['applications']);

        $entityMapper = new EntityMapper($mapperConfiguration['flexix_mapper']['entities']);

        $PathAnalyzer = new PathAnalyzer($appliactionMapper, $entityMapper);

        $prototypeConfiguration = Yaml::parse(self::$prototypeConfiguration);
        $developerConfiguration = Yaml::parse(self::$developerConfiguration);

        $baseConfiguration = new Configuration($prototypeConfiguration['flexix_prototype']);
        $configurationFactory = new ControllerConfigurationFactory($baseConfiguration, $PathAnalyzer);
        $developerConfiguration = new Configuration($developerConfiguration['flexix_prototype']);
        $configurationFactory->addConfiguration($developerConfiguration, self::_applicationPath, self::_ALIAS);
        return $configurationFactory->createConfiguration(new ControllerConfiguration(), 'new',self::_applicationPath,self::_entitiesPath,self::_ID);
    }

    public function testCreateConfiguration_instaceOfControllerConfiguration() {

        $this->assertInstanceOf('Flexix\ControllerConfigurationBundle\Util\ControllerConfiguration', $this->createConfiguration());
    }
    
    public function testCreateConfiguration_overridingAttributes() {
        
        $configuration=$this->createConfiguration();
        
        $this->assertEquals('Default\Container\SomeElephant.html.twig',$configuration->get('templates.container') );
    }
    

}

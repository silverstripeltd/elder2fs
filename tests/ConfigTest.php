<?php

namespace Elder;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    protected $configFile = null;

    public function setUp()
    {
        parent::setUp();
        $this->configFile = tempnam(sys_get_temp_dir(), 'elder2fsconfig');
    }

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->configFile)) {
            unlink($this->configFile);
        }
    }

    public function testConfigInstance()
    {
        $this->assertInstanceOf("Elder\\Config", new Config('fakefile'));
    }

    public function testConfigFileNotFound()
    {
        $config = new Config("non_existing_config.yml");
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage('file not found.');
        $config->load();
    }

    public function testConfigFileNotYaml()
    {
        file_put_contents($this->configFile, 'hey I am not proper yaml');
        $config = new Config($this->configFile);
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage(' file could not to be parsed as yaml');
        $config->load();
    }

    public function testNoElderURL()
    {
        $config = new Config($this->setConfig(['elderUrl' => '']));
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage('file has no "elderUrl" entry.');
        $config->load();
    }

    public function testNoPages()
    {
        $config = new Config($this->setConfig(['elderUrl' => 'http://test.com/private']));
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage('file has no "pages" entry.');
        $config->load();
    }

    public function testGetService()
    {
        $url = 'http://test.com/private';
        $config = new Config($this->setConfig(['elderUrl' => $url, 'pages' => ['manual' => []]]));
        $config->load();
        $this->assertEquals($url, $config->getService());
    }

    public function testGetPages()
    {
        $pages = [
            'manual' => ['kb' => 'kb000001.md', 'locale' => 'en_NZ'],
        ];
        $filePath = $this->setConfig([
            'elderUrl' => 'http://test.com/private',
            'pages' => $pages
        ]);
        $config = new Config($filePath);
        $config->load();
        $this->assertEquals($pages, $config->getPages());
    }

    public function testGetEmptyVariables()
    {
        $filePath = $this->setConfig([
            'elderUrl' => 'http://test.com/private',
            'pages' => ['manual' => []]
        ]);
        $config = new Config($filePath);
        $config->load();
        $this->assertEquals([], $config->getVariables());
    }

    public function testGetVariables()
    {
        $vars = ['platform' => ['companyName' => 'Terrible Ideas Ltd']];
        $filePath = $this->setConfig([
            'elderUrl' => 'http://test.com/private',
            'pages' => ['manual' => []],
            'variables' => $vars
        ]);
        $config = new Config($filePath);
        $config->load();
        $this->assertEquals($vars, $config->getVariables());
    }

    protected function getConfig(array $values) {
        return new Config($this->setConfig($values));
    }

    protected function setConfig(array $values)
    {
        $content = \Symfony\Component\Yaml\Yaml::dump($values);
        file_put_contents($this->configFile, $content);
        return $this->configFile;
    }
}

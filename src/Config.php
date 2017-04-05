<?php

namespace Elder2Fs;

use SebastianBergmann\CodeCoverage\RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Config
{

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $values;

    /**
     *
     * @param string $configFilePath
     */
    public function __construct($configFilePath)
    {
        $this->filePath = $configFilePath;
    }

    /**
     * load and validate configuration
     *
     * @throws RuntimeException
     */
    public function load()
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException($this->filePath . ' file not found.');
        }

        // Load and parse all configuration.
        $values = Yaml::parse(file_get_contents($this->filePath));
        if (!is_array($values)) {
            throw new \RuntimeException($this->filePath . ' file could not to be parsed as yaml');
        }
        $this->values = $values;

        if (empty($this->values['elderUrl'])) {
            throw new \RuntimeException($this->filePath . ' file has no "elderUrl" entry.');
        }
        if (empty($this->values['pages'])) {
            throw new \RuntimeException($this->filePath . ' file has no "pages" entry.');
        }
        if (empty($this->values['variables'])) {
            $this->values['variables'] = [];
        }
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->values['elderUrl'];
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->values['pages'];
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->values['variables'];
    }
}

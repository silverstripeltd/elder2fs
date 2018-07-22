<?php

namespace Elder2Fs;

use Exception;

class Page extends Node
{

    /**
     * @var string
     */
    public $kb;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var string
     */
    public $version;

    /**
     * @var array
     */
    public $meta;

    /**
     * @param array $data
     */
    public function load($data)
    {
        if (empty($data['url']) && empty($data['kb'])) {
            throw new Exception('Either "url" or "kb" key must be present on Page node');
        }
        $this->url = !empty($data['url']) ? $data['url'] : '';
        $this->kb = !empty($data['kb']) ? $data['kb'] : '';
        $this->locale = !empty($data['locale']) ? $data['locale'] : 'en_NZ';
        $this->version = !empty($data['version']) ? $data['version'] : 'master';
        $this->meta = !empty($data['meta']) ? $data['meta'] : [];
    }
}

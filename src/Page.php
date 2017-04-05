<?php

namespace Elder2Fs;

class Page extends Node
{

    /**
     * @var string
     */
    public $kb;

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
        $this->kb = $data['kb'];
        $this->locale = !empty($data['locale']) ? $data['locale'] : 'en_NZ';
        $this->version = !empty($data['version']) ? $data['version'] : 'master';
        $this->meta = !empty($data['meta']) ? $data['meta'] : [];
    }
}

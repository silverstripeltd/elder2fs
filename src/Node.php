<?php

namespace Elder2Fs;

/**
 * Node is a building block of a page tree built from an array.
 * This allows us to traverse the structure more easily, and work with nested paths.
 * This will allow us to generate the entire md structure more easily later.
 */
abstract class Node
{

    public $path;

    public $parent;

    public $children = [];

    public function __construct($path = '.', $parent = null)
    {
        $this->path = $path;
        $this->parent = $parent;
    }

    abstract public function load($data);

    /**
     * Descends into the page tree and executes $f on $class.
     *
     * @param string $class
     * @param callable $f
     */
    public function walk($class, callable $f)
    {
        if (get_class($this) === $class) {
            $f($this);
        }
        foreach ($this->children as $child) {
            $child->walk($class, $f);
        }
    }
}

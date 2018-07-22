<?php

namespace Elder2Fs;

class Dir extends Node
{

    public function load($data)
    {
        foreach ($data as $name => $child) {
            $path = sprintf('%s/%s', $this->path, $name);
            if (empty($child['kb']) && empty($child['url'])) {
                // Must be directory if it has no 'kb' entry.
                $node = new Dir($path, $this);
            } else {
                $node = new Page($path, $this);
            }
            $this->children[] = $node;
            $node->load($child);
        }
    }
}

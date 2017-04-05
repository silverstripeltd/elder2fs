<?php

use Elder2Fs\Dir;
use Elder2Fs\Page;

class NodeTest extends \PHPUnit_Framework_TestCase
{

    public function testNodeWalking()
    {
        $parent = new Dir();
        $parent->load([
            'subdir' => [
                'page' => [
                    'kb' => 'kb00001'
                ]
            ]
        ]);

        $count = 0;
        $parent->walk('Elder2Fs\Page', function ($page) use (&$count) {
            $count += 1;
        });
        $this->assertEquals(1, $count, 'expected to visit 1 "Page" node');

        $count = 0;
        $parent->walk('Elder2Fs\Dir', function ($page) use (&$count) {
            $count += 1;
        });
        $this->assertEquals(2, $count, 'expected to visit 2 "Dir" nodes');
    }
}

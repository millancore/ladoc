<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Styles;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Styles
 */
class StylesTest extends TestCase
{
    public function test_create_styles_from_array(): void
    {
        $styles = new Styles([
            'title' => 'bg-teal-500 px-1',
            'inline-code' => 'bg-gray-500',
        ]);

        $this->assertEquals('bg-teal-500 px-1', $styles->get('title'));
        $this->assertEquals('bg-gray-500', $styles->get('inline-code'));
    }

    public function test_it_can_get_all_styles(): void
    {
        $styles = new Styles([
            'title' => 'bg-teal-500 px-1',
            'inline-code' => 'bg-gray-500',
        ]);

        $this->assertEquals([
            'title' => 'bg-teal-500 px-1',
            'inline-code' => 'bg-gray-500',
        ], $styles->all());
    }
}

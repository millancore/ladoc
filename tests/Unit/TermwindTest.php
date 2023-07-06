<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Styles;
use Ladoc\Termwind;

/**
 * @covers \Ladoc\Termwind
 * @uses  \Ladoc\Styles
 */
class TermwindTest extends TestCase
{
    public function test_load_styles(): void
    {
        $termwind = new Termwind(new Styles([
            'title' => 'bg-teal-500 px-1',
            'inline-code' => 'bg-gray-500',
        ]));

        $this->assertTrue($termwind->loadStyles());
    }

}

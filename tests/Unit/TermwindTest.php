<?php

namespace Lo\Tests\Unit;

use Lo\Styles;
use Lo\Termwind;

/**
 * @covers \Lo\Termwind
 * @uses  \Lo\Styles
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

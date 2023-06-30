<?php

namespace Lo\Tests\Unit;

use Lo\Version;

class VersionTest extends TestCase
{
    public function test_it_can_return_latest_version()
    {
        $this->assertEquals('10.x', Version::getLatestVersion()->value);

    }

    public function test_it_can_return_version_from_value()
    {
        $this->assertEquals('10.x', Version::fromValue('10.x')->value);
        $this->assertEquals('10.x', Version::fromValue(10)->value);
        $this->assertEquals('10.x', Version::fromValue(10.0)->value);
        $this->assertEquals('6.x', Version::fromValue(6)->value);
        $this->assertEquals('5.2', Version::fromValue(5.2)->value);
    }

}
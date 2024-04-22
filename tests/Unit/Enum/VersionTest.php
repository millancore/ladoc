<?php

namespace Ladoc\Tests\Unit\Enum;

use Ladoc\Enum\Version;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Enum\Version
 */
class VersionTest extends TestCase
{
    public function test_it_can_return_latest_version(): void
    {
        $this->assertEquals('11.x', Version::getLatestVersion()->value);
    }

    public function test_it_can_return_version_from_value(): void
    {
        $this->assertEquals('10.x', Version::fromValue('10.x')->value);
        $this->assertEquals('10.x', Version::fromValue(10)->value);
        $this->assertEquals('10.x', Version::fromValue(10.0)->value);
        $this->assertEquals('6.x', Version::fromValue(6)->value);
        $this->assertEquals('5.2', Version::fromValue(5.2)->value);
    }

    public function test_error_try_get_invalid_version(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Version::fromValue('invalid');
    }

}

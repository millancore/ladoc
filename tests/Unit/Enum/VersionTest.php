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
        $this->assertEquals('13.x', Version::getLatestVersion()->value);
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

    public function test_error_lists_available_4x_versions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Version 4.x not found. Available 4.x versions: 4.2, 4.1, 4.0');

        Version::fromValue('4.x');
    }

    public function test_error_lists_available_5x_versions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Version 5 not found. Available 5.x versions: 5.8, 5.7, 5.6, 5.5, 5.4, 5.3, 5.2, 5.1, 5.0'
        );

        Version::fromValue(5);
    }

    public function test_it_can_return_versions_by_major(): void
    {
        $this->assertSame(['4.2', '4.1', '4.0'], Version::withMajorVersion(4));
        $this->assertSame([], Version::withMajorVersion(3));
    }

}

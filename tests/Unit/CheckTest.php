<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Check;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Check
 */
class CheckTest extends TestCase
{
    public function test_it_can_get_last_version_from_github_api(): void
    {
        $check = new Check();
        $version = $check->getLastVersion();

        $this->assertMatchesRegularExpression('/^v\d+\.\d+\.\d+$/', (string) $version);
    }

    public function test_it_can_compare_local_version_with_tag_version_from_github_api(): void
    {
        $check = new Check();
        $version = $check->isLastVersion('0.0.0');

        $this->assertFalse($version);
    }

}

<?php

namespace Ladoc\Tests\Unit\Process;

use Ladoc\Process\ProcessFactory;
use Ladoc\Process\ProcessInterface;
use Ladoc\Tests\TestCase;
use Symfony\Component\Process\Process;

/**
 * @covers \Ladoc\Process\ProcessFactory
 */
class ProcessFactoryTest extends TestCase
{
    public function test_it_can_create_a_new_process(): void
    {
        $processFactory = new ProcessFactory();
        $process = $processFactory->newProcess(['ls', '-la']);

        $this->assertInstanceOf(ProcessInterface::class, $process);
        $this->assertInstanceOf(Process::class, $process);

    }

}

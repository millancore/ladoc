<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Enum\Version;
use Ladoc\FileManager;
use Ladoc\Process\Process;
use Ladoc\Process\ProcessFactory;
use Ladoc\Repository;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Repository
 *
 * @uses \Ladoc\FileManager
 */
class RepositoryTest extends TestCase
{
    public function test_check_and_download_repository(): void
    {
        $fileManager = new FileManager(
            Version::V6,
            ROOT_TEST . '/data/.docs',
            ROOT_TEST . '/data/index'
        );

        $fileManager->removeDocDirectory();

        $processFactory = $this->createMock(ProcessFactory::class);

        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(1);

        $process->method('isSuccessful')->willReturn(true);

        $processFactory->method('newProcess')->willReturn($process);

        $repository = new Repository($fileManager, $processFactory);
        $repository->check();

        $this->assertDirectoryExists($fileManager->getDocPath());
    }


}

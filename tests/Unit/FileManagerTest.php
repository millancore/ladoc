<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Enum\Version;
use Ladoc\Exception\FileManagerException;
use Ladoc\FileManager;

/**
 * @covers \Ladoc\FileManager
 */
class FileManagerTest extends TestCase
{
    public function test_get_index_path(): void
    {
        $fileManager = $this->newFileManager();

        $this->assertEquals(ROOT_TEST.'/data/index/10.x', $fileManager->getIndexPath());
    }

    public function test_get_doc_path(): void
    {
        $fileManager = $this->newFileManager();

        $this->assertEquals(ROOT_TEST.'/data/.docs/10.x', $fileManager->getDocPath());
    }

    public function test_get_version(): void
    {
        $fileManager = $this->newFileManager();

        $this->assertEquals(Version::V10, $fileManager->getVersion());
    }

    public function test_get_file_content(): void
    {
        $fileManager = $this->newFileManager();

        $fileManager->saveIndexFile('section/test.html', 'test');

        $content = $fileManager->getFileContent('section/test.html');

        $this->assertIsString($content);
    }

    public function test_try_to_get_invalid_file_content(): void
    {
        $fileManager = $this->newFileManager();

        $this->expectException(FileManagerException::class);

        $fileManager->getFileContent('section/test.html');
    }

    public function test_get_files_from_repo_directory(): void
    {
        $fileManager = $this->newFileManager();

        $files = $fileManager->getRepositoryFiles();

        $this->assertIsArray($files);
        $this->assertCount(2, $files);
        $this->assertEquals([
            ROOT_TEST.'/data/.docs/10.x/artisan.md',
            ROOT_TEST.'/data/.docs/10.x/validation.md'
        ], array_values($files));
    }

    public function test_get_files_from_no_exist_directory(): void
    {
        $fileManager = new FileManager(
            Version::V10,
            'wrong/path',
            'wrong/path'
        );

        $this->expectException(FileManagerException::class);
        $this->expectExceptionMessage('Repository folder wrong/path/10.x not found');
        $fileManager->getRepositoryFiles();
    }

    public function test_it_can_save_file(): void
    {
        $fileManager = $this->newFileManager();

        $fileManager->saveIndexFile('section/test.html', 'test');

        $this->assertFileExists(ROOT_TEST . '/data/index/10.x/section/test.html');
        $this->assertEquals('test', file_get_contents(ROOT_TEST . '/data/index/10.x/section/test.html'));
    }

    public function test_it_can_remove_index_directory(): void
    {
        $fileManager = $this->newFileManager();

        $fileManager->removeIndexDirectory();
        $this->assertDirectoryDoesNotExist(ROOT_TEST . '/data/index/10.x');
    }

    public function test_it_try_two_remove_twice_index_directory(): void
    {
        $fileManager = $this->newFileManager();

        $fileManager->removeIndexDirectory();
        $fileManager->removeIndexDirectory();
        $this->assertDirectoryDoesNotExist(ROOT_TEST . '/data/index/10.x');
    }

    private function newFileManager(): FileManager
    {
        return new FileManager(
            Version::V10,
            ROOT_TEST . '/data/.docs',
            ROOT_TEST . '/data/index'
        );
    }


}

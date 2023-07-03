<?php

namespace Lo\Tests\Unit;

use Lo\Enum\Version;
use Lo\FileManager;
use Lo\Settings;

class FileManagerTest extends TestCase
{
    public function test_validate_if_branch_folder_exist()
    {
        $fileManager = new FileManager(Version::V10, $this->getSettings());

        $this->assertTrue($fileManager->versionDocumentFolderExist());
    }

    public function test_get_files_from_branch_folder()
    {
        $fileManager = new FileManager(Version::V10, $this->getSettings());

        $files = $fileManager->getVersionFiles();

        $this->assertIsArray($files);
        $this->assertCount(2, $files);
        $this->assertEquals([
            ROOT_TEST.'/data/.docs/10.x/artisan.md',
            ROOT_TEST.'/data/.docs/10.x/validation.md'
        ], array_values($files));
    }

    public function test_it_can_save_index_section()
    {
        $fileManager = new FileManager(Version::V10, $this->getSettings());

        $fileManager->saveSectionArticle(
            'validation',
            'test.html',
            'content'
        );

        $this->assertFileExists(ROOT_TEST . '/data/index/10.x/validation/test.html');
        $this->assertFalse(is_dir(ROOT_TEST . '/data/index/10.x/validation/test.html'));

        // remove dir
        rmdir(ROOT_TEST . '/data/index/10.x/');
    }


    private function getSettings(): Settings
    {
        return new Settings([
            'repository' => 'https://local',
            'doc_path' => ROOT_TEST . '/data/.docs',
            'index_path' => ROOT_TEST . '/data/index',
        ]);
    }

}
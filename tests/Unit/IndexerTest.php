<?php

namespace Lo\Tests\Unit;

use League\CommonMark\CommonMarkConverter;
use Lo\FileManager;
use Lo\Indexer;
use Lo\Settings;
use Lo\Version;

class IndexerTest extends TestCase
{
    public function test_it_can_create_index_for_version()
    {
        $indexer = new Indexer($this->getFileManager(), new CommonMarkConverter);

        $indexer->createIndexForVersion();

        $this->assertFileExists(ROOT_TEST . '/data/index/10.x/artisan/introduction.html');
        $this->assertFileExists(ROOT_TEST . '/data/index/10.x/artisan/laravel-sail.html');
        $this->assertFileExists(ROOT_TEST . '/data/index/10.x/validation/quick-defining-the-routes.html');

        // remove dir
        rmdir(ROOT_TEST . '/data/index/10.x/');
    }

    private function getFileManager()
    {
        return new FileManager(
            Version::V10,
            new Settings([
                'repository' => 'https://local',
                'doc_path' => ROOT_TEST . '/data/.docs',
                'index_path' => ROOT_TEST . '/data/index',
            ])
        );
    }

}
<?php

namespace Ladoc\Tests\Unit\Index;

use Ladoc\Enum\Version;
use Ladoc\FileManager;
use Ladoc\Index\IndexList;
use Ladoc\Index\IndexManager;
use Ladoc\Tests\Unit\TestCase;

/**
 * @covers \Ladoc\Index\IndexManager
 *
 * @uses \Ladoc\FileManager
 * @uses \Ladoc\Formatter\TermwindFormatter
 * @uses \Ladoc\Index\IndexList
 * @uses \Ladoc\Index\ItemList
 * @uses \Ladoc\Section
 * @uses \Ladoc\Splitter
 *
 */
class IndexManagerTest extends TestCase
{
    private IndexManager $indexManager;

    private FileManager $fileManager;

    protected function setUp(): void
    {
        $this->fileManager   = new FileManager(
            Version::V10,
            ROOT_TEST . '/data/.docs',
            ROOT_TEST . '/data/index'
        );

        $this->indexManager = new IndexManager($this->fileManager);
        parent::setUp();
    }

    public function test_it_can_create_index(): void
    {
        $this->indexManager->createIndex();
        $indexPath = $this->fileManager->getIndexPath();

        $this->assertFileExists($indexPath);
        $this->assertFileExists($indexPath . '/index.ladoc');
        $this->assertFileExists($indexPath . '/artisan/index.ladoc');
        $this->assertFileExists($indexPath . '/validation/index.ladoc');
    }


    public function test_it_check_if_index_directory_exist(): void
    {
        $this->fileManager->removeIndexDirectory();

        $this->assertFalse($this->indexManager->check());

        $this->indexManager->createIndex();
        $this->assertTrue($this->indexManager->check());
    }

    public function test_it_can_return_section_path(): void
    {
        $this->refreshIndex();

        $this->assertEquals(
            ROOT_TEST . '/data/index/10.x/artisan',
            $this->indexManager->getSectionPath('artisan')
        );
    }

    public function test_it_can_get_main_index(): void
    {
        $this->refreshIndex();

        $mainList = $this->indexManager->getMainIndex();

        $this->assertInstanceOf(IndexList::class, $mainList);
        $this->assertEquals('Main List', $mainList->getName());
        $this->assertEquals('Artisan Console', $mainList->get(0)->title);
        $this->assertEquals('Validation', $mainList->get(1)->title);

    }

    public function test_it_can_get_section_index(): void
    {
        $this->refreshIndex();

        $sectionList = $this->indexManager->getSectionIndex('artisan');

        $this->assertInstanceOf(IndexList::class, $sectionList);
        $this->assertEquals('Artisan Console', $sectionList->getName());
        $this->assertEquals('Introduction', $sectionList->get(0)->title);
        $this->assertEquals('Writing Commands', $sectionList->get(1)->title);
        ;
    }

    public function test_it_can_get_section_article(): void
    {
        $this->refreshIndex();

        $htmlArticleFile = $this->indexManager->getArticle('artisan', 'writing-commands');

        $this->assertStringContainsString('<p class="title">Writing Commands</p>', $htmlArticleFile);
    }



    private function refreshIndex(): void
    {
        $this->fileManager->removeIndexDirectory();
        $this->indexManager->createIndex();
    }


}

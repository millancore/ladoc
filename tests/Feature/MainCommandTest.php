<?php

namespace Lo\Tests\Feature;

use Lo\Enum\Version;
use Lo\FileManager;
use Lo\Tests\Unit\TestCase;
use Lo\Command\MainCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Lo\Command\MainCommand
 *
 * @uses \Lo\Enum\Version
 * @uses \Lo\FileManager
 * @uses \Lo\Index\IndexManager
 * @uses \Lo\InputResolver
 * @uses \Lo\Repository
 * @uses \Lo\Formatter\TermwindFormatter
 * @uses \Lo\Splitter
 * @uses \Lo\Section
 */
class MainCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new FileManager(
            Version::V10,
            ROOT_TEST.'/data/.docs',
            ROOT_TEST.'/data/index'
        ))->removeIndexDirectory();
    }

    /**
     * @uses \Lo\Action\ListAction
     * @uses \Lo\Index\IndexList
     * @uses \Lo\Index\ItemList
     * @uses \Lo\Index\Render
     */
    public function test_it_can_display_main_list(): void
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute([
            'section' => 'list',
            'query' => [],
            '--letter' => 'a',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Main List | filter: A', $output);
        $this->assertStringContainsString('[0] Artisan Console (artisan)', $output);

    }

    /**
     * @uses \Lo\Action\SectionQueryAction
     * @uses \Lo\Index\IndexList
     * @uses \Lo\Index\ItemList
     */
    public function test_it_can_display_search_article(): void
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute([
            'section' => 'artisan',
            'query' => ['repl'],
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Tinker (REPL)', $output);
    }

    private function getCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add(new MainCommand(
            ROOT_TEST.'/data',
            true
        ));

        $application->setAutoExit(false);

        return new CommandTester($application->find('search'));
    }

}

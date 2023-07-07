<?php

namespace Ladoc\Tests\Feature;

use Ladoc\Enum\Version;
use Ladoc\FileManager;
use Ladoc\Tests\Unit\TestCase;
use Ladoc\Command\MainCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Ladoc\Command\MainCommand
 *
 * @uses \Ladoc\Enum\Version
 * @uses \Ladoc\FileManager
 * @uses \Ladoc\Index\IndexManager
 * @uses \Ladoc\InputResolver
 * @uses \Ladoc\Repository
 * @uses \Ladoc\Formatter\TermwindFormatter
 * @uses \Ladoc\Splitter
 * @uses \Ladoc\Section
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
     * @uses \Ladoc\Action\ListAction
     * @uses \Ladoc\Index\IndexList
     * @uses \Ladoc\Index\ItemList
     * @uses \Ladoc\Index\Render
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
     * @uses \Ladoc\Action\SectionQueryAction
     * @uses \Ladoc\Index\IndexList
     * @uses \Ladoc\Index\ItemList
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
            'test-version',
            ROOT_TEST.'/data',
            true
        ));

        $application->setAutoExit(false);

        return new CommandTester($application->find('search'));
    }

}

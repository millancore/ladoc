<?php

namespace Ladoc\Tests\Feature;

use Ladoc\Command\MainCommand;
use Ladoc\Enum\Version;
use Ladoc\FileManager;
use Ladoc\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
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
 * @uses \Ladoc\Process\ProcessFactory
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
        $this->assertStringContainsString('Artisan Console (artisan)', $output);

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

    /**
     * @uses \Ladoc\Index\IndexList
     * @uses \Ladoc\Index\ItemList
     */
    public function test_error_on_unknown_section(): void
    {
        $commandTester = $this->getCommandTester();

        $statusCode = $commandTester->execute([
            'section' => 'artisann',
            'query' => ['repl'],
        ]);

        $this->assertSame(Command::FAILURE, $statusCode);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Section "artisann" not found.', $output);
        $this->assertStringContainsString('Did you mean: artisan?', $output);
        $this->assertStringContainsString('Run "ladoc" without arguments to list all sections.', $output);
    }

    /**
     * @uses \Ladoc\Action\SectionQueryAction
     * @uses \Ladoc\Index\IndexList
     * @uses \Ladoc\Index\ItemList
     */
    public function test_it_reports_empty_search_and_suggests_other_sections(): void
    {
        $commandTester = $this->getCommandTester();

        $statusCode = $commandTester->execute([
            'section' => 'artisan',
            'query' => ['hasMany'],
        ]);

        $this->assertSame(Command::SUCCESS, $statusCode);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('No results for "hasMany" in section "artisan".', $output);
        $this->assertStringContainsString('Sections with matches:', $output);
        $this->assertStringContainsString('Try: ladoc', $output);
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

        return new CommandTester($application->find('ladoc'));
    }

}

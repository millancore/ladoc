<?php

declare(strict_types=1);

namespace Ladoc\Command;

use League\CommonMark\Exception\CommonMarkException;
use Ladoc\Enum\Version;
use Ladoc\FileManager;
use Ladoc\Index\IndexManager;
use Ladoc\InputResolver;
use Ladoc\Repository;
use Ladoc\Styles;
use Ladoc\Termwind;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'search'
)]
class MainCommand extends Command
{
    public function __construct(
        private readonly string $rootPath,
        private readonly bool   $isTestMode = false
    ) {
        parent::__construct();
    }

    public function configure(): void
    {

        $this->addArgument(
            'section',
            InputArgument::OPTIONAL,
            'Section name',
            'list'
        );

        $this->addArgument(
            'query',
            InputArgument::IS_ARRAY,
            'Search string'
        );

        $this->addOption(
            'branch',
            'b',
            InputArgument::OPTIONAL,
            'Laravel version branch',
            Version::getLatestVersion()->value
        );

        $this->addOption(
            'letter',
            'l',
            InputArgument::OPTIONAL,
            'Filter Main list by letter',
            ''
        );
    }

    /**
     * @throws \Exception
     * @throws CommonMarkException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $section = $input->getArgument('section');
        $query = $input->getArgument('query');
        $versionInput = $input->getOption('branch');


        $version = Version::fromValue($versionInput);

        if ($version !== Version::getLatestVersion()) {
            $output->writeln(
                sprintf('<comment>Using old version: %s</comment>', $version->value)
            );
        }

        $fileManager = new FileManager(
            $version,
            $this->rootPath . '/.docs',
            $this->rootPath . '/index'
        );

        $indexManager = new IndexManager($fileManager);

        if (!$indexManager->check()) {

            $output->writeln(sprintf('Download v%s and Indexing...', $version->value));

            (new Repository($fileManager))->check();
            $indexManager->createIndex();
        }

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve($section, $query);

        $content = $action->execute(
            $query,
            ['letter' => $input->getOption('letter')]
        );

        if ($this->isTestMode) {
            $output->write($content);
            return Command::SUCCESS;
        }

        // @codeCoverageIgnoreStart
        (new Termwind(
            new Styles(require $this->rootPath . '/styles.php')
        ))->render($content);

        return Command::SUCCESS;
        // @codeCoverageIgnoreEnd
    }


}

<?php

declare(strict_types=1);

namespace Ladoc\Command;

use Ladoc\Process\ProcessFactory;
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
    name: 'ladoc'
)]
class MainCommand extends Command
{
    public function __construct(
        private readonly string $version,
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
            null
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

        try {
            $version = Version::fromValue($versionInput);
        } catch (\InvalidArgumentException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return Command::FAILURE;
        }

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

            (new Repository(
                $fileManager,
                new ProcessFactory(),
            ))->check();
            $indexManager->createIndex();
        }

        if (!is_numeric($section) && $section !== 'list' && !$indexManager->sectionExists($section)) {
            $output->writeln(sprintf('<error>Section "%s" not found.</error>', $section));

            $suggestions = $this->suggestSections($section, $indexManager);

            if (!empty($suggestions)) {
                $output->writeln(sprintf('Did you mean: %s?', implode(', ', $suggestions)));
            }

            $listCommand = $version === Version::getLatestVersion()
                ? 'ladoc'
                : sprintf('ladoc -b %s', $version->value);

            $output->writeln(sprintf('Run "%s" without arguments to list all sections.', $listCommand));

            return Command::FAILURE;
        }

        $inputResolver = new InputResolver($indexManager);
        $action = $inputResolver->resolve($section, $query, $input->getOptions());

        $content = $action->execute(
            $query,
            $input->getOptions()
        );

        if ($this->isTestMode) {
            $output->write($content);
            return Command::SUCCESS;
        }

        // @codeCoverageIgnoreStart
        $rendered = (new Termwind(
            new Styles(require $this->rootPath . '/styles.php')
        ))->renderToString($content);

        if (!$output->isDecorated()) {
            $rendered = (string) preg_replace(
                '/\e\[[0-9;?]*[ -\/]*[@-~]|\e\][^\e\a]*(?:\e\\\\|\a)/',
                '',
                $rendered
            );
        }

        $output->write($rendered, false, OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return array<string>
     */
    private function suggestSections(string $section, IndexManager $indexManager): array
    {
        $anchors = array_map(
            fn ($item) => $item->anchor,
            $indexManager->getMainIndex()->all()
        );

        $suggestions = array_filter(
            $anchors,
            fn ($anchor) => str_contains($anchor, $section) || levenshtein($anchor, $section) <= 3
        );

        sort($suggestions);

        return array_values($suggestions);
    }

}

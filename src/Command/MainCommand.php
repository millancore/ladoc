<?php

namespace Lo\Command;

use League\CommonMark\CommonMarkConverter;
use Lo\Enum\Version;
use Lo\FileManager;
use Lo\Formatter\TermwindFormatter;
use Lo\Indexer;
use Lo\Settings;
use Lo\Styles;
use Lo\Termwind;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'search'
)]
class MainCommand extends Command
{
    public function __construct(
        private readonly Settings $settings,
        private readonly Styles $styles
    ) {
        parent::__construct();
    }

    public function configure(): void
    {

        $this->addArgument('section', InputArgument::REQUIRED, 'Section name');

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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $section = $input->getArgument('section');
        $query = $input->getArgument('query');
        $version = $input->getOption('branch');

        $fileManager = new FileManager(
            Version::fromValue($version),
            $this->settings
        );

        //        if (!$fileManager->versionIndexFolderExist()) {
        $indexer = new Indexer(
            $fileManager,
            new TermwindFormatter(),
            new CommonMarkConverter()
        );

        $indexer->createIndexForVersion();


        dd($indexer->getMainIndex());

        //        }


        // search in content file using preg and Symfony process, return file name
        $process = new Process([
            'grep',
            '-rl',
            ROOT_APP.'/index/'.$version.'/'.$section,
            '-ie',
            implode(' ', $query)
        ]);
        $process->run();

        // process output as array
        $output = explode("\n", $process->getOutput());

        //remove empty values
        $output = array_filter($output);

        // array reverse
        $output = array_reverse($output);


        $content = '';
        foreach ($output as $file) {
            $content .= file_get_contents($file);
            $content .= '<hr>';
        }

        $content = trim($content, '<hr>');

        (new Termwind($this->styles))->render($content);

        return  Command::SUCCESS;
    }

}

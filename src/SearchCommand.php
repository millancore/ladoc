<?php

namespace Lo;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use function Termwind\render;

#[AsCommand(
    name: 'search'
)]
class SearchCommand extends Command
{
    public function configure()
    {
        // add argument
        $this->addArgument('query', InputArgument::REQUIRED, 'Search string');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $query = $input->getArgument('query');

        // search in content file using preg and Symfony process, return file name
        $process = new Process(['grep', '-rl', $query, 'index/validation']);
        $process->run();

        // process output as array
        $output = explode("\n", $process->getOutput());

        //remove empty values
        $output = array_filter($output);

        $content = '';
        foreach ($output as $file) {
            //ad hr

            $content .= file_get_contents($file);
            $content .= '<hr>';
        }

//        echo $content;
//        die;

        render('<div>'.$content.'</div>');

        return  Command::SUCCESS;
    }

}
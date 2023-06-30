<?php

namespace Lo;

use Symfony\Component\Process\Process;

class Downloader
{

    //check if git is installed
    public function checkGit()
    {
        $process = new Process(['git', '--version']);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        echo $process->getOutput();
    }

    public function download()
    {
        $repositoryUrl = 'https://github.com/laravel/docs.git';
        $branchName = '10.x'; // Replace with the desired branch name
        $targetDirectory =  ROOT_APP.'/.docs/'.$branchName;

// Create the process instance
        $process = new Process(['git', 'clone', '--branch', $branchName, $repositoryUrl, $targetDirectory]);

// Run the process
        $process->run();

// Check if the process was successful
        if ($process->isSuccessful()) {
            // Repository cloned successfully
            echo 'Repository cloned successfully.';
        } else {
            // An error occurred
            echo 'An error occurred while cloning the repository: '.$process->getErrorOutput();
        }
    }

}
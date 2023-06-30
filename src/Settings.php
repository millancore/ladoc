<?php

namespace Lo;

readonly class Settings
{
    public string $repositoryUrl;
    public string $docPath;

    public string $indexPath;

    public function __construct(array $settings)
    {
        $this->repositoryUrl = $settings['repository'];
        $this->docPath = $settings['doc_path'];
        $this->indexPath = $settings['index_path'];

    }

}
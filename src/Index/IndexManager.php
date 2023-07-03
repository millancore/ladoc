<?php

namespace Lo\Index;

use Exception;
use Lo\Enum\Version;

class IndexManager
{
    private IndexList $mainIndex;

    /**
     * @throws Exception
     */
    public function __construct(private readonly Version $version)
    {
        $this->init();
    }

    /**
     * @throws Exception
     */
    private function init(): void
    {
        if (!is_dir($this->getVersionIndexPath())) {
            throw new Exception('Index folder not found');
        }

        $this->loadMainIndex();
    }


    /**
     * @throws Exception
     */
    public function loadMainIndex(): void
    {
        if(!$this->mainIndexFileExist()) {
            throw new Exception('Index file not found');
        }

        $this->mainIndex = unserialize(file_get_contents($this->getVersionIndexPath() . '/index'));
    }

    public function getMainIndex(): IndexList
    {
        return $this->mainIndex;
    }

    public function saveMainIndex(): void
    {
        file_put_contents(
            $this->getVersionIndexPath() . '/index',
            serialize($this->mainIndex)
        );
    }

    /**
     * @throws Exception
     */
    public function getIndexSection(string $section): IndexList
    {
        if(!$this->sectionIndexFileExist($section)) {
            throw new Exception('Index section file not found');
        }

        return unserialize(file_get_contents($this->getVersionIndexPath() . '/' . $section . '/index'));
    }

    public function saveIndexSection(string $section, IndexList $indexList): void
    {
        file_put_contents(
            $this->getVersionIndexPath() . '/' . $section . '/index',
            serialize($indexList)
        );
    }


    public function getVersionIndexPath(): string
    {
        return INDEX_DIR . '/' . $this->version->value;
    }

    public function mainIndexFileExist(): bool
    {
        return file_exists($this->getVersionIndexPath() . '/index');
    }

    public function sectionIndexFileExist(string $section): bool
    {
        return file_exists($this->getVersionIndexPath() . '/' . $section . '/index');
    }


}

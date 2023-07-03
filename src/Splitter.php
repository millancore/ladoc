<?php

namespace Lo;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Lo\Formatter\FormatterInterface;
use Lo\Index\IndexList;
use Lo\Index\ItemList;

readonly class Splitter
{
    private DOMDocument $domDocument;

    public function __construct(
        private string $section,
        private string $htmlContent,
        private FormatterInterface $formatter
    ) {
        $this->domDocument = new DOMDocument();
        $this->domDocument->loadHTML($htmlContent);
    }

    public function splitArticles(): array
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML($this->formatter->format($this->htmlContent));

        $xpath = new DOMXPath($domDocument);

        $elements = $xpath->query('//p[a/@name]');

        $articles = [];
        $currentKey = '';
        $currentSection = '';
        foreach ($elements as $element) {

            $name = $element->getElementsByTagName('a')->item(0)->getAttribute('name');

            if (!empty($currentSection)) {
                $articles[$currentKey] = $currentSection;
            }

            $currentKey = $name;
            $currentSection = '';

            $currentNode = $element;
            while ($currentNode = $currentNode->nextSibling) {
                if ($currentNode instanceof DOMElement
                    && $currentNode->tagName === 'p'
                    && $currentNode->childNodes->length === 1
                    && $currentNode->childNodes[0] instanceof DOMElement
                    && $currentNode->childNodes[0]->tagName === 'a') {
                    break;
                }

                $currentSection .= $domDocument->saveHTML($currentNode);
            }
        }

        if (!empty($currentSection)) {
            $articles[$currentKey] = $currentSection;
        }

        return array_filter($articles);
    }

    public function getTitle(): ?ItemList
    {
        $firstTitle = $this->domDocument->getElementsByTagName('h1')[0];

        if (!$firstTitle) {
            return null;
        }

        return new ItemList($firstTitle->nodeValue, $this->section, new IndexList());
    }


    public function getIndexList(): IndexList
    {
        // Get first list element
        $list = $this->domDocument->getElementsByTagName('ul')[0];

        return $this->parseList($list);
    }

    public function parseList($list): IndexList
    {
        $result = new IndexList();

        foreach ($list->childNodes as $item) {
            if ($item->nodeName === 'li') {

                $children = $item->getElementsByTagName('ul');
                $anchor = $item->getElementsByTagName('a');

                $key = str_replace('#', '', $anchor[0]->getAttribute('href'));

                $value = $anchor[0]->nodeValue;

                $sub = new IndexList();
                if ($children->length > 0) {
                    $sub->attach($this->parseList($children[0]));
                }

                $result->attach(new ItemList($value, $key, $sub));
            }
        }

        return $result;
    }

}

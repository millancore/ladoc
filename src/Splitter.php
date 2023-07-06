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
        private string $htmlContent,
    ) {
        $this->domDocument = new DOMDocument();
        $this->domDocument->loadHTML($htmlContent, LIBXML_NOERROR | LIBXML_COMPACT);
    }


    /**
     * @param FormatterInterface $formatter
     * @return array<string, string>
     */
    public function splitArticles(FormatterInterface $formatter): array
    {
        $htmlContent = $formatter->format($this->htmlContent);

        $domDocument = new DOMDocument();
        $domDocument->loadHTML($htmlContent, LIBXML_NOERROR | LIBXML_COMPACT);

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

    public function getTitle(): ?string
    {
        $firstTitle = $this->domDocument->getElementsByTagName('h1')[0];

        if (!$firstTitle) {
            return null;
        }

        return $firstTitle->nodeValue;
    }


    public function getIndexList(): IndexList
    {
        // Get first list element
        $list = $this->domDocument->getElementsByTagName('ul')[0];

        $sectionList = $this->parseList($list);

        if ($this->getTitle()) {
            $sectionList->setName($this->getTitle());
        }

        return $sectionList;
    }

    public function parseList(?DOMElement $list): IndexList
    {
        $result = new IndexList();

        if (!$list) {
            return $result;
        }

        foreach ($list->childNodes as $item) {
            if ($item->nodeName === 'li') {

                if(empty($item->nodeValue)) {
                    continue;
                }

                $children = $item->getElementsByTagName('ul');
                $anchor = $item->getElementsByTagName('a');

                $key = str_replace('#', '', $anchor[0]->getAttribute('href'));

                $value = $anchor[0]->nodeValue;

                $result->attach(new ItemList($value, $key, $this->parseList($children[0])));
            }
        }

        return $result;
    }

}

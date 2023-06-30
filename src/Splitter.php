<?php

namespace Lo;

use DOMDocument;
use DOMElement;
use DOMXPath;

readonly class Splitter
{
    public function __construct(private string $htmlContent)
    {
        //
    }

    public function splitSections() : array
    {
        $dom = new DOMDocument();
        $dom->loadHTML($this->htmlContent);

        $xpath = new DOMXPath($dom);

        $elements = $xpath->query('//p[a/@name]');

        $sections = [];
        $currentKey = '';
        $currentSection = '';
        foreach ($elements as $element) {

            $name = $element->getElementsByTagName('a')->item(0)->getAttribute('name');

            if (!empty($currentSection)) {
                $sections[$currentKey] = $currentSection;
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

                $currentSection .= $dom->saveHTML($currentNode);
            }
        }

        if (!empty($currentSection)) {
            $sections[$currentKey] = $currentSection;
        }

        return array_filter($sections);
    }

}
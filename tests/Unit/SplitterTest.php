<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Formatter\TermwindFormatter;
use Ladoc\Index\IndexList;
use Ladoc\Splitter;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Splitter
 */
class SplitterTest extends TestCase
{
    /**
     * @uses \Ladoc\Formatter\TermwindFormatter
     */
    public function test_it_can_split_section_articles(): void
    {

        $htmlContent = <<<'HTML'
<body>
     <p><a name="test-1"></a></p>
    <h1>Test-1</h1>
    <p>lorem</p>
      <p><a name="test-2"></a></p>
    <h2>Test-2</h2>
    <p>lorem</p>
</body>
HTML;

        $splitter = new Splitter($htmlContent);

        $sections = $splitter->splitArticles(new TermwindFormatter());

        $this->assertCount(2, $sections);
        $this->assertEquals('test-1', array_key_first($sections));
        $this->assertEquals(
            '<pclass="title">Test-1</p><p>lorem</p>',
            str_replace([PHP_EOL, ' '], '', $sections['test-1'])
        );
    }

    public function test_it_can_get_first_h1_title(): void
    {
        $htmlContent = <<<'HTML'
    <h1>Test-1</h1>
    <p>lorem</p>
    <h2>Test-2</h2>
    <p>lorem</p>
HTML;

        $splitter = new Splitter($htmlContent);

        $title = $splitter->getTitle();
        $this->assertEquals('Test-1', $title);
    }

    public function test_it_return_null_if_html_have_not_h1_title(): void
    {
        $htmlContent = <<<'HTML'
    <h2>Test-2</h2>
    <p>lorem</p>
HTML;

        $splitter = new Splitter($htmlContent);

        $title = $splitter->getTitle();
        $this->assertNull($title);

    }


    /**
     * @uses  \Ladoc\Index\IndexList
     * @uses  \Ladoc\Index\ItemList
     */
    public function test_it_parse_html_list_to_index_list(): void
    {
        $htmlContent = <<<'HTML'
<body>
    <h1>Test-1</h1>
    <p>lorem</p>
    <ul>
        <li>
        <a href="#test-1">Test-1</a>
             <ul>
                <li><a href="#test-1.1">Test-1.1</a></li>
                <li><a href="#test-1.2">Test-1.2</a></li>
            </ul>
        </li>
        <li><a href="#test-2">Test-2</a></li>
    </ul>
    <h2>Test-2</h2>
    <p>lorem</p>
HTML;

        $splitter = new Splitter($htmlContent);

        $index = $splitter->getIndexList();

        $this->assertInstanceOf(IndexList::class, $index);
        $this->assertCount(2, $index);
        $this->assertEquals('Test-2', $index->get(1)->title);
        $this->assertInstanceOf(IndexList::class, $index->get(0)->children);
        $this->assertCount(2, $index->get(0)->children);
        $this->assertEquals('Test-1.1', $index->get(0)->children->get(0)->title);

    }
}

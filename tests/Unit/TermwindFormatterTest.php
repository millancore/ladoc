<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Formatter\TermwindFormatter;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Formatter\TermwindFormatter
 */
class TermwindFormatterTest extends TestCase
{
    public function test_it_can_remove_style_blocks(): void
    {

        $formatter = new TermwindFormatter();

        $html = '<style>body { color: red; }</style><p>Test</p>';
        $expected = '<p>Test</p>';

        $this->assertEquals($expected, $formatter->removeStyleBlocks($html));
    }


    public function test_it_can_set_title_styles(): void
    {
        $formatter = new TermwindFormatter();

        $html = '<h1>Test</h1>';
        $expected = '<p class="text-2xl font-bold">Test</p>';

        $this->assertEquals($expected, $formatter->setTitleStyles('text-2xl font-bold', $html));
    }

    public function test_it_can_set_inline_code_styles(): void
    {
        $formatter = new TermwindFormatter();

        $html = '<code>Test</code><p>lorem</p><code>extra</code>';
        $expected = '<span class="bg-gray-100">Test</span><p>lorem</p><span class="bg-gray-100">extra</span>';

        $this->assertEquals($expected, $formatter->setInlineCodeStyles('bg-gray-100', $html));
    }

    public function test_it_can_remove_all_pre_tags(): void
    {
        $formatter = new TermwindFormatter();

        $html = '<pre>Test</pre><p>lorem</p><pre>extra</pre>';
        $expected = 'Test<p>lorem</p>extra';

        $this->assertEquals($expected, $formatter->removePreTags($html));
    }

    public function test_it_can_format_html_document(): void
    {
        $formatter = new TermwindFormatter();

        $html = '<style>body { color: red; }</style><h1>Test</h1><code>Test</code><p>lorem</p><pre>extra</pre>';
        $expected = '<p class="title">Test</p><span class="inline-code">Test</span><p>lorem</p>extra';

        $this->assertEquals($expected, $formatter->format($html));
    }
}

<?php

namespace Lo\Tests\Unit;

use Lo\Splitter;

class SplitterTest extends TestCase
{
    public function test_it_can_split_sections()
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

        $sections = $splitter->splitArticles();

        $this->assertCount(2, $sections);
        $this->assertEquals('test-1', array_key_first($sections));
        $this->assertEquals(
            '<h1>Test-1</h1><p>lorem</p>',
            str_replace([PHP_EOL, ' '], '', $sections['test-1'])
        );
    }
}
<?php

namespace Lo\Tests\Unit;

use Lo\Index\IndexList;
use Lo\Section;

/**
 * @covers \Lo\Section
 * @covers \Lo\Index\IndexList
 */
class SectionTest extends TestCase
{
    public function test_it_can_access_properties(): void
    {
        $section = new Section('title', new IndexList(), [
            'article-1' => 'content-1',
            'article-2' => 'content-2',
        ]);

        $this->assertSame('title', $section->name);
        $this->assertInstanceOf(IndexList::class, $section->indexList);
        $this->assertSame([
            'article-1' => 'content-1',
            'article-2' => 'content-2',
        ], $section->articles);
    }

}

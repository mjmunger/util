<?php


use hphio\util\Slugify;
use PHPUnit\Framework\TestCase;

class SlugifyTest extends TestCase
{

    /**
     * @return void
     * @dataProvider providerTestGetSlug
     */
    public function testGetSlug($input, $expected)
    {
        $this->assertSame($expected, Slugify::getSlug($input));
    }

    public function providerTestGetSlug()
    {
        return [
            $this->withPunctuation(),
            $this->withSpaces()
        ];
    }

    private function withSpaces()
    {
        return [
            "this is a test", 'this-is-a-test'
        ];
    }

    private function withPunctuation()
    {
        return [
            "ThIs# IS$, a !@#$ TeST!", "this-is-a-test"
        ];
    }
}

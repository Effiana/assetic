<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Resource;

use Assetic\Factory\Resource\DirectoryResource;

class DirectoryResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFresh()
    {
        $resource = new DirectoryResource(__DIR__);
        $this->assertTrue($resource->isFresh(time() + 5));
        $this->assertFalse($resource->isFresh(0));
    }

    /**
     * @dataProvider getPatterns
     */
    public function testGetContent($pattern)
    {
        $resource = new DirectoryResource(__DIR__, $pattern);
        $content = $resource->getContent();

        $this->assertInternalType('string', $content);
    }

    public function getPatterns()
    {
        return array(
            array(null),
            array('/\.php$/'),
            array('/\.foo$/'),
        );
    }

    /**
     * @dataProvider getPatternsAndEmpty
     */
    public function testIteration($pattern, $empty)
    {
        $resource = new DirectoryResource(__DIR__, $pattern);

        $count = 0;
        foreach ($resource as $r) {
            ++$count;
            $this->assertInstanceOf('Assetic\\Factory\\Resource\\ResourceInterface', $r);
        }

        if ($empty) {
            $this->assertEmpty($count);
        } else {
            $this->assertNotEmpty($count);
        }
    }

    public function getPatternsAndEmpty()
    {
        return array(
            array(null, false),
            array('/\.php$/', false),
            array('/\.foo$/', true),
        );
    }
}
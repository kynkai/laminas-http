<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptEncoding;

class AcceptEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptEncodingFromStringCreatesValidAcceptEncodingHeader()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $acceptEncodingHeader);
        $this->assertInstanceOf('Laminas\Http\Header\AcceptEncoding', $acceptEncodingHeader);
    }

    public function testAcceptEncodingGetFieldNameReturnsHeaderName()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $this->assertEquals('Accept-Encoding', $acceptEncodingHeader->getFieldName());
    }

    public function testAcceptEncodingGetFieldValueReturnsProperValue()
    {
        $acceptEncodingHeader = AcceptEncoding::fromString('Accept-Encoding: xxx');
        $this->assertEquals('xxx', $acceptEncodingHeader->getFieldValue());
    }

    public function testAcceptEncodingToStringReturnsHeaderFormattedString()
    {
        $acceptEncodingHeader = new AcceptEncoding();
        $acceptEncodingHeader->addEncoding('compress', 0.5)
                             ->addEncoding('gzip', 1);

        $this->assertEquals('Accept-Encoding: compress;q=0.5, gzip', $acceptEncodingHeader->toString());
    }

    /** Implmentation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.5,gzip');
        $this->assertTrue($header->hasEncoding('compress'));
        $this->assertTrue($header->hasEncoding('gzip'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptEncoding::fromString('Accept-Encoding: compress;q=0.8,gzip,*;q=0.4');
        $expected = array(
            'gzip',
            'compress',
            '*'
        );

        $test = array();
        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->getEncoding());
        }
    }

    public function testWildcharEncoder()
    {
        $acceptHeader = new AcceptEncoding();
        $acceptHeader->addEncoding('compress', 0.8)
                     ->addEncoding('*', 0.4);

        $this->assertTrue($acceptHeader->hasEncoding('compress'));
        $this->assertTrue($acceptHeader->hasEncoding('gzip'));
        $this->assertEquals('Accept-Encoding: compress;q=0.8, *;q=0.4', $acceptHeader->toString());
    }
}

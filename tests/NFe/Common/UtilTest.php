<?php
namespace NFe\Common;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testToCurrency()
    {
        $this->assertEquals('25.00', Util::toCurrency(25));
        $this->assertEquals('2.56', Util::toCurrency(2.556));
        $this->assertEquals('2.556', Util::toCurrency(2.5556, 3));
    }

    public function testToFloat()
    {
        $this->assertEquals('25.0000', Util::toFloat(25));
        $this->assertEquals('2.5556', Util::toFloat(2.55556));
        $this->assertEquals('2.55556', Util::toFloat(2.555556, 5));
    }

    public function testIsLess()
    {
        $this->assertTrue(Util::isLess(0.00, 0.01));
        $this->assertFalse(Util::isLess(0.01, 0.00));
        $this->assertFalse(Util::isLess(0.01, 0.01));
    }

    public function testCreateDirectory(Type $var = null)
    {
        $root = dirname(dirname(dirname(__DIR__)));
        $path = $root . '/storage/test123';
        $this->assertFalse(file_exists($path));
        Util::createDirectory($path);
        $this->assertTrue(file_exists($path));
        rmdir($path);
        $this->assertFalse(file_exists($path));
    }

    public function testGetModulo10()
    {
        $this->assertEquals(6, Util::getModulo10('1264361568465'));
        $this->assertEquals(5, Util::getModulo10('089464987464568'));
        $this->assertEquals(9, Util::getModulo10('98762165498436198'));
    }

    public function testGetDAC()
    {
        $this->assertEquals(4, Util::getDAC('1264361568465', 10));
        $this->assertEquals(1, Util::getDAC('089464987464568', 11));
        $this->assertEquals(8, Util::getDAC('98762165498436198', 11));
    }

    public function testFindNode()
    {
        $dom = new \DOMDocument();
        $element = $dom->createElement('foo');
        $element->appendChild($dom->createElement('bar', 1));
        $dom->appendChild($element);
        $found = Util::findNode($element, 'bar');
        $this->assertXmlStringEqualsXmlString('<bar>1</bar>', $dom->saveXML($found));
        $found = Util::findNode($element, 'foo');
        $this->assertXmlStringEqualsXmlString('<foo><bar>1</bar></foo>', $dom->saveXML($found));
        $this->setExpectedException('\Exception');
        Util::findNode($element, 'other');
    }
}

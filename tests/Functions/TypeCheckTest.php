<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Improved\type_check
 * @covers \Improved\Internal\type_check_error
 * @covers \Improved\Internal\type_join_descriptions
 */
class TypeCheckTest extends TestCase
{
    public function validProvider()
    {
        $streamResource = fopen('data://text/plain,a', 'r');
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);

        return [
            [10, 'int'],
            [10, 'integer'],
            [true, 'bool'],
            [true, 'boolean'],
            [[], 'array'],
            [(object)[], 'stdClass'],
            [10, ['int', 'boolean']],
            ['hello', 'string'],
            ['hello', '?string'],
            [null, '?string'],
            [$streamResource, 'resource'],
            [$streamResource, 'stream resource'],
            [$streamResource, ['stream resource', 'gd resource']],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testValid($var, $type)
    {
        $ret = i\type_check($var, $type);
        $this->assertEquals($var, $ret);
    }


    public function invalidProvider()
    {
        $streamResource = fopen('data://text/plain,a', 'r');
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);

        return [
            [0, 'boolean', 'Expected boolean, int(0) given'],
            [10, 'boolean', 'Expected boolean, int(10) given'],
            ['foo', 'int', 'Expected int, string(3) "foo" given'],
            ['foo', ['int', 'boolean'], 'Expected int or boolean, string(3) "foo" given'],
            [10, '?string', 'Expected string or null, int(10) given'],
            [(object)[], 'Foo', 'Expected instance of Foo, instance of stdClass given'],
            [$streamResource, 'string', 'Expected string, stream resource given'],
            [$streamResource, 'gd resource', 'Expected gd resource, stream resource given'],
            [$streamResource, ['int', 'gd resource'], 'Expected int or gd resource, stream resource given'],
            [$streamResource, 'stream', 'Expected instance of stream, stream resource given'],
            [$closedResource, 'string', 'Expected string, resource (closed) given']
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalid($var, $type, $error)
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage($error);

        i\type_check($var, $type);
    }


    public function testNoException()
    {
        $ret = i\type_check(10, 'int', new \InvalidArgumentException("Lorem ipsum", 42));
        
        $this->assertEquals(10, $ret);
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected int, string(3) "foo" given
     */
    public function testWithExceptionClass()
    {
        i\type_check('foo', 'int', new \InvalidArgumentException());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum
     * @expectedExceptionCode 42
     */
    public function testWithException()
    {
        i\type_check('foo', 'int', new \InvalidArgumentException("Lorem ipsum", 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum string(3) "foo" black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessage()
    {
        i\type_check('foo', 'int', new \InvalidArgumentException("Lorem ipsum %s black", 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem int ipsum string(3) "foo" black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessageType()
    {
        $exception = new \InvalidArgumentException('Lorem %2$s ipsum %1$s black', 42);
        i\type_check('foo', 'int', $exception);
    }


    public function testTypeCheckErrorExtraArgs()
    {
        $exception = new \InvalidArgumentException('%2$s: %4$s ipsum %1$s / %3$d black');
        $result = i\Internal\type_check_error('foo', 'int', $exception, '', ['Not good', 22]);

        $this->assertInstanceOf(\InvalidArgumentException::class, $result);
        $this->assertEquals('Not good: int ipsum string(3) "foo" / 22 black', $result->getMessage());
    }
}

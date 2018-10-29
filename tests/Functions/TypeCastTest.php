<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers Improved\type_cast
 * @covers Improved\Internal\type_cast_var
 * @covers Improved\Internal\type_check_throw
 * @covers Improved\Internal\type_cast_string_int
 * @covers Improved\Internal\type_cast_string_float
 * @covers Improved\Internal\type_cast_int_bool
 * @covers Improved\Internal\type_cast_bool_int
 * @covers Improved\Internal\type_cast_float_int
 * @covers Improved\Internal\type_cast_int_float
 * @covers Improved\Internal\type_cast_int_string
 * @covers Improved\Internal\type_cast_float_string
 * @covers Improved\Internal\type_cast_object_string
 * @covers Improved\Internal\type_cast_object_array
 * @covers Improved\Internal\type_cast_array_object
 */
class TypeCoerceTest extends TestCase
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
            [$streamResource, 'resource'],
            [$streamResource, 'stream resource'],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testValid($var, $type)
    {
        $ret = i\type_cast($var, $type);
        $this->assertEquals($var, $ret);
    }


    public function castProvider()
    {
        $object = new class() {
            public function __toString(): string
            {
                return 'hello';
            }
        };

        return [
            ['10', 'int', 10],
            ['-10', 'int', -10],
            ['10.4', 'int', 10],
            ['2.4', 'float', 2.4],
            ['-7', 'float', -7.0],
            ['1' . str_repeat('0', 100), 'float', 1.0e+100],
            [0, 'bool', false],
            [1, 'bool', true],
            [1, 'boolean', true],
            [42, 'float', 42.0],
            [42, 'double', 42.0],
            [42, 'string', '42'],
            [-42, 'string', '-42'],
            [42.2, 'int', 42],
            [-7.6, 'int', -7],
            [-7.6, 'integer', -7],
            [-7.6, 'string', '-7.6'],
            [1.0e+100, 'string', '1.0E+100'],
            [false, 'int', 0],
            [true, 'int', 1],
            [['one' => 'I', 'two' => 'II'], 'object', (object)['one' => 'I', 'two' => 'II']],
            [['one' => 'I', 'two' => 'II'], \stdClass::class, (object)['one' => 'I', 'two' => 'II']],
            [$object, 'string', 'hello'],
            [(object)['one' => 'I', 'two' => 'II'], 'array', ['one' => 'I', 'two' => 'II']],
            [(object)['one' => 'I', 'two' => 'II'], 'iterable', ['one' => 'I', 'two' => 'II']],
        ];
    }

    /**
     * @dataProvider castProvider
     */
    public function testCoerce($var, $type, $expected)
    {
        $ret = i\type_cast($var, $type);
        $this->assertEquals($expected, $ret, gettype($var) . ':' . $type);
    }


    public function invalidProvider()
    {
        return [
            ['foo', 'int', "Expected int, string given"],
            ['1' . str_repeat('0', 100), 'int', "Expected int, string given"],
            ['foo', 'float', "Expected float, string given"],
            [10, 'boolean', "Expected boolean, integer given"],
            [1.0e+100, 'int', "Expected int, float given"],
            [['one', 'two'], 'object', "Expected object, array given"],
            [['a' => 'one', 7 => 'two'], 'object', "Expected object, array given"],
            [new \DateTime(), 'string', "Expected string, DateTime object given"],
            [new \DateTime(), 'array', "Expected array, DateTime object given"],
            [(object)[], 'Foo', "Expected Foo object, stdClass object given"],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalid($var, $type, $error)
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage($error);

        i\type_cast($var, $type);
    }


    public function testNoException()
    {
        $ret = i\type_cast(10, 'int', new \InvalidArgumentException("Lorem ipsum", 42));

        $this->assertSame(10, $ret);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum
     * @expectedExceptionCode 42
     */
    public function testWithException()
    {
        i\type_cast('foo', 'int', new \InvalidArgumentException("Lorem ipsum", 42));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum string black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessage()
    {
        i\type_cast('foo', 'int', new \InvalidArgumentException("Lorem ipsum %s black", 42));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Lorem int ipsum string black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessageType()
    {
        $exception = new \InvalidArgumentException('Lorem %2$s ipsum %1$s black', 42);
        i\type_cast('foo', 'int', $exception);
    }
}

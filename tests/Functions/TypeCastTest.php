<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Improved\type_cast
 * @covers \Improved\Internal\type_cast_var
 * @covers \Improved\Internal\type_check_error
 * @covers \Improved\Internal\type_join_descriptions
 * @covers \Improved\Internal\type_cast_string_int
 * @covers \Improved\Internal\type_cast_string_float
 * @covers \Improved\Internal\type_cast_int_bool
 * @covers \Improved\Internal\type_cast_bool_int
 * @covers \Improved\Internal\type_cast_float_int
 * @covers \Improved\Internal\type_cast_int_float
 * @covers \Improved\Internal\type_cast_int_string
 * @covers \Improved\Internal\type_cast_float_string
 * @covers \Improved\Internal\type_cast_object_string
 * @covers \Improved\Internal\type_cast_object_array
 * @covers \Improved\Internal\type_cast_array_object
 * @covers \Improved\Internal\type_cast_null_string
 * @covers \Improved\Internal\type_cast_null_int
 * @covers \Improved\Internal\type_cast_null_float
 * @covers \Improved\Internal\type_cast_null_bool
 * @covers \Improved\Internal\type_cast_null_array
 * @covers \Improved\Internal\type_cast_null_object
 */
class TypeCastTest extends TestCase
{
    public function validProvider()
    {
        $streamResource = fopen('data://text/plain,a', 'r');
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);

        return [
            [10, 'int'],
            [10, 'integer'],
            [0.0, 'float'],
            [0.0, 'double'],
            [true, 'bool'],
            [true, 'boolean'],
            ['hello', 'string'],
            ['hello', '?string'],
            [null, '?string'],
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
            [10, '?string', '10'],
            [null, '?string', null],
            [false, 'int', 0],
            [true, 'int', 1],
            [null, 'string', ''],
            [null, 'int', 0],
            [null, 'float', 0.0],
            [null, 'bool', false],
            [null, 'array', []],
            [null, 'object', (object)[]],
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
    public function testCast($var, $type, $expected)
    {
        $ret = i\type_cast($var, $type);
        $this->assertEquals($expected, $ret, gettype($var) . ':' . $type);
    }


    public function invalidProvider()
    {
        return [
            ['foo', 'int', 'Unable to cast to int, string(3) "foo" given'],
            [
                '1' . str_repeat('0', 100),
                'int',
                'Unable to cast to int, string(101) "10000000000000000000000000000"... given'
            ],
            ['foo', 'float', 'Unable to cast to float, string(3) "foo" given'],
            [10, 'boolean', 'Unable to cast to boolean, int(10) given'],
            [1.0e+100, 'int', 'Unable to cast to int, float(1.0E+100) given'],
            [10, '?boolean', 'Unable to cast to boolean, int(10) given'],
            [null, 'Foo', 'Unable to cast to instance of Foo, null given'],
            [['one', 'two'], 'object', 'Unable to cast to object, array(2) given'],
            [['a' => 'one', 7 => 'two'], 'object', 'Unable to cast to object, array(2) given'],
            [new \DateTime(), 'string', 'Unable to cast to string, instance of DateTime given'],
            [new \DateTime(), 'array', 'Unable to cast to array, instance of DateTime given'],
            [(object)[], 'Foo', 'Unable to cast to instance of Foo, instance of stdClass given'],
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to cast to int, string(3) "foo" given
     */
    public function testWithExceptionClass()
    {
        i\type_cast('foo', 'int', new \InvalidArgumentException());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum
     * @expectedExceptionCode 42
     */
    public function testWithException()
    {
        i\type_cast('foo', 'int', new \InvalidArgumentException("Lorem ipsum", 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem ipsum string(3) "foo" black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessage()
    {
        i\type_cast('foo', 'int', new \InvalidArgumentException("Lorem ipsum %s black", 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Lorem int ipsum string(3) "foo" black
     * @expectedExceptionCode 42
     */
    public function testWithExceptionMessageType()
    {
        $exception = new \InvalidArgumentException('Lorem %2$s ipsum %1$s black', 42);
        i\type_cast('foo', 'int', $exception);
    }
}

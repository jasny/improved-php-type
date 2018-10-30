<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Improved\type_is
 * @covers \Improved\Internal\type_is_internal_func
 */
class TypeIsTest extends TestCase
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
            [null, 'null'],
            [[], 'array'],
            [(object)[], 'stdClass'],
            [10, ['int', 'boolean']],
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
        $this->assertTrue(i\type_is($var, $type));
    }


    public function invalidProvider()
    {
        $streamResource = fopen('data://text/plain,a', 'r');
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);

        return [
            [10, 'boolean'],
            ['foo', 'int'],
            ['foo', ['int', 'boolean']],
            [10, '?string'],
            [(object)[], 'Foo'],
            [$streamResource, 'string'],
            [$streamResource, 'gd resource'],
            [$streamResource, ['int', 'gd resource']],
            [$streamResource, 'stream'],
            [$closedResource, 'string'],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalid($var, $type)
    {
        $this->assertFalse(i\type_is($var, $type));
    }
}

<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Improved\type_describe
 * @covers \Improved\Internal\type_describe_type
 * @covers \Improved\Internal\type_describe_value
 */
class TypeDescribeTest extends TestCase
{
    public function provider()
    {
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);
        return [
            [null, 'null', 'null'],
            [10, 'integer', 'int(10)'],
            [10.2, 'float', 'float(10.2)'],
            [true, 'boolean', 'bool(true)'],
            ["hello", 'string', 'string(5) "hello"'],
            [str_repeat('x', 100), 'string', 'string(100) "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx"...'],
            [['one', 'two', 'three'], 'array', 'array(3)'],
            [(object)[], 'instance of stdClass', 'instance of stdClass'],
            [new \DateTime(), 'instance of DateTime', 'instance of DateTime'],
            [fopen('data://text/plain,hello', 'r'), 'stream resource', 'stream resource'],
            [$closedResource, 'resource (closed)', 'resource (closed)']
        ];
    }

    /**
     * @dataProvider provider
     */
    public function test($var, $expected)
    {
        $type = i\type_describe($var);
        $this->assertSame($expected, $type);
    }

    /**
     * @dataProvider provider
     */
    public function testDetails($var, $_, $expected)
    {
        $type = i\type_describe($var, true);
        $this->assertSame($expected, $type);
    }
}

<?php declare(strict_types=1);

namespace Improved\Tests\Functions;

use Improved as i;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Improved\type_describe
 */
class TypeDescribeTest extends TestCase
{
    public function provider()
    {
        $closedResource = fopen('php://memory', 'r+');
        fclose($closedResource);
        return [
            [10, 'integer'],
            [10.2, 'float'],
            [true, 'boolean'],
            [[], 'array'],
            [(object)[], 'stdClass object'],
            [new \DateTime(), 'DateTime object'],
            [fopen('data://text/plain,hello', 'r'), 'stream resource'],
            [$closedResource, 'resource (closed)']
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
}

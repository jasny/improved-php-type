![improved PHP library](https://user-images.githubusercontent.com/100821/46372249-e5eb7500-c68a-11e8-801a-2ee57da3e5e3.png)

# type handling

[![Build Status](https://travis-ci.org/improved-php-library/type.svg?branch=master)](https://travis-ci.org/improved-php-library/type)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/improved-php-library/type/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/improved-php-library/type/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/improved-php-library/type/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/improved-php-library/type/?branch=master)
[![Packagist Stable Version](https://img.shields.io/packagist/v/improved/type.svg)](https://packagist.org/packages/improved/type)
[![Packagist License](https://img.shields.io/packagist/l/improved/type.svg)](https://packagist.org/packages/improved/type)

Library for type handling.

This library provides a set of consistent functions for PHP. You should always use these types rather than the ones
provided by PHP natively.

## Installation

    composer require improved/type

## types

* [`type_is(mixed $var, string|string[] $type)`](#type_is)
* [`type_check(mixed $var, string|string[] $type, Throwable $throwable = null)`](#type_check)
* [`type_cast(mixed $var, string|string[] $type, Throwable $throwable = null)`](#type_cast)
* [`type_describe(mixed $var)`](#type_describe)

## Reference

### type_is

    bool type_is(mixed $var, string|string[] $type)
    
Return true if variable has the specified type.

As type you can specify any internal type, including `callable` and `object`, a class name or a resource type (eg
`stream resource`). 

Typed arrays or iteratators are **not** supported, as it would require looping through them.

A question mark can be added to a class to accept null, eg `"?string"` is similar to using `["string", "null"]`.

### type_check

    mixed type_check(mixed $var, string|string[] $type, Throwable $throwable = null)
    
Validate that a variable has a specific type. The same types cas in `type_is()` can be used.

Typed arrays or iteratators are **not** supported. Use
[`iterable_check_type`](https://github.com/improved-php-library/iterable#checktype) instead.

By default a `TypeError` is thrown. Optionally you can pass an exception instead. 

The message may contain a `%s`, which is replaced by the type description of `$var`. It optionally may contain a second
`%s` which is replaced by the description of the required type. Use sprintf positioning to use the required type first.

If the message or the exception or error contains a `%`, a new throwable of the same type is created with the filled
out message.

The function returns `$var` if the type matches, so you can use it while setting a variable.

```php
use Improved as i;

$date = i\type_check(get_some_date(), DateTimeInterface::class);
$name = i\type_check($user->getName(), 'string');
$number = i\type_check(get_distance(), ['int', 'float']);

$foo = i\type_check(do_something(), Foo::class, new UnexpectedException('Wanted %2$s, not %1$s'));
```

A question mark can be added to a class to accept null, eg `"?string"` is similar to using `["string", "null"]`.

### type_cast

    mixed type_cast(mixed $var, string $type, Throwable $throwable = null)

Check the variable has a specific type or can be casted to that type, otherwise throw a `TypeError` or exception.

This function is similar to `type_check`, with the difference that is will cast the value in some cases


| from     | to                    |                                          |
|----------|-----------------------|------------------------------------------|
| `string` | `int`                 | only numeric strings and < `PHP_INT_MAX` |
| `string` | `float`               | only numeric strings                     |
| `int`    | `bool`                | only 0 or 1                              |
| `int`    | `float`               |                                          |
| `int`    | `string`              |                                          |
| `float`  | `int`                 | if float < `PHP_INT_MAX`                 |
| `float`  | `string`              |                                          |
| `bool`   | `int`                 |                                          |
| `array`  | `object` \ `stdClass` | if array has no numeric keys             |
| `object` | `string`              | if only has `__toString()` method        |
| `object` | `array` \ `iterable`  | only `stdClass` objects                  |
| `null`   | any scalar            |                                          |
| `null`   | `array`               |                                          |
| `null`   | `object` \ `stdClass` |                                          |

In contrary to `type_is` and `type_check`, only one type may be specified.

A question mark can be added to a class to accept null, eg `?string` will try to cast everything to a string except
`null`.

### type_describe

    string type_describe(mixed $var, bool $detailed = false)

Get the type of a variable in a descriptive way to using in an (error) message.

For scalar, null and array values, the result is the same a `gettype`. Except for floats which will return `float`
rather than `double`.  

Objects are have their class name, appended with `object`. For resources the resource type is returned, appended with
`resource`.

```php
type_describe('hello');        // string
type_describe(22);             // integer
type_describe(STDIN);          // stream resource
type_describe(new DateTime()); // instance of DateTime
```

The detailed option describes both the value an type. This is similar to what is used in the error messages of
`type_check` and `type_cast`.

```php
type_describe('hello');         // string(5) "hello"
type_describe(22);              // int(22)
type_describe(["a", "b", "c"]); // array(3)
```

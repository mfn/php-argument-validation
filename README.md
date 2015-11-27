# Argument Validation library for PHP [ ![Travis Build Status](https://travis-ci.org/mfn/php-argument-validation.svg?branch=master)](https://travis-ci.org/mfn/php-argument-validation)

Homepage: https://github.com/mfn/php-argument-validation

# Blurb

This library can be used to validate parameter definitions against a list
of arguments.

The following PHP types are supported out of the box:
- `array`
- `any` Use for, well, any kind of type (aliased as `mixed`)
- `bool`|`boolean`
- `callable` Suports for the `['class', 'method']` syntax. For closures simply
  use the `\Closure` type hint
- `float`|`double`|`real`
- `int`|`integer`
- `number` (either `float` or `integer`)
- `numeric` (either `int` or `string`, if it represents a number)
- `object`
- `resource` Supports requiring a resource of a specific type with
  `resource<type>`, e.g. for files you can use `resource<stream>`
- `string`
- any classname you provide

Features:
- Optional types are supported by prefixing with a question mark: `?@var`. This
  syntax was chosen to not break IDE support, e.g. PhpStorm et al.
- Classes `instanceof` validation is performed too, just write the full
  qualified class name, i.e. `Class` or `Namespace\Class`. See later note on
  fully qualified symbol names.
- Typed arrays, e.g. `array<string>` or `string[]` as well as types for keys
  and values, i.e. `array<int,string>`
- Support alternative types, e.g. `int|string`

The philosophy of the type checker is to strictly check the arguments, no type
coercion magic is performed.

Note about classes and the leading backslash:

This library expects the docblock to have been "normalized", in a sense that all
symbol names are fully qualified **without** a leading backslash. 
"Fully qualified" also means they're already resolved against the namespace and
possibly use aliases. See https://github.com/mfn/php-docblock-normalize for a
library which can to all this.


# Requirements

PHP 5.6

# Install

Using composer: `composer.phar require mfn/argument-validation 0.1`

# Example

```PHP
<?php
use Mfn\ArgumentValidation\ExtractFromDocblock;
use Mfn\ArgumentValidation\ArgumentValidation;

$docblock = <<<'DOCBLOCK'
/**
 * @var array $someArray
 * @var bool $isCool
 */
DOCBLOCK;

$parameters = (new ExtractFromDocblock)->extract($docblock);

$arguments = [
    'someArray' => [],
    'isCool' => 'foobar',
];

$errors = (new ArgumentValidation)->validate($parameters, $arguments);

var_dump($errors);
```
This script will output:
```
array(1) {
  [0]=>
  string(86) "Argument $isCool does not match type 'bool': Expected instance of bool but received string"
}
```

# Create your own types

- implement `\Mfn\ArgumentValidation\Interfaces\TypeInterface`
  - the `getName()` and `getAliases()` returns string (array of strings) under which your type will be registered. This is the literal name/type which must be present in a type declaration.

- create an instance of `\Mfn\ArgumentValidation\ArgumentValidation` and register the type either via `registerType()` or `registerTypeAs()`

Three arguments are passed to your `validate()` method:

- `\Mfn\ArgumentValidation\Interfaces\TypeValidatorInterface` so you can call additional validations from your type, if necessary
- `\Mfn\ArgumentValidation\Interfaces\TypeDescriptionParserInterface` a description of an inner type (may be empty, see `isEmpty()` method)
- The actual `$value` to inspect

If your validation deems there's an error, throw a `\Mfn\ArgumentValidation\Exceptions\TypeError`

# Limitations

- nested arrays with key/value types are not supported, i.e.
  `array<int,array<int,string>>` will not be properly parsed

# Contribute

Fork it, hack on a feature branch, create a pull request, be awesome!

No developer is an island so adhere to these standards:

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

© Markus Fischer <markus@fischer.name>

<?php namespace Mfn\ArgumentValidation\Tests;

/*
 * This file is part of https://github.com/mfn/php-argument-validation
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Markus Fischer <markus@fischer.name>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Mfn\ArgumentValidation\Parameter;
use Mfn\ArgumentValidation\ArgumentValidation;
use Mfn\ArgumentValidation\Tests\Shims\ShimCollection;
use Mfn\ArgumentValidation\TypeDescriptionParser;
use Mfn\ArgumentValidation\Types\Array_\TraversableType;

class ArgumentValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArgumentValidation
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new ArgumentValidation();
    }

    public function testValidateString()
    {
        $parameters = [new Parameter('name', 'string')];
        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'string\': Expected string but integer received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'string\': Expected string but array received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'string\': Expected string but object<stdClass> received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateInteger()
    {
        $parameters = [new Parameter('name', 'integer')];
        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'integer\': Expected integer but string received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'integer\': Expected integer but array received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'integer\': Expected integer but object<stdClass> received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateIntAlias()
    {
        $parameters = [new Parameter('name', 'int')];
        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'int\': Expected integer but string received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'int\': Expected integer but array received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'int\': Expected integer but object<stdClass> received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateArrayAnyType()
    {
        $parameters = [new Parameter('data', 'array')];


        $arguments = ['data' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['some', 1, true],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array\': Expected array but string received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array\': Expected array but integer received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => true,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array\': Expected array but boolean received',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array\': Expected array but object<stdClass> received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateArrayStringArray()
    {
        $parameters = [new Parameter('data', 'string[]')];


        $arguments = ['data' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['some'],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['some', 'string'],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => [1],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array<string>\': Error in value at key #0 (\'0\'): Expected string but integer received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateTypedArrayString()
    {
        $parameters = [new Parameter('data', 'array<string>')];


        $arguments = ['data' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['some'],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['some', 'string'],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => [1],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array<string>\': Error in value at key #0 (\'0\'): Expected string but integer received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateTypedArrayKeyValue()
    {
        $parameters = [new Parameter('data', 'array<string,integer>')];


        $arguments = ['data' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = [
            'data' => [
                'key' => 1,
            ],
        ];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = [
            'data' => [
                'key' => 1,
                'key2' => 2,
            ],
        ];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['data' => ['string'],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $data does match type \'array<string,integer>\': Error in key #0: Expected string but integer received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateAlternativeTypes()
    {
        $parameters = [new Parameter('name', 'string|int')];
        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Parameter $name: did not match type declaration "string|int"',
        ];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Parameter $name: did not match type declaration "string|int"',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateAny()
    {
        $parameters = [new Parameter('name', 'any')];
        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);


        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateMissingRequired()
    {
        $parameters = [new Parameter('name', 'any')];
        $arguments = [];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Required argument $name of type any is missing',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateMissingOptional()
    {
        $parameters = [new Parameter('name', 'any', true)];
        $arguments = [];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateAttributeTooMany()
    {
        $parameters = [];
        $arguments = ['foo' => 'bar'];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'No parameter definition for argument $foo',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateClassInstance()
    {
        $parameters = [new Parameter('param', 'stdClass')];

        $arguments = ['param' => new \stdClass()];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['param' => 1];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $param does match type \'stdClass\': Expected instance of stdClass but received integer',
        ];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['param' => new \DateTime()];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $param does match type \'stdClass\': DateTime not an instance of stdClass',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidateUnknownType()
    {
        $parameters = [new Parameter('param', 'someRandomType')];

        $arguments = ['param' => 1];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $param does match type \'someRandomType\': Expected instance of someRandomType but received integer',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }


    public function testValidateNumber()
    {
        $parameters = [new Parameter('name', 'number')];

        $arguments = ['name' => 'string',];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'number\': Expected number (float or integer) but string received',
        ];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['name' => 1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['name' => 2.1,];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['name' => [],];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'number\': Expected number (float or integer) but array received',
        ];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['name' => new \stdClass(),];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $name does match type \'number\': Expected number (float or integer) but object<stdClass> received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidatResource()
    {
        $parameters = [new Parameter('file', 'resource')];

        $arguments = ['file' => $f = fopen(__FILE__, 'r')];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
        fclose($f);
    }

    public function testValidatResourceFile()
    {
        $parameters = [new Parameter('file', 'resource<string>')];

        $arguments = ['file' => fopen(__FILE__, 'r')];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $file does match type \'resource<string>\': Expected resource of type string but stream received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidatResourceFileStream()
    {
        $parameters = [new Parameter('file', 'resource<stream>')];

        $arguments = ['file' => fopen(__FILE__, 'r')];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidatResourceFileAlternaiveTypes()
    {
        $parameters = [new Parameter('file', 'resource<unknown|stream>')];

        $arguments = ['file' => fopen(__FILE__, 'r')];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testValidatResourceInvalidType()
    {
        $parameters = [new Parameter('file', 'resource<randomtype>')];

        $arguments = ['file' => fopen(__FILE__, 'r')];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'Argument $file does match type \'resource<randomtype>\': Expected resource of type randomtype but stream received',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testTraversableLikeArray()
    {
        $this->validator->registerTypeAs(new TraversableType(), 'array');

        $parameters = [new Parameter('collection', 'array')];

        $arguments = ['collection' => []];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);

        $arguments = ['collection' => new ShimCollection()];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testEmptyParamArgumentError()
    {
        $parameters = [];
        $arguments = ['foo' => 'bar'];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [
            'No parameter definition for argument $foo',
        ];
        $this->assertEquals($expetedErrors, $errors);
    }

    public function testEmptyParamArgumentIgnored()
    {
        $this->validator = new ArgumentValidation(TypeDescriptionParser::class,
            false);

        $parameters = [];
        $arguments = ['foo' => 'bar'];
        $errors = $this->validator->validate($parameters, $arguments);
        $expetedErrors = [];
        $this->assertEquals($expetedErrors, $errors);
    }
}

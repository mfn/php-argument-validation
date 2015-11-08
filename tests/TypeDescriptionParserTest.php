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

use Mfn\ArgumentValidation\TypeDescriptionParser;

class TypeDescriptionParserTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $result = (new TypeDescriptionParser(''))->parseOuterTypes();
        $expectedResult = [];
        $this->assertEquals($expectedResult, $result);
    }

    public function testSingleType()
    {
        $result = (new TypeDescriptionParser('type'))->parseOuterTypes();
        $expectedResult = [
            'type',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultipleTypes()
    {
        $result = (new TypeDescriptionParser('type1|type2'))->parseOuterTypes();
        $expectedResult = [
            'type1',
            'type2',
        ];
        $this->assertEquals($expectedResult, $result);

        $result = (new TypeDescriptionParser('type1|type2|type3'))->parseOuterTypes();
        $expectedResult = [
            'type1',
            'type2',
            'type3',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testSingleNested()
    {
        $result = (new TypeDescriptionParser('array<type1>'))->parseOuterTypes();
        $expectedResult = [
            'array<type1>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testSingleNestedMulti()
    {
        $result = (new TypeDescriptionParser('array<type1|type2>'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|type2>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultiNestedSingle()
    {
        $result = (new TypeDescriptionParser('array<type1>|type2'))->parseOuterTypes();
        $expectedResult = [
            'array<type1>',
            'type2',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultiNestedMulti()
    {
        $result = (new TypeDescriptionParser('array<type1|type2>|type3'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|type2>',
            'type3',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultiNestedMultiMoar()
    {
        $result = (new TypeDescriptionParser('array<type1|type2>|type3|array<type4>'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|type2>',
            'type3',
            'array<type4>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultiNestedMultiEvenMoar()
    {
        $result = (new TypeDescriptionParser('array<type1|type2>|type3|array<type4|type5>'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|type2>',
            'type3',
            'array<type4|type5>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testMultiNestedMultiEvenMoarMoar()
    {
        $result = (new TypeDescriptionParser('array<type1|type2>|type3|array<type4|type5>|type6'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|type2>',
            'type3',
            'array<type4|type5>',
            'type6',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * This test is wrong, it should return
     *      'array<type1|array<type2>>'
     * It's a limitation of the current parser.
     */
    public function testNestedKeyValueNested()
    {
        $result = (new TypeDescriptionParser('array<type1|array<type2>>'))->parseOuterTypes();
        $expectedResult = [
            'array<type1|array<type2>',
            '>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testShortArraySyntax()
    {
        $result = (new TypeDescriptionParser('type[]'))->parseOuterTypes();
        $expectedResult = [
            'array<type>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testShortArraySyntaxMulti()
    {
        $result = (new TypeDescriptionParser('foo|type[]'))->parseOuterTypes();
        $expectedResult = [
            'foo',
            'array<type>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testShortArraySyntaxNested()
    {
        $result = (new TypeDescriptionParser('array<type[]>'))->parseOuterTypes();
        $expectedResult = [
            'array<type[]>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testParseKeyValueTypeEmpty()
    {
        $result = (new TypeDescriptionParser(''))->parseKeyValueTypes();
        $expectedResult = [
            '',
            '',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testParseKeyValueTypeOnlyValue()
    {
        $result = (new TypeDescriptionParser('value'))->parseKeyValueTypes();
        $expectedResult = [
            '',
            'value',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testParseKeyValueType()
    {
        $result = (new TypeDescriptionParser('key,value'))->parseKeyValueTypes();
        $expectedResult = [
            'key',
            'value',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testParseKeyValueTypeNested()
    {
        $result = (new TypeDescriptionParser('key,value<key2,value2>'))->parseKeyValueTypes();
        $expectedResult = [
            'key',
            'value<key2,value2>',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetOuterType()
    {
        $result = (new TypeDescriptionParser('array'))->parseOuterType();
        $expectedResult = 'array';
        $this->assertEquals($expectedResult, $result);

        $result = (new TypeDescriptionParser('array<string>'))->parseOuterType();
        $expectedResult = 'array';
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException \Mfn\ArgumentValidation\Exceptions\TypeDescriptionParserException
     */
    public function testParseOuterTypeException()
    {
        (new TypeDescriptionParser('<foo>'))->parseOuterType();
    }
}

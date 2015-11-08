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

use Mfn\ArgumentValidation\ExtractFromDocblock;
use Mfn\ArgumentValidation\Parameter;

class NaiveExtractFromDocblockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExtractFromDocblock
     */
    protected $extractor;

    public function setUp()
    {
        $this->extractor = new ExtractFromDocblock();
    }

    public function testSimple()
    {
        $docblock = '@var string $name';
        $parameters = $this->extractor->extract($docblock);
        $expectedParameters = [
            new Parameter('name', 'string'),
        ];
        $this->assertEquals($expectedParameters, $parameters);
    }

    public function testSimpleOptional()
    {
        $docblock = '?@var string $name';
        $parameters = $this->extractor->extract($docblock);
        $expectedParameters = [
            new Parameter('name', 'string', true),
        ];
        $this->assertEquals($expectedParameters, $parameters);
    }

    public function testInDocblock()
    {
        $docblock = <<<'DOCBLOCK'
/**
 * @var type $name
 * ?@var type2 $name2 Description
 */
DOCBLOCK;
        $parameters = $this->extractor->extract($docblock);
        $expectedParameters = [
            new Parameter('name', 'type'),
            new Parameter('name2', 'type2', true),
        ];
        $this->assertEquals($expectedParameters, $parameters);
    }

    public function testLeadingBackslashStays()
    {
        $docblock = <<<'DOCBLOCK'
/**
 * @var \type $name
 */
DOCBLOCK;
        $parameters = $this->extractor->extract($docblock);
        $expectedParameters = [
            new Parameter('name', '\type'),
        ];
        $this->assertEquals($expectedParameters, $parameters);
    }
}

<?php namespace Mfn\ArgumentValidation\Interfaces;

    /*
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

/**
 * Represent an eventual type description.
 *
 * "eventual" because the type description may as well be an empty string!
 */
interface TypeDescriptionParserInterface
{
    /**
     * Parses a type description which potentially contains multiple type
     * and returns a string array of the "outermost" type description.
     *
     * E.g. `int|string` can return `['int', 'string']`
     *
     * @return static[]
     */
    public function parseOuterTypes();

    /**
     * Expects the passed $typeDescription to represent a single type (but
     * possible a collection with "inner types").
     *
     * E.g. `array<int>` can return `array`
     *
     * @return static
     */
    public function parseOuterType();

    /**
     * Returns the inner type of collections.
     *
     * E.g. `array<resource|string>` may return `resource|string`
     *
     * @return static
     */
    public function parseInnerTypeDescription();

    /**
     * Collection types may represent a key/value relation.
     *
     * E.g. `array<string,int>` may return ['string', 'int'] and
     * `array<int>` may return `['', 'int']`
     *
     * I.e. the first element is always the key type (but may be
     * absent) and the second always the value type
     *
     * @return static[]
     */
    public function parseKeyValueTypes();

    /**
     * Whether the type description is empty
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * @return string
     */
    public function __toString();
}

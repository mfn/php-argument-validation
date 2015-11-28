<?php namespace Mfn\ArgumentValidation;

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

use Mfn\ArgumentValidation\Exceptions\TypeDescriptionParserException;
use Mfn\ArgumentValidation\Interfaces\TypeDescriptionParserInterface;

class TypeDescriptionParser implements TypeDescriptionParserInterface
{
    /**
     * @var string
     */
    protected $typeDescription;

    /**
     * @param string $typeDescription
     */
    public function __construct($typeDescription)
    {
        $this->typeDescription = $typeDescription;
    }

    /**
     * Implementation detail:
     * - the shorthand syntax of `type[]` is converted to `array<type>`.
     *
     * Current limitation:
     * - nested arrays with key/value types are not supported, i.e.
     *   `array<int,array<int,string>>` will not be properly parsed
     *
     * @return static[]
     */
    public function parseOuterTypes()
    {
        $splits = preg_split('/([^<|]+<[^>]+>)|\|/', $this->typeDescription, -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $types = array_map(
            function ($split) {

                # convert `type[]` into `array<type>`
                if (preg_match('/^([^[]+)\[\]$/', $split, $m)) {
                    $split = 'array<' . $m[1] . '>';
                }

                return new static($split);
            },
            $splits
        );

        return $types;
    }

    /**
     * @return static
     * @throws TypeDescriptionParserException
     */
    public function parseOuterType()
    {
        if (!preg_match('/^([^<]+)/', $this->typeDescription, $m)) {
            throw new TypeDescriptionParserException(
                "Unable to extract outer most type from '$this->typeDescription'"
            );
        }

        return new static($m[1]);
    }

    /**
     * @return static
     */
    public function parseInnerTypeDescription()
    {
        $inner = '';

        if (preg_match('/<([^>]+)>$/', $this->typeDescription, $m)) {
            $inner = $m[1];
        }

        return new static($inner);
    }

    /**
     * @return static[]
     */
    public function parseKeyValueTypes()
    {
        $key = '';
        $value = '';
        if (preg_match('/([^,]+)(?:,(.*)?)?/', $this->typeDescription, $m)) {
            if (isset($m[2])) {
                $key = $m[1];
                $value = $m[2];
            } else {
                $value = $m[1];
            }
        }

        return [new static($key), new static($value)];
    }

    /**
     * Whether the type description is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->typeDescription);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->typeDescription;
    }
}

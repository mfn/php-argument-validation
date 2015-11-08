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

use Mfn\ArgumentValidation\Interfaces\ParameterInterface;

class Parameter implements ParameterInterface
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $typeDescription;
    /**
     * @var bool
     */
    protected $optional;

    /**
     * @param string $name
     * @param string $typeDescription A type description. May be simple single
     *   types like `string`, alternatives like `string|int` or collections,
     *   etc. The actual supported flexibility of the type system depends on the
     *   `TypeDescriptionParserInterface` implementation.
     * @param bool $optional
     */
    public function __construct($name, $typeDescription, $optional = false)
    {
        $this->name = $name;
        $this->typeDescription = $typeDescription;
        $this->optional = $optional;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTypeDescription()
    {
        return $this->typeDescription;
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTypeDescription() . ' $' . $this->getName();
    }
}

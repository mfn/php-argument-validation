<?php namespace Mfn\ArgumentValidation\Types;

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

use Mfn\ArgumentValidation\Interfaces\TypeInterface;

abstract class AbstractType implements TypeInterface
{
    /**
     * For convenience. The majority of types won't have aliases.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Helper to get a useful description with details from scalar types as
     * well as resources and objects.
     *
     * @param mixed $mixed
     * @return string
     */
    protected function getPhpTypeDescription($mixed)
    {
        if (is_resource($mixed)) {
            return 'resource<' . get_resource_type($mixed) . '>';
        }

        if (is_object($mixed)) {
            return 'object<' . get_class($mixed) . '>';
        }

        return gettype($mixed);
    }
}

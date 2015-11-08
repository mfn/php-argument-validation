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

use Mfn\ArgumentValidation\Exceptions\ExtractException;
use Mfn\ArgumentValidation\Interfaces\ParameterInterface;

/**
 * Poor mans regex approach trying to extract parameter definitions from
 * a docblock.
 *
 * In fact, the docblock isn't verified at all, it just looks for a pattern
 * like:
 * @var type $name
 * or
 * @param type $name
 *
 * Optional types are supported via
 * ?@var
 * This syntax was chosen as not to break current IDE phpdoc type hinting.
 */
class ExtractFromDocblock
{
    /**
     * @param string $docblock
     * @return ParameterInterface[]
     * @throws ExtractException
     */
    public function extract($docblock)
    {
        $re = '/(?<decl>\??@(?:var|param))\s+(?<types>[^\s]+)\s+\$(?<names>[^\s]+)/';
        preg_match_all($re, $docblock, $m);

        if (empty($m)) {
            return [];
        }

        $parameters = [];

        $collectedNames = [];
        $duplicateDefinitions = [];

        foreach ($m['names'] as $index => $name) {
            $type = $m['types'][$index];
            $decl = $m['decl'][$index];

            if (in_array($name, $collectedNames)) {
                $duplicateDefinitions[] = $name;
                continue;
            }

            $isOptional = $decl{0} === '?';

            $collectedNames[] = $name;
            $parameters[] = new Parameter($name, $type, $isOptional);
        }

        if (!empty($duplicateDefinitions)) {
            $msg = 'Multiple definitions for: ' . join(', ',
                    $duplicateDefinitions);
            throw new ExtractException($msg);
        }

        return $parameters;
    }
}

<?php namespace Mfn\ArgumentValidation\Types\Complex;

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

use Mfn\ArgumentValidation\Exceptions\TypeError;
use Mfn\ArgumentValidation\Interfaces\TypeDescriptionParserInterface;
use Mfn\ArgumentValidation\Interfaces\TypeValidatorInterface;
use Mfn\ArgumentValidation\Types\AbstractType;

class ResourceType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'resource';
    }

    /**
     * @param TypeValidatorInterface $validator
     * @param TypeDescriptionParserInterface $innerTypeDescriptor
     * @param mixed $value
     * @throws TypeError
     */
    public function validate(
        TypeValidatorInterface $validator,
        TypeDescriptionParserInterface $innerTypeDescriptor,
        $value
    ) {
        if (!is_resource($value)) {
            throw new TypeError(
                'Expected resource but ' .
                $this->getPhpTypeDescription($value) . ' received');
        }

        if (!$innerTypeDescriptor->isEmpty()) {
            $resourceType = get_resource_type($value);
            $matched = false;

            foreach ($innerTypeDescriptor->parseOuterTypes() as $typeDescriptor) {
                if ($resourceType == $typeDescriptor) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                throw new TypeError(
                    "Expected resource of type $innerTypeDescriptor but " .
                    get_resource_type($value) . ' received');
            }
        }
    }
}

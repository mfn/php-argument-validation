<?php namespace Mfn\ArgumentValidation\Types\Array_;

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

use Mfn\ArgumentValidation\Exceptions\TypeError;
use Mfn\ArgumentValidation\Interfaces\TypeDescriptionParserInterface;
use Mfn\ArgumentValidation\Interfaces\TypeValidatorInterface;
use Mfn\ArgumentValidation\Types\AbstractType;

class ArrayType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'array';
    }

    /**
     * @param TypeValidatorInterface $validator
     * @param TypeDescriptionParserInterface $innerTypeDescriptor
     * @param mixed $values
     * @throws TypeError
     */
    public function validate(
        TypeValidatorInterface $validator,
        TypeDescriptionParserInterface $innerTypeDescriptor,
        $values
    ) {
        if (!is_array($values)) {
            throw new TypeError('Expected array but ' .
                $this->getPhpTypeDescription($values) . ' received');
        }

        if ($innerTypeDescriptor->isEmpty()) {
            return;
        }

        /** @var TypeDescriptionParserInterface $keyType */
        /** @var TypeDescriptionParserInterface $valueType */
        list($keyType, $valueType) = $innerTypeDescriptor->parseKeyValueTypes();

        $count = 0;
        foreach ($values as $key => $value) {
            # Handle key validation, if type is present
            if (!$keyType->isEmpty()) {
                try {
                    $validator->validateTypeDescriptor($keyType, $key);
                } catch (TypeError $e) {
                    throw new TypeError("Error in key #$count: " . $e->getMessage());
                }
            }
            # Handle value validation
            try {
                $validator->validateTypeDescriptor($valueType, $value);
            } catch (TypeError $e) {
                throw new TypeError(
                    "Error in value at key #$count ('$key'): " . $e->getMessage());
            }
            # Counter is only for puny human reporting
            $count++;
        }
    }
}

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

use Mfn\ArgumentValidation\Exceptions\ArgumentValidationException;
use Mfn\ArgumentValidation\Exceptions\TypeError;
use Mfn\ArgumentValidation\Interfaces\ArgumentValidationInterface;
use Mfn\ArgumentValidation\Interfaces\ParameterInterface;
use Mfn\ArgumentValidation\Interfaces\TypeDescriptionParserInterface;
use Mfn\ArgumentValidation\Interfaces\TypeInterface;
use Mfn\ArgumentValidation\Interfaces\TypeValidatorInterface;
use Mfn\ArgumentValidation\Types\Array_\ArrayType;
use Mfn\ArgumentValidation\Types\Complex\AnyType;
use Mfn\ArgumentValidation\Types\Complex\CallableType;
use Mfn\ArgumentValidation\Types\Complex\NumberType;
use Mfn\ArgumentValidation\Types\Complex\NumericType;
use Mfn\ArgumentValidation\Types\Complex\ObjectType;
use Mfn\ArgumentValidation\Types\Complex\ResourceType;
use Mfn\ArgumentValidation\Types\Scalar\BoolType;
use Mfn\ArgumentValidation\Types\Scalar\FloatType;
use Mfn\ArgumentValidation\Types\Scalar\IntegerType;
use Mfn\ArgumentValidation\Types\Scalar\StringType;

/**
 * The actual type checker implementation.
 *
 * Usage:
 * <code>
 *  $checker = new ArgumentValidation;
 *
 *  $parameters = [
 *      new Parameter('name', 'string')
 *  ];
 *  $arguments = [
 *      'name' => 'foobar',
 *  ];
 *
 *  $errors = $checker->validate($parameters, $arguments);
 *
 *  if (!empty($errors)) {
 *      # appropriately report the errors to the user
 *  }
 * </code>
 */
class ArgumentValidation implements ArgumentValidationInterface, TypeValidatorInterface
{
    use PhpTypeDescriptionTrait;

    /**
     * @var string
     */
    protected $parser;

    /**
     * @var TypeInterface[]
     */
    protected $types = [];

    /**
     * @var bool
     */
    protected $abortOnMissingDeclaration = true;

    /**
     * @param string $typeDescriptionParserClass
     * @param bool $abortOnMissingDeclaration By default (true), arguments
     *   without a type declaration will be reported as errors. Use false to
     *   disable this behaviour.
     */
    public function __construct(
        $typeDescriptionParserClass = TypeDescriptionParser::class,
        $abortOnMissingDeclaration = true
    ) {
        $this->registerDefaultTypes();

        $this->parser = $typeDescriptionParserClass;
        $this->abortOnMissingDeclaration = $abortOnMissingDeclaration;
    }

    /**
     * @param ParameterInterface[] $parameters
     * @param array $arguments
     * @return string[]
     */
    public function validate(array $parameters, array $arguments)
    {
        $parameters = $this->indexParametersByName($parameters);

        $errors = [];

        if ($this->abortOnMissingDeclaration) {
            $errors = $this->checkForTooManyParameters($parameters, $arguments);
        }

        $errors = array_merge(
            $errors,
            $this->validateParams($parameters, $arguments)
        );

        return $errors;
    }

    protected function registerDefaultTypes()
    {
        $this
            ->registerType(new AnyType())
            ->registerType(new ArrayType())
            ->registerType(new BoolType())
            ->registerType(new CallableType())
            ->registerType(new FloatType())
            ->registerType(new IntegerType())
            ->registerType(new NumberType())
            ->registerType(new NumericType())
            ->registerType(new ObjectType())
            ->registerType(new ResourceType())
            ->registerType(new StringType());

        return $this;
    }

    /**
     * Register a type to support for argument validation
     *
     * The type will be register under its name and all its provided aliases.
     *
     * @param TypeInterface $type
     * @return $this
     */
    public function registerType(TypeInterface $type)
    {
        $this->registerTypeAs($type, $type->getName());

        foreach ($type->getAliases() as $alias) {
            $this->registerTypeAs($type, $alias);
        }

        return $this;
    }

    /**
     * Register a type with a specific name, ignoring the built-in name and
     * aliases.
     *
     * @see registerType()
     * @param TypeInterface $type
     * @param string $name
     * @return $this
     */
    public function registerTypeAs(TypeInterface $type, $name)
    {
        if (!isset($this->types[$name])) {
            $this->types[$name] = [];
        }

        $this->types[$name][] = $type;

        return $this;
    }

    /**
     * @param ParameterInterface[] $parameters
     * @param array $arguments
     * @return string[]
     */
    protected function checkForTooManyParameters(
        array $parameters,
        array $arguments
    ) {
        $errors = [];

        $parameterNames = array_map(
            function (ParameterInterface $param) {
                return $param->getName();
            },
            $parameters
        );
        $argumentNames = array_keys($arguments);

        $tooMany = array_diff($argumentNames, $parameterNames);
        if (!empty($tooMany)) {
            foreach ($tooMany as $argumentName) {
                $errors[] =
                    'No parameter definition for argument $' . $argumentName .
                    ' of type ' . $this->getPhpTypeDescription($arguments[$argumentName]);

            }
        }

        return $errors;
    }

    /**
     * The core implementation for parameter/argument validation.
     *
     * @param ParameterInterface[] $parameters
     * @param array $arguments
     * @return string[]
     */
    protected function validateParams(array $parameters, array $arguments)
    {
        $errors = [];

        foreach ($parameters as $param) {
            $name = $param->getName();

            # If the parameter is required and no argument present -> error
            if (!array_key_exists($name, $arguments)) {
                if (!$param->isOptional()) {
                    $errors[] =
                        'Required argument $' . $name . ' of type ' .
                        $param->getTypeDescription() . ' is missing';
                }
                continue;
            }
            $value = $arguments[$name];

            /** @var TypeDescriptionParserInterface $typeDescriptor */
            $typeDescriptor = new $this->parser($param->getTypeDescription());
            $typeDescriptors = $typeDescriptor->parseOuterTypes();

            $errorsForThisParam = [];

            # Implementation detail: the TypeError exception is used to a
            # a logic flow (unfortunately). But it seemed easier at first
            # because type validation calls may be nested and bubbling up
            # the errors is easier that way.
            foreach ($typeDescriptors as $typeDescriptor) {
                try {
                    $this->validateTypeDescriptor($typeDescriptor, $value);
                } catch (TypeError $e) {
                    $errorsForThisParam[] =
                        'Argument $' . $name . " does not match type '$typeDescriptor': " . $e->getMessage();
                }
            }

            # We only keep the actual error for a single type; all others are
            # basically thrown away because we must tell the user that none
            # of the actual types validated
            if (1 === count($typeDescriptors)) {
                $errors = array_merge($errors, $errorsForThisParam);
            } else {
                if (count($typeDescriptors) === count($errorsForThisParam)) {
                    $errors[] = 'Parameter $' . $name . ': did not match type declaration "' . $param->getTypeDescription() . '"';
                }
            }
        }

        return $errors;
    }

    /**
     * @param string $type
     * @return TypeInterface[]|null
     */
    protected function getTypes($type)
    {
        if (!isset($this->types[$type])) {
            return null;
        }

        return $this->types[$type];
    }

    /**
     * Validate the `value` against the `typeDescription`.
     *
     * Note: this may delegate to the registered types `validate()` method
     * which receives an instance of this call to be able to also call this
     * method if necessary (for collection types).
     *
     * @param TypeDescriptionParserInterface $typeDescriptor
     * @param mixed $value
     * @throws TypeError
     */
    public function validateTypeDescriptor($typeDescriptor, $value)
    {
        $typeName = $typeDescriptor->parseOuterType()->__toString();
        $types = $this->getTypes($typeName);
        $innerTypeDescriptor = $typeDescriptor->parseInnerTypeDescription();

        if (null === $types) {
            $this->validateClassInstance($typeName, $innerTypeDescriptor,
                $value);
        } else {
            $exceptions = [];

            foreach ($types as $type) {
                try {
                    $type->validate($this, $innerTypeDescriptor, $value);
                } catch (TypeError $e) {
                    $exceptions[] = $e;
                }
            }

            if (count($exceptions) === count($types)) {
                # if all type validations failed, throw the first
                throw reset($exceptions);
            }
        }
    }

    /**
     * Ensures the array of parameters is indexed by their name.
     *
     * @param ParameterInterface[] $parameters
     * @return array
     * @throws ArgumentValidationException
     */
    protected function indexParametersByName(array $parameters)
    {
        $tmp = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (isset($tmp[$name])) {
                throw new ArgumentValidationException(
                    'Duplicate parameter definition for $' . $name);
            }

            $tmp[$parameter->getName()] = $parameter;
        }

        return $tmp;
    }

    /**
     * Validating a class instance cannot be represented with a distinct type
     * thus we provide this method.
     *
     * @param string $className
     * @param TypeDescriptionParserInterface $innerTypeDescriptor
     * @param mixed $value
     * @throws TypeError
     */
    protected function validateClassInstance(
        $className,
        $innerTypeDescriptor,
        $value
    ) {
        if (!$innerTypeDescriptor->isEmpty()) {
            throw new TypeError(
                "Cannot handle $className<$innerTypeDescriptor> in a generic way, " .
                "this requires a type implementation for $className"
            );
        }

        if (!is_object($value)) {
            throw new TypeError(
                "Expected instance of $className but received " . gettype($value));
        }

        if (!($value instanceof $className)) {
            throw new TypeError(
                get_class($value) . " not an instance of $className");
        }
    }
}

<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Validation;

use Hazelnut\Exception\Validation\ValidationException;
use ReflectionClass;

class Validator
{
    /**
     * @throws ValidationException
     */
    public function validate(object $dto): void
    {
        $reflector = new ReflectionClass($dto);
        $errors = [];

        foreach ($reflector->getProperties() as $property) {
            $propertyName = $property->getName();
            $propertyValue = $property->isInitialized($dto) ? $property->getValue($dto) : null;

            foreach ($property->getAttributes() as $attribute) {
                $attributeInstance = $attribute->newInstance();

                if ($attributeInstance instanceof ValidatorInterface) {
                    $errorMessage = $attributeInstance->validate($propertyName, $propertyValue);

                    if ($errorMessage !== null) {
                        $errors[$propertyName][] = $errorMessage;
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errorMessage, $errors);
        }
    }
}

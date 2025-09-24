<?php
declare(strict_types=1);

namespace Egg\Validation;

use Exception;
use ReflectionClass;

class Validator
{
    public function validate(object $dto): void
    {
        $reflection = new ReflectionClass($dto);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes();
            
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                
                if (method_exists($attributeInstance, 'validate')) {
                    $attributeInstance->validate($property->getName(), $property->getValue($dto));
                }
            }
        }
    }
}

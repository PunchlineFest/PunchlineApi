<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyAccess\PropertyAccess;

class BaseController extends AbstractController
{
    public function setProperties($entity, $data): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        $reflectionClass = new \ReflectionClass($entity);

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                if ($reflectionClass->hasProperty($key)) {
                    $property = $reflectionClass->getProperty($key);
                    $propertyType = $property->getType();

                    if ($propertyType && $propertyType->getName() === \DateTimeInterface::class) {
                        $value = new \DateTime($value);
                    }
                }

                $propertyAccessor->setValue($entity, $key, $value);
            }
        }
    }
}
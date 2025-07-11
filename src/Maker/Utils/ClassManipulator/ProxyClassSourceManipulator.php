<?php
declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Utils\ClassManipulator;

use Exception;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;

final class ProxyClassSourceManipulator
{
    public function __construct(private ClassSourceManipulator $classSourceManipulator)
    {}

    public function __call(string $name, $arguments)
    {
        try {
            $reflectionClass = new ReflectionClass(ClassSourceManipulator::class);
            
            $reflectionMethod = $reflectionClass->getMethod($name);

            if (!$reflectionMethod->isPublic()) {
                $reflectionMethod->setAccessible(true);
            }

            $reflectionMethod->invoke($this->classSourceManipulator, $arguments);

            if (!$reflectionMethod->isPublic()) {
                $reflectionMethod->setAccessible(false);
            }
        } catch (ReflectionException $reflectionException) {
            throw new Exception(sprintf('Unknown method %s', $name), 500);
        } catch (\Throwable $th) {
            throw new Exception(sprintf('An error occured during the call to the method %s', $name), 500);
        }
        
    }
}
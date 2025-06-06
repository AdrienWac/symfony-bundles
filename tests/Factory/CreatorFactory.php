<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\Creator;
use Symfony\Bundle\MakerBundle\FileManager;

class CreatorFactory implements FactoryInterface
{
    public static function create(FileManager $fileManager): Creator {
        return new Creator($fileManager);
    }
    
}
<?php 

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Traits;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;

trait UseCaseFileTrait
{
    public static function createUseCaseFile(
        
    ): UseCaseFile
    {
        return new UseCaseFile();
    }
}
<?php
namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ClassFile;

interface CreatorInterface 
{
    public function generate(): void;

    public function generateUseCase( string $name, string $folderPath, string $domainPath ): void;

    public function addOperation(ClassFile $classFile): void;

    public function writeChanges(): void;
}
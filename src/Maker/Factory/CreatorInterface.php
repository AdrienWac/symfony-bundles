<?php
namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;

interface CreatorInterface 
{
    public function generateUseCase( string $name, string $folderPath, string $domainPath ): void;

    public function generateResponse( string $name, string $folderPath, string $domainPath ): void;

    public function generatePresenterInterface( string $name, string $folderPath, string $domainPath ): void;

    public function generateRequest( string $name, string $folderPath, string $domainPath ): RequestFile;

    public function writeChanges(): void;
}
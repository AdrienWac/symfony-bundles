<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\CreatorInterface;

final class Creator implements CreatorInterface
{
    public function generateUseCase(
        string $name,
        string $folderPath
    ): void
    {
        $useCase = new UseCaseFile();
    }


    public function generate(): void
    {

    }

    public function addOperation(): void
    {

    }

    public function writeChanges(): void
    {

    }
}
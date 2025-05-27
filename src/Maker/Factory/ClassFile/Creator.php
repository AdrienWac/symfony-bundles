<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\CreatorInterface;

final class Creator implements CreatorInterface
{
    private array $operations = [];

    public function generateUseCase(
        string $name,
        string $folderPath
    ): void
    {
        $requestFile = new RequestFile();
        $presenterInterfaceFile = new PresenterInterfaceFile();
        $useCaseFile = new UseCaseFile();

        $this->addOperation($useCaseFile);
    }


    public function generate(): void
    {

    }

    public function addOperation(ClassFile $classFile): void
    {
        $operations[] = $classFile;
    }

    public function writeChanges(): void
    {

    }
}
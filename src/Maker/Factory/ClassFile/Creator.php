<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\CreatorInterface;
use Symfony\Bundle\MakerBundle\FileManager;

final class Creator implements CreatorInterface
{
    private array $operations = [];

    private array $elements = [];

    public function __construct(private FileManager $fileManager)
    {}

    public function generateUseCase(
        string $name,
        string $folderPath,
        string $domainPath
    ): void
    {
        $requestFile = new RequestFile($domainPath, $folderPath, $name);

        $presenterInterfaceFile = new PresenterInterfaceFile($domainPath, $folderPath, $name);

        $useCaseFile = new UseCaseFile($domainPath, $folderPath, $name, $requestFile, $presenterInterfaceFile);

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
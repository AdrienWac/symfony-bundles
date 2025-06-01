<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\CreatorInterface;
use Symfony\Bundle\MakerBundle\FileManager;

final class Creator implements CreatorInterface
{
    private array $operationsList = [];

    private array $elementsList = [];

    public function __construct(private FileManager $fileManager)
    {}

    public function generateUseCase(
        string $name,
        string $folderPath,
        string $domainPath
    ): void
    {
        $requestFile = $this->buildRequestFile($domainPath, $folderPath, $name);

        $responseFile = $this->buildResponseFile($domainPath, $folderPath, $name);

        $presenterInterfaceFile = $this->buildPresenterInterfaceFile($domainPath, $folderPath, $name, $responseFile);

        $useCaseFile = $this->buildUseCaseFile($domainPath, $folderPath, $name, $requestFile, $presenterInterfaceFile);

        $this->addOperation($useCaseFile);
    }

    private function buildRequestFile(
        string $name,
        string $folderPath,
        string $domainPath
    ): RequestFile
    {
        $requestFile = new RequestFile($domainPath, $folderPath, $name);

        $this->elementsList[RequestFile::class] = $requestFile;

        return $requestFile;
    }

    private function buildResponseFile(
        string $name,
        string $folderPath,
        string $domainPath
    ): ResponseFile
    {
        $responseFile = new ResponseFile($domainPath, $folderPath, $name);

        $this->elementsList[ResponseFile::class] = $responseFile;

        return $responseFile;
    }

    private function buildPresenterInterfaceFile(
        string $name,
        string $folderPath,
        string $domainPath,
        ResponseFile $responseFile
    ): PresenterInterfaceFile
    {
        $presenterInterfaceFile = new PresenterInterfaceFile($domainPath, $folderPath, $name, $responseFile);

        $this->elementsList[PresenterInterfaceFile::class] = $presenterInterfaceFile;

        return $presenterInterfaceFile;
    }

    private function buildUseCaseFile(
        string $name,
        string $folderPath,
        string $domainPath,
        RequestFile $requestFile, 
        PresenterInterfaceFile $presenterInterfaceFile
    ): UseCaseFile
    {
        $useCaseFile = new UseCaseFile($domainPath, $folderPath, $name, $requestFile, $presenterInterfaceFile);

        $this->elementsList[UseCaseFile::class] = $presenterInterfaceFile;

        return $useCaseFile;
    }

    public function generate(): void
    {

    }

    public function addOperation(ClassFile $classFile): void
    {
        $this->operationsList[] = $classFile;
    }

    public function writeChanges(): void
    {
        foreach ($this->operationsList as $key => $operation) {
            # code...
        }
        
        $this->resetList();
    }

    private function resetList(): void
    {
        $this->elementsList = [];
        $this->operationsList = [];
    }
}
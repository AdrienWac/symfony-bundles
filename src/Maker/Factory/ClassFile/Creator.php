<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\CreatorInterface;
use Symfony\Bundle\MakerBundle\FileManager;

final class Creator implements CreatorInterface
{
    private array $operationsList = [];

    private array $elementsList = [];

    public const TEMPLATE_FOLDER_PATH = 'templates';

    public function __construct(private FileManager $fileManager)
    {}

    public function getOperationsList(): array
    {
        return $this->operationsList;
    }

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
        string $domainPath,
        string $folderPath,
        string $name
    ): RequestFile
    {
        $requestFile = new RequestFile($domainPath, $folderPath, $name);

        $this->setTargetPath($requestFile);

        $this->elementsList[RequestFile::class] = $requestFile;

        return $requestFile;
    }

    private function buildResponseFile(
        string $domainPath,
        string $folderPath,
        string $name
    ): ResponseFile
    {
        $responseFile = new ResponseFile($domainPath, $folderPath, $name);

        $this->setTargetPath($responseFile);

        $this->elementsList[ResponseFile::class] = $responseFile;

        return $responseFile;
    }

    private function buildPresenterInterfaceFile(
        string $domainPath,
        string $folderPath,
        string $name,
        ResponseFile $responseFile
    ): PresenterInterfaceFile
    {
        $presenterInterfaceFile = new PresenterInterfaceFile($domainPath, $folderPath, $name, $responseFile);

        $this->setTargetPath($presenterInterfaceFile);

        $this->elementsList[PresenterInterfaceFile::class] = $presenterInterfaceFile;

        return $presenterInterfaceFile;
    }

    private function buildUseCaseFile(
        string $domainPath,
        string $folderPath,
        string $name,
        RequestFile $requestFile, 
        PresenterInterfaceFile $presenterInterfaceFile
    ): UseCaseFile
    {
        $useCaseFile = new UseCaseFile($domainPath, $folderPath, $name, $requestFile, $presenterInterfaceFile);

        $this->setTargetPath($useCaseFile);

        $this->elementsList[UseCaseFile::class] = $presenterInterfaceFile;

        return $useCaseFile;
    }

    /**
     * Edit targetPath attribute value of ClasseFile instance
     * with FileManager method.
     *
     * @todo Use event dispatch in ClassFile constructor to do that 
     * @param ClassFile $classFile
     * @return void
     */
    private function setTargetPath(ClassFile $classFile): void
    {
        $targetPath = $this->fileManager->getRelativePathForFutureClass($classFile->getFullClassName());

        $classFile->setTargetPath($targetPath);
    }

    public function generate(): void
    {

    }

    public function addOperation(ClassFile $classFile): void
    {
        if ($this->fileManager->fileExists($classFile->getTargetPath())) {
            throw new \Exception(
                \sprintf(
                    'The file "%s" can\'t be generated because it already exists.',
                    $this->fileManager->relativizePath($classFile->getTargetPath())
                )
            );
        }

        $this->operationsList[] = $classFile;
    }

    public function writeChanges(): void
    {
        foreach ($this->operationsList as $classFile) {
            $this->fileManager->dumpFile(
                $classFile->getTargetPath(),
                $this->getFileContentsForOperation($classFile)
            );
        }
        
        $this->resetList();
    }

    /**
     * Get content of template with hydrated template variables
     *
     * @param ClassFile $classFile
     * @return string
     */
    private function getFileContentsForOperation(ClassFile $classFile): string
    {
        $parameters = $classFile->getTemplateVariables();

        $templateParameters = array_merge($parameters, [
            'relative_path' => $this->fileManager->relativizePath($classFile->getTargetPath())
        ]);

        return $this->fileManager->parseTemplate(
            $classFile->getFullTemplatePath(), 
            $templateParameters
        );
    }

    private function resetList(): void
    {
        $this->elementsList = [];
        $this->operationsList = [];
    }
}
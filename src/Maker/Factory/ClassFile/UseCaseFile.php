<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ClassFile;

final class UseCaseFile extends ClassFile
{
    public const FOLDER_NAME = 'UseCase';

    public const TEMPLATE_PATH = '/UseCase.tpl.php';

    public function __construct(
        private readonly string $domainFolderPath,
        private readonly string $folderPath,
        private readonly string $useCaseName,
        private readonly RequestFile $requestFile,
        private readonly PresenterInterfaceFile $presenterInterfaceFile
    ) {
        parent::__construct(
            $domainFolderPath,
            $folderPath,
            $useCaseName
        );
    }

    protected function getFolderName(): string
    {
        return self::FOLDER_NAME;
    }

    protected function getTemplatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    public function buildUseStatementArray(): array
    {
        return [
            $this->requestFile->getFullClassName(),
            $this->presenterInterfaceFile->getFullClassName()
        ];
    }

    public function getClassName(): string 
    {
        return $this->useCaseName;
    }
}
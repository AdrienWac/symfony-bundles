<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ClassFile;

final class UseCaseFile extends ClassFile
{
    private const FOLDER_NAME = 'UseCase'; 

    private string $nameSpace;

    public function __construct(
        private readonly $domainFolderPath,
        private string $folderPath,
        private string $useCaseName
    ) {
        $this->nameSpace = $this->buildNameSpace(
            $domainFolderPath, 
            $folderPath, 
            $useCaseName
        );
    }
    
    private function buildNameSpace(
        string $domainFolderPath,
        string $useCaseFolderPath,
        string $useCaseName
    ): string
    {
        return sprintf(
            '%s\\%s\\%s\\%s',
            $domainFolderPath,
            self::FOLDER_NAME,
            str_replace('/', '\\', $useCaseFolderPath),
            $useCaseName
        );
    }

    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }
}
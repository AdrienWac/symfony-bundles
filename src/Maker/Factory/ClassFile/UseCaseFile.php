<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ClassFile;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class UseCaseFile extends ClassFile
{
    public const FOLDER_NAME = 'UseCase'; 

    public function __construct(
        private readonly string $domainFolderPath,
        private string $folderPath,
        private string $useCaseName,
        private readonly RequestFile $requestFile,
        // private readonly PresenterInterfaceFile $presenterInterfaceFile
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

    public function buildUseStatement(): UseStatementGenerator
    {
        return new UseStatementGenerator([
            $this->requestFile->getNameSpace()
        ]);
    }
}
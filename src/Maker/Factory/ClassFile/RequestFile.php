<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class RequestFile extends ClassFile
{
    public const FOLDER_NAME = 'UseCase'; 

    public function __construct(
        private readonly string $domainFolderPath,
        private string $folderPath,
        private string $useCaseName,
    ) {
        $this->nameSpace = $this->buildNameSpace(
            $domainFolderPath, 
            $folderPath, 
            $useCaseName
        );
    }

    public function buildUseStatement(): UseStatementGenerator
    {
        return new UseStatementGenerator([]);
    }
}
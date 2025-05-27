<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class PresenterInterfaceFile extends ClassFile
{
    public const FOLDER_NAME = 'API';

    public function __construct(
        private readonly string $domainFolderPath,
        private string $folderPath,
        private string $useCaseName,
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
        return new UseStatementGenerator([]);
    }
}
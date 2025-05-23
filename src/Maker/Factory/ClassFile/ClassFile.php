<?php
declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

abstract class ClassFile implements ClassFileInterface
{
    public const FOLDER_NAME = ''; 

    protected string $nameSpace;

    abstract public function buildUseStatement(): UseStatementGenerator;

    protected function buildNameSpace(
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
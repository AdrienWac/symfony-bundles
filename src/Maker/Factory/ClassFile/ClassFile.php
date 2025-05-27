<?php
declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

abstract class ClassFile implements ClassFileInterface
{
    protected string $nameSpace;

    abstract public function buildUseStatement(): UseStatementGenerator;

    abstract protected function getFolderName(): string;

    protected function buildNameSpace(
        string $domainFolderPath,
        string $useCaseFolderPath,
        string $useCaseName
    ): string
    {
        return sprintf(
            '%s\\%s\\%s\\%s',
            $domainFolderPath,
            $this->getFolderName(),
            str_replace('/', '\\', $useCaseFolderPath),
            $useCaseName
        );
    }

    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }
}
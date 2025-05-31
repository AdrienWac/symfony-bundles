<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class PresenterInterfaceFile extends ClassFile
{
    public const FOLDER_NAME = 'API';

    public const TEMPLATE_PATH = '/PresenterInterface.tpl.php';

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

    protected function buildFullClassName (
        $domainFolderPath,
        $classFileFolderPathFromUser
    ): string 
    {
        return sprintf(
            '%s\\%s\\%s',
            $domainFolderPath,
            $this->getFolderName(),
            $this->getClassName()
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

    public function getClassName(): string 
    {
        return 'PresenterInterface';
    }

    public function buildUseStatementArray(): array
    {
        return [];
    }
}
<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

final class RequestFile extends ClassFile
{
    public const FOLDER_NAME = 'Request'; 

    public const SUFFIX_FILE = 'Request';

    public const TEMPLATE_PATH = '/Request.tpl.php';

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

    public function getClassName(): string 
    {
        return sprintf('%s%s', $this->useCaseName, self::SUFFIX_FILE);
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
        return [];
    }
}
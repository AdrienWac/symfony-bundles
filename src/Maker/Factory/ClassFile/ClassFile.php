<?php
declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Utils\Str\StrMakerBundleAdapter;
use Symfony\Bundle\MakerBundle\FileManager;

abstract class ClassFile implements ClassFileInterface
{
    protected string $nameSpace;

    protected string $fullClassName;

    protected string $shortClassName;

    protected array $useStatementArray;

    protected string $targetPath;

    public function __construct(
        private readonly string $domainFolderPath,
        private string $folderPath,
        private string $classFileName
    ) 
    {
        $this->checkTemplateExist();

        $this->fullClassName = $this->buildFullClassName(
            $domainFolderPath,
            $folderPath
        );

        $this->nameSpace = $this->buildNameSpace($this->fullClassName);

        $this->useStatementArray = $this->buildUseStatementArray();
    }

    abstract public function buildUseStatementArray(): array;

    abstract protected function getFolderName(): string;

    abstract protected function getTemplatePath(): string;

    abstract public function getClassName(): string;

    abstract public function getTemplateVariables(): array;

    abstract public static function getUserQuestion(): string;

    /**
     * @return string
     * @example
     * - Domain folder path => Domain/
     * - FileClass name => UseCase/
     * - File parent folder path => Folder/SubFolder/
     * - File name => FileA
     * - Result => Domain\UseCase\Folder\SubFolder\FileA
     */
    protected function buildFullClassName (
        $domainFolderPath,
        $classFileFolderPathFromUser
    ): string 
    {
        return sprintf(
            '%s\\%s\\%s\\%s',
            $domainFolderPath,
            $this->getFolderName(),
            str_replace('/', '\\', $classFileFolderPathFromUser),
            $this->getClassName()
        );
    }

    protected function buildNameSpace(string $fullClassName): string
    {
        return StrMakerBundleAdapter::getNamespace($fullClassName);
    }

    public function getFullClassName(): string
    {
        return $this->fullClassName;
    }

    public function getShortClassName(): string
    {
        return $this->classFileName;
    }

    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }

    public function getUseStatementArray(): array
    {
        return $this->useStatementArray;
    }

    public function setTargetPath(string $targetPath): void
    {
        $this->targetPath = $targetPath;
    }

    public function getTargetPath(): string
    {
        return $this->targetPath;
    }

    public function getFullTemplatePath(): string
    {
        return \sprintf(
            '%s/%s/%s/%s/%s', 
            $_SERVER["WORKDIR_PATH"], 
            'src',
            'Maker',
            Creator::TEMPLATE_FOLDER_PATH, 
            $this->getTemplatePath()
        );
    }

    /**
     * Check if template of ClassFile instance exist
     * @throws Exception If template not exist
     */
    private function checkTemplateExist(): void
    {
        $templatePath = $this->getFullTemplatePath();

        if (!file_exists($templatePath)) {
            throw new \Exception(\sprintf('Cannot find template "%s" in the templates/ dir.', $templatePath));
        }
    }
}
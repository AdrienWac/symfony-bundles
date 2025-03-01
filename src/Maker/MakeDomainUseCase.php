<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker;

use AdrienLbt\HexagonalMakerBundle\Maker\MakeTrait;
use Symfony\Bundle\MakerBundle\Maker\Common\UidTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class MakeDomainUseCase extends AbstractMaker
{
    use UidTrait;
    use MakeTrait;

    public function __construct(
        private string $applicationPath,
        private string $domainPath,
        private string $infrastructurePath
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:domain:usecase';
    }

    public static function getCommandDescription(): string
    {
        return 'Create or update a Domain use case class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'folder_path',
                InputArgument::OPTIONAL,
                \sprintf('Folder path in use case parent folder (e.g. <fg=yellow>%s</>)',
                Str::asClassName(Str::getRandomTerm()))
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                \sprintf('Class name of the use to create or update (e.g. <fg=yellow>%s</>)',
                Str::asClassName(Str::getRandomTerm()))
            );
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $useCaseName = $input->getArgument('name');
        $useCaseFolderPath = $this->sanitizeFolderPath(
            $input->getArgument('folder_path')
        );

        $useCaseNameSpace = "Domain\\UseCase\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName;
        $requestNameSpace = "Domain\\Request\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName."Request";

        $entityClassDetails = $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: 'Domain\\UseCase\\',
            suffix: 'UseCase'
        );

        $useStatements = new UseStatementGenerator([
            $requestNameSpace,
            // @todo Gérer via la configuration
            'Domain\\API\\PresenterInterface'
        ]);

        $entityPath = $generator->generateClass(
            className: $useCaseNameSpace,
            templateName: __DIR__ .  '/templates/UseCase.tpl.php',
            variables: [
                "use_statements" => $useStatements,
            ]
        );

        $generator->writeChanges();
    }

    /**
     * Sanitize use case folder path
     * - Remove "/" at the end of file if present 
     *
     * @param string $useCaseFolderPath
     * @return string Content of $useCaseFolderPath sanitize
     */
    private function sanitizeFolderPath(string $useCaseFolderPath): string
    {
        if (mb_substr($useCaseFolderPath, -1) === '/') {
            return mb_substr($useCaseFolderPath, 0, -1);
        }
        return $useCaseFolderPath;
    }

    public function configureDependencies(DependencyBuilder $dependencies, ?InputInterface $input = null): void
    {
    }
}
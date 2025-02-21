<?php

namespace AdrienLbt\HexagonalMakerBundle\Command;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class MakeDomainUseCase extends AbstractMaker
{
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
        $useCaseFolderPath = $input->getArgument('folder_path');

        $useCaseFolderPath = $this->domainPath . '/' . $useCaseFolderPath;
        $useCaseFilePath = $useCaseFolderPath . '/' . $useCaseName . '.php';
        $io->writeln(
            'Creating or updating the use case class: <info>' . $useCaseName . ' at '. $useCaseFilePath .'</info>'
        );
    }

    public function configureDependencies(DependencyBuilder $dependencies, ?InputInterface $input = null): void
    {
    }
}
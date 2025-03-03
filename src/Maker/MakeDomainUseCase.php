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

        $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: 'Domain\\UseCase\\',
            suffix: 'UseCase'
        );

        $useStatements = new UseStatementGenerator([
            $requestNameSpace,
            // @todo Gérer via la configuration
            'Domain\\API\\PresenterInterface'
        ]);

        $generator->generateClass(
            className: $useCaseNameSpace,
            templateName: __DIR__ .  '/templates/UseCase.tpl.php',
            variables: [
                "use_statements" => $useStatements,
            ]
        );

        $generator->writeChanges();

        $this->askForCreateResponseFile(
            $useCaseName,
            $useCaseFolderPath,
            $input,
            $io,
            $generator
        );

        $this->askForCreatePresenterInterfaceFile(
            $useCaseName,
            $useCaseFolderPath,
            $input,
            $io,
            $generator
        );

        $this->askForCreateRequestFile(
            $useCaseName,
            $useCaseFolderPath,
            $input,
            $io,
            $generator
        );

        $this->writeSuccessMessage($io);
    }

    /**
     * @todo Ajouter la liaison avec une entité du domaine (Question + choix + ajout de la propriété dans la classe)
     * Create response file
     *
     * @param string $useCaseName
     * @param string $useCaseFolderPath
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     * @return void
     */
    private function askForCreateResponseFile(
        string $useCaseName,
        string $useCaseFolderPath,
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator
    ): void
    {
        $fullPathFile = 'Domain/Response/'.$useCaseFolderPath.'/'.$useCaseName.'Response.php';

        $createFile = $io->confirm(
            'Would you like to create the response file ('.$fullPathFile.') for use case '.$useCaseName.' ?', 
            true
        );

        if (!$createFile) {
            return;
        }

        $responseNameSpace = "Domain\\Response\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName."Response";

        $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: 'Domain\\Response\\',
            suffix: 'Response'
        );

        $useStatements = new UseStatementGenerator([]);

        $generator->generateClass(
            $responseNameSpace,
            __DIR__ .  '/templates/Response.tpl.php',
            [
                "use_statements" => $useStatements,
            ]
        );

        $generator->writeChanges();
    }

    /**
     * Create presenter interface file
     *
     * @param string $useCaseName
     * @param string $useCaseFolderPath
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     * @return void
     */
    private function askForCreatePresenterInterfaceFile(
        string $useCaseName,
        string $useCaseFolderPath,
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator
    ): void
    {
        $fullPathFile = 'Domain/API/'.$useCaseFolderPath.'/'.$useCaseName.'PresenterInterface.php';

        $createFile = $io->confirm(
            'Would you like to create the presenter interface file ('.$fullPathFile.') for use case '.$useCaseName.' ?', 
            true
        );

        if (!$createFile) {
            return;
        }

        $presenterInterfaceNameSpace = "Domain\\API\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName."PresenterInterface";
        $responseNameSpace = "Domain\\Response\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName."Response";

        $presenterInterfaceClassDetails = $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: 'Domain\\API\\',
            suffix: 'PresenterInterface'
        );

        $useStatements = new UseStatementGenerator([
            $responseNameSpace
        ]);

        $presenterInterfacePath = $generator->generateClass(
            className: $presenterInterfaceNameSpace,
            templateName: __DIR__ .  '/templates/PresenterInterface.tpl.php',
            variables: [
                "use_statements" => $useStatements,
                "use_case_name" => $useCaseName
            ]
        );

        $generator->writeChanges();
    }

    /**
     * @todo => Déplacer, après la création du fichier de request, la Constructor Property Promotion 
     * de la request dans le use case
     * Create Domain Request file
     *
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     * @return void
     */
    private function askForCreateRequestFile(
        string $useCaseName,
        string $useCaseFolderPath,
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator
    ): void
    {
        $fullPathFile = 'Domain/Request/'.$useCaseFolderPath.'/'.$useCaseName.'Request.php';

        $createFile = $io->confirm(
            'Would you like to create the request file ('.$fullPathFile.') for use case '.$useCaseName.' ?', 
            true
        );

        if (!$createFile) {
            return;
        }

        $requestNameSpace = "Domain\\Request\\".str_replace("/", "\\", $useCaseFolderPath)."\\".$useCaseName."Request";

        $requestClassDetails = $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: 'Domain\\Request\\',
            suffix: 'Request'
        );

        $useStatements = new UseStatementGenerator([]);

        $requestFilePath = $generator->generateClass(
            className: $requestNameSpace,
            templateName:__DIR__ .  '/templates/Request.tpl.php',
            variables:[
                "use_statements" => $useStatements,
            ]
        );

        $generator->writeChanges();

        $currentFields = $this->getPropertyNames($requestClassDetails->getFullName());
        $manipulator = $this->createClassManipulator($requestFilePath, $io, false);

        $isFirstField = true;

        // @todo Récupérer les chemins de manière dynamique
        $entityDomainTypes = self::getDomainEntityTypes(
            basePath: 'var/www/html',
            domainEntityDirectoryPath: 'src/Domain/Entity'
        );

        while (true) {
            $newField = $this->askForNextField(
                $io, 
                $currentFields, 
                $requestClassDetails->getFullName(), 
                $isFirstField, 
                otherValidTypes: $entityDomainTypes
            );

            $isFirstField = false;

            if (null === $newField) {
                break;
            }

            $fileManagerOperations = [];
            $fileManagerOperations[$requestFilePath] = $manipulator;

            $manipulator->addClassProperty(
                mapping: $newField, 
                withConstructorPropertyPromotion: true
            );
            $currentFields[] = $newField->propertyName;

            foreach ($fileManagerOperations as $path => $manipulatorOrMessage) {
                if (\is_string($manipulatorOrMessage)) {     /* @phpstan-ignore-line - https://github.com/symfony/maker-bundle/issues/1509 */
                    $io->comment($manipulatorOrMessage);
                } else {
                    $this->dumpFile($path, $manipulatorOrMessage->getSourceCode(), $io);
                }
            }
        }
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
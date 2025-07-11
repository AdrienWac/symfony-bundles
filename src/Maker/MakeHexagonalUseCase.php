<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\Creator;
use AdrienLbt\HexagonalMakerBundle\Maker\Handler\PresenterInterfaceHandler;
use AdrienLbt\HexagonalMakerBundle\Maker\Handler\RequestHandler;
use AdrienLbt\HexagonalMakerBundle\Maker\Handler\ResponseHandler;
use AdrienLbt\HexagonalMakerBundle\Maker\Handler\UseCaseHandler;
use AdrienLbt\HexagonalMakerBundle\Maker\MakeTrait;
use Symfony\Bundle\MakerBundle\Maker\Common\UidTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class MakeHexagonalUseCase extends AbstractMaker
{
    use UidTrait;
    use MakeTrait;

    public function __construct(
        private string $domainPath, 
        private FileManager $fileManager
    )
    {
    }

    public static function getCommandName(): string
    {
        return 'make:hexagonal:usecase';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a Domain use case class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'folder_path',
                InputArgument::OPTIONAL,
                \sprintf(
                    'Folder path in use case parent folder (e.g. <fg=yellow>%s</>)',
                    Str::asClassName(Str::getRandomTerm())
                )
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                \sprintf(
                    'Class name of the use to create or update (e.g. <fg=yellow>%s</>)',
                    Str::asClassName(Str::getRandomTerm())
                )
            );
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {   
        $useCaseName = $input->getArgument('name');
        
        $useCaseFolderPath = $this->sanitizeFolderPath(
            $input->getArgument('folder_path')
        );

        $creator = new Creator($this->fileManager);
        
        $useCaseHandler = new UseCaseHandler($creator, $io);
        $responseHandler = new ResponseHandler($creator, $io);
        $presenterInterfaceHandler = new PresenterInterfaceHandler($creator, $io);
        $requestHandler = new RequestHandler($creator, $io);

        $useCaseHandler->setNext($responseHandler);
        $responseHandler->setNext($presenterInterfaceHandler);
        $presenterInterfaceHandler->setNext($requestHandler);

        // @todo Improve creation of handler request
        $useCaseHandler->handleRequest([
            'useCaseName' => $useCaseName,
            'useCaseFolderPath' => $useCaseFolderPath,
            'domainPath' => $this->domainPath
        ]);

        $creator->writeChanges();

        $this->writeSuccessMessage($io);
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
    ): void {
        $fullPathFile = sprintf(
            '%s/Request/%s/%sRequest.php',
            $this->domainPath,
            $useCaseFolderPath,
            $useCaseName
        );

        $createFile = $io->confirm(
            'Would you like to create the request file (' . $fullPathFile . ') for use case ' . $useCaseName . ' ?',
            true
        );

        if (!$createFile) {
            return;
        }

        $requestNameSpace = sprintf(
            '%s\\Request\\%s\\%sRequest',
            $this->domainPath,
            str_replace('/', '\\', $useCaseFolderPath),
            $useCaseName
        );

        $nameSpacePrefix = sprintf(
            '%s\\Request\\',
            $this->domainPath
        );

        $requestClassDetails = $generator->createClassNameDetails(
            name: $useCaseName,
            namespacePrefix: $nameSpacePrefix,
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

        $domainEntityDirectoryPath = sprintf(
            'src/%s/Entity/',
            $this->domainPath
        );

        $entityDomainTypes = self::getDomainEntityTypes(
            domainEntityDirectoryPath: $domainEntityDirectoryPath
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

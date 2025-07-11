<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;
use AdrienLbt\HexagonalMakerBundle\Maker\MakeTrait;

final class RequestHandler extends AbstractHandler
{
    use MakeTrait;

    public function handleRequest(mixed $request): void
    {
        $createRequest = $this->io->confirm(
            RequestFile::getUserQuestion($request['useCaseName']),
            true
        );

        if (!$createRequest) {
            parent::handleRequest($request);
            return;
        }

        // Create request file 
        $requestFile = $this->creator->generateRequest(
            name: $request['useCaseName'],
            folderPath: $request['useCaseFolderPath'],
            domainPath: $request['domainPath']
        );

        $this->handleNextFieldCreation($requestFile, $request);
    }
    
    /**
     * Asking user for adding field in current RequestFile class 
     * Then add properties to RequestFile instance.
     *
     * @param RequestFile $requestFile
     * @param array $request
     * @return void
     */
    private function handleNextFieldCreation(RequestFile $requestFile, array $request): void
    {

    }

    /**
     * Asking user for adding field in current RequestFile class 
     *
     * @param RequestFile $requestFile
     * @param array $request
     * @return void
     */
    private function old_handleNextFieldCreation(RequestFile $requestFile, array $request): void
    {
        $isFirstField = true;
        
        $currentFields = $this->getPropertyNames($requestFile->getFullClassName());
        
        $domainEntityDirectoryPath = sprintf(
            'src/%s/Entity/',
            $request['domainPath']
        );

        $entityDomainTypes = self::getDomainEntityTypes(
            domainEntityDirectoryPath: $domainEntityDirectoryPath
        );

        $manipulator = $this->createClassManipulator(
            $requestFile->getTargetPath(), 
            $this->io, 
            false
        );

        while (true) {
            $newField = $this->askForNextField(
                $this->io,
                $currentFields,
                $requestFile->getFullClassName(),
                $isFirstField,
                $entityDomainTypes
            );

            $isFirstField = false;

            if (is_null($newField)) {
                break;
            }

            $manipulator->addClassProperty(
                mapping: $newField,
                withConstructorPropertyPromotion: true
            );

            $currentFields[] = $newField->propertyName;
            
            $this->dumpFile(
                $requestFile->getTargetPath(), 
                $manipulator->getSourceCode(), 
                $this->io
            );
        }
    }
}
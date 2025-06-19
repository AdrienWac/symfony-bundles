<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;

final class PresenterInterfaceHandler extends AbstractHandler
{
    public function handleRequest(mixed $request): void
    {
        $createPresenterInterface = $this->io->confirm(
            PresenterInterfaceFile::getUserQuestion($request['useCaseName']),
            true
        );

        if ($createPresenterInterface) {
            $this->creator->generatePresenterInterface(
                name: $request['useCaseName'],
                folderPath: $request['useCaseFolderPath'],
                domainPath: $request['domainPath']
            );
        }

        parent::handleRequest($request);
    }
}
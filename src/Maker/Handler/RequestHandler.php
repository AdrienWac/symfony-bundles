<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;

final class RequestHandler extends AbstractHandler
{
    public function handleRequest(mixed $request): void
    {
        $createRequest = $this->io->confirm(
            RequestFile::getUserQuestion($request['useCaseName']),
            true
        );

        if ($createRequest) {
            $this->creator->generateRequest(
                name: $request['useCaseName'],
                folderPath: $request['useCaseFolderPath'],
                domainPath: $request['domainPath']
            );
        }

        parent::handleRequest($request);
    }
}
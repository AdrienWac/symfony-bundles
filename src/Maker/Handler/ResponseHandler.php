<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ResponseFile;

final class ResponseHandler extends AbstractHandler
{
    public function handleRequest(mixed $request): void
    {
        $createResponse = $this->io->confirm(
            ResponseFile::getUserQuestion($request['useCaseName']),
            true
        );

        if ($createResponse) {
            $this->creator->generateResponse(
                name: $request['useCaseName'],
                folderPath: $request['useCaseFolderPath'],
                domainPath: $request['domainPath']
            );
        }

        parent::handleRequest($request);
    }
}
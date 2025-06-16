<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Handler;

final class UseCaseHandler extends AbstractHandler
{
    public function handleRequest(mixed $request): void
    {
        $this->creator->generateUseCase(
            name: $request['useCaseName'],
            folderPath: $request['useCaseFolderPath'],
            domainPath: $request['domainPath']
        );

        parent::handleRequest($request);
    }
}
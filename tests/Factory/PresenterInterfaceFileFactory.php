<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ResponseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\FactoryInterface;

final class PresenterInterfaceFileFactory implements FactoryInterface
{
    public static function create(
        string $domainFolderPath = 'Domain',
        string $folderPath = 'ParentFolder/ChildFolder/Foo',
        string $useCaseName = 'Bar',
        ?ResponseFile $responseFile = null
    ) {
        if (is_null($responseFile)) {
            $responseFile = ResponseFileFactory::create(
                $domainFolderPath,
                $folderPath,
                $useCaseName
            );
        }
        
        return new PresenterInterfaceFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName,
            $responseFile
        );
    }
}
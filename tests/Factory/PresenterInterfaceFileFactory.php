<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\FactoryInterface;

final class PresenterInterfaceFileFactory implements FactoryInterface
{
    public static function create(
        string $domainFolderPath = 'Domain',
        string $folderPath = 'ParentFolder/ChildFolder/Foo',
        string $useCaseName = 'Bar'
    ) {
        return new PresenterInterfaceFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName
        );
    }
}
<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\FactoryInterface;

final class RequestFileFactory implements FactoryInterface
{
    public static function create(
        string $domainFolderPath = 'Domain',
        string $folderPath = 'ParentFolder/ChildFolder/Foo',
        string $useCaseName = 'Bar'
    ) {
        return new RequestFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName
        );
    }
}
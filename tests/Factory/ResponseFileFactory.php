<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ResponseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\FactoryInterface;

final class ResponseFileFactory implements FactoryInterface
{
    public static function create(
        string $domainFolderPath = 'Domain',
        string $folderPath = 'ParentFolder/ChildFolder/Foo',
        string $useCaseName = 'Bar'
    ) {
        return new ResponseFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName
        );
    }
}
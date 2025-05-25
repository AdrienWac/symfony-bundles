<?php 

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Factory;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Traits\RequestFileFactory;

final class UseCaseFileFactory implements FactoryInterface
{
    public static function create(
        string $domainFolderPath = 'Domain',
        string $folderPath = 'ParentFolder/ChildFolder/Foo',
        string $useCaseName = 'Bar',
        ?RequestFile $requestFile = null
    ): UseCaseFile
    {
        if (is_null($requestFile)) {
            $requestFile = RequestFileFactory::create(
                $domainFolderPath,
                $folderPath,
                $useCaseName
            );
        }

        return new UseCaseFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName,
            $requestFile
        );
    }
}
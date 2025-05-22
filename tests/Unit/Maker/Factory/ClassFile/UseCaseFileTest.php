<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;
use PHPUnit\Framework\TestCase;

final class UseCaseFileTest extends TestCase
{
    public function testInstanciateUseCaseFile(): void
    {
        $domainFolderPath = 'Domain';
        $folderPath = 'ParentFolder/ChildFolder/Foo';
        $useCaseName = 'Bar';

        $useCaseFile = new UseCaseFile(
            $domainFolderPath,
            $folderPath,
            $useCaseName
        );

        $this->assertInstanceOf(
            UseCaseFile::class,
            $useCaseFile
        );
    }
}
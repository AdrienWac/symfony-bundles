<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\UseCaseFileFactory;
use PHPUnit\Framework\TestCase;

final class UseCaseFileTest extends TestCase
{
    /**
     * Should get instance of use case file
     * Should have built expected namespace attribute
     */
    public function testInstanciateUseCaseFile(): void
    {
        $domainFolderPath = 'Domain';
        $folderPath = 'ParentFolder/ChildFolder/Foo';
        $useCaseName = 'Bar';

        $useCaseFile = UseCaseFileFactory::create($domainFolderPath, $folderPath, $useCaseName);

        $this->assertInstanceOf(
            UseCaseFile::class,
            $useCaseFile
        );

        $expectedUseCaseNameSpace = 'Domain\UseCase\ParentFolder\ChildFolder\Foo\Bar';
        
        $this->assertEquals(
            $expectedUseCaseNameSpace,
            $useCaseFile->getNameSpace()
        );
    }


}
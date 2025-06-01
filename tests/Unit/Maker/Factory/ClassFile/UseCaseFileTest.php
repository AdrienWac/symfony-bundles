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

        $expectedFullClassName = 'Domain\UseCase\ParentFolder\ChildFolder\Foo\Bar';
        $expectedUseCaseNameSpace = 'Domain\UseCase\ParentFolder\ChildFolder\Foo';
        $expectedShortClassName = 'Bar';
        $expectedUseStatementArray = [
            'Domain\Request\ParentFolder\ChildFolder\Foo\BarRequest',
            'Domain\API\ParentFolder\ChildFolder\Foo\BarPresenterInterface'
        ];
        
        $this->assertEquals(
            $expectedUseCaseNameSpace,
            $useCaseFile->getNameSpace()
        );

        $this->assertEquals(
            $expectedShortClassName,
            $useCaseFile->getClassName()
        );

        $this->assertEquals(
            $expectedFullClassName,
            $useCaseFile->getFullClassName()
        );

        $this->assertEquals(
            $expectedUseStatementArray,
            $useCaseFile->getUseStatementArray()
        );
    }


}
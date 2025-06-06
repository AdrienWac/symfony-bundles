<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\PresenterInterfaceFileFactory;
use PHPUnit\Framework\TestCase;

final class PresenterInterfaceFileTest extends TestCase
{
    /**
     * Should get instance of request file
     */
    public function testInstanciate(): void
    {
        $domainFolderPath = 'Domain';
        $folderPath = 'ParentFolder/ChildFolder/Foo';
        $useCaseName = 'Bar';

        $presenterInterfaceFile = PresenterInterfaceFileFactory::create($domainFolderPath, $folderPath, $useCaseName);

        $this->assertInstanceOf(
            PresenterInterfaceFile::class,
            $presenterInterfaceFile
        );

        $expectedFullClassName = 'Domain\API\ParentFolder\ChildFolder\Foo\BarPresenterInterface';
        $expectedUseCaseNameSpace = 'Domain\API\ParentFolder\ChildFolder\Foo';
        $expectedShortClassName = 'BarPresenterInterface';
        $expectedUseStatementArray = [
            'Domain\Response\ParentFolder\ChildFolder\Foo\BarResponse'
        ];
        
        $this->assertEquals(
            $expectedUseCaseNameSpace,
            $presenterInterfaceFile->getNameSpace()
        );

        $this->assertEquals(
            $expectedShortClassName,
            $presenterInterfaceFile->getClassName()
        );

        $this->assertEquals(
            $expectedFullClassName,
            $presenterInterfaceFile->getFullClassName()
        );

        $this->assertEquals(
            $expectedUseStatementArray,
            $presenterInterfaceFile->getUseStatementArray()
        );
    }
}
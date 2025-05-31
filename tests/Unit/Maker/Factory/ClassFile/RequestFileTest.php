<?php

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\RequestFileFactory;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\UseCaseFileFactory;
use PHPUnit\Framework\TestCase;

final class RequestFileTest extends TestCase
{
    /**
     * Should get instance of request file
     */
    public function testInstanciate(): void
    {
        $domainFolderPath = 'Domain';
        $folderPath = 'ParentFolder/ChildFolder/Foo';
        $useCaseName = 'Bar';

        $requestFile = RequestFileFactory::create($domainFolderPath, $folderPath, $useCaseName);

        $this->assertInstanceOf(
            RequestFile::class,
            $requestFile
        );

        $expectedFullClassName = 'Domain\Request\ParentFolder\ChildFolder\Foo\BarRequest';
        $expectedUseCaseNameSpace = 'Domain\Request\ParentFolder\ChildFolder\Foo';
        $expectedShortClassName = 'BarRequest';
        $expectedUseStatementArray = [];
        
        $this->assertEquals(
            $expectedUseCaseNameSpace,
            $requestFile->getNameSpace()
        );

        $this->assertEquals(
            $expectedShortClassName,
            $requestFile->getClassName()
        );

        $this->assertEquals(
            $expectedFullClassName,
            $requestFile->getFullClassName()
        );

        $this->assertEquals(
            $expectedUseStatementArray,
            $requestFile->getUseStatementArray()
        );
    }
}
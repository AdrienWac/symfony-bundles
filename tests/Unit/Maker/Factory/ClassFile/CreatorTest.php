<?php 

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\Creator;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\CreatorFactory;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\UseCaseFileFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\FileManager;

class CreatorTest extends TestCase
{
    private FileManager & MockObject $fileManager;


    public function setUp(): void
    {
        parent::setUp();

        $this->fileManager = $this
            ->getMockBuilder(FileManager::class)
            ->getMock();

    }


    public function testGenerateUseCase(): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $creator->generateUseCase($useCaseName, $folderPath, $domainFolder);
        
        $useCaseFile = UseCaseFileFactory::create($useCaseName, $folderPath, $domainFolder);

        $this->assertEquals(
            [$useCaseFile],
            $creator->getOperationsList()
        );
    }
}
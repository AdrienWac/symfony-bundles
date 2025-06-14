<?php 

declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Tests\Unit\Maker\Factory\ClassFile;

use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\PresenterInterfaceFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\RequestFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\ResponseFile;
use AdrienLbt\HexagonalMakerBundle\Maker\Factory\ClassFile\UseCaseFile;
use AdrienLbt\HexagonalMakerBundle\Tests\Factory\CreatorFactory;
use AdrienLbt\HexagonalMakerBundle\Tests\Utils\Constraint\Traversable\TraversableContainsInstanceOf;
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
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * Should have expected element in operationsList attribute
     * Should have expected elements in elementsList attribute
     */
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
        
        $this->assertContainsOnlyInstancesOf(UseCaseFile::class, $creator->getOperationsList());
        
        $authorizedInstance = [
            UseCaseFile::class, 
            RequestFile::class, 
            ResponseFile::class, 
            PresenterInterfaceFile::class
        ];

        static::assertThat(
            $creator->getElementsList(),
            new TraversableContainsInstanceOf($authorizedInstance),
            ''
        );
    }

    /**
     * Should add ResponseFile instance in operations and elements list
     */
    public function testGenerateResponse(): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $this->assertEmpty($creator->getOperationsList());
        $this->assertEmpty($creator->getElementsList());

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $creator->generateResponse($useCaseName, $folderPath, $domainFolder);

        $this->assertContainsOnlyInstancesOf(ResponseFile::class, $creator->getOperationsList());
        $this->assertContainsOnlyInstancesOf(ResponseFile::class, $creator->getElementsList());
    }

    /**
     * Should create an PresenterInterfaceFile instance and then add it in operationList
     */
    public function testGeneratePresenterInterface(): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $creator->generateResponse($useCaseName, $folderPath, $domainFolder);
        $creator->generatePresenterInterface($useCaseName, $folderPath, $domainFolder);
        
        $expectedInstanceInElementList = [
            ResponseFile::class, 
            PresenterInterfaceFile::class
        ];

        static::assertThat(
            $creator->getElementsList(),
            new TraversableContainsInstanceOf($expectedInstanceInElementList),
            ''
        );
    }

    /**
     * Should throw an exception
     */
    public function testGeneratePresenterInterfaceWithMissingResponseFile(): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf("Unable to create %s. Missing %s", PresenterInterfaceFile::class, ResponseFile::class));

        $creator->generatePresenterInterface($useCaseName, $folderPath, $domainFolder);
    }

    /**
     * @dataProvider dataProviderGenerateInstanceFileWithExistingElement
     * Should add ClassFile instance from elementList in operationList
     */
    public function testGeneratePresenterInterfaceWithExistingInElementList(string $className, string $methodName): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $creator->generateUseCase($useCaseName, $folderPath, $domainFolder);

        $fileInstanceBeforeResponseCreation = $creator->getInstanceOf(
            $creator->getElementsList(), 
            $className
        );

        $creator->$methodName($useCaseName, $folderPath, $domainFolder);

        $fileInstanceAfterResponseCreation = $creator->getInstanceOf(
            $creator->getElementsList(), 
            $className
        );

        $this->assertEquals($fileInstanceBeforeResponseCreation, $fileInstanceAfterResponseCreation);
    }

    /**
     * Provides class and method name to test that creating an existing ClassFile instance in 
     * elementList doesn't restart creation
     *
     * @return \Generator
     */
    public static function dataProviderGenerateInstanceFileWithExistingElement(): \Generator
    {
        yield [
            PresenterInterfaceFile::class,
            'generatePresenterInterface',    
        ];

        yield [
            ResponseFile::class,
            'generateResponse'
        ];
    }

    
}
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
     *
     * @return void
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
     * @return void
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
     * Create use case and response file, then check if 
     * response file in elements list before create response file
     * in the same as after create response file. 
     * @todo Use a partial mock ?
     * @return void
     */
    public function testGenerateResponseWithExistingInElementsList(): void
    {
        $this->fileManager
            ->method('getRelativePathForFutureClass')
            ->willReturn('/var/www/html/src/Domain/Bar/Foo.php');

        $creator = CreatorFactory::create($this->fileManager);

        $domainFolder = 'Domain';
        $folderPath = 'Bar';
        $useCaseName = 'Foo';

        $creator->generateUseCase($useCaseName, $folderPath, $domainFolder);

        $responseFileInstanceBeforeResponseCreation = $creator->getInstanceOf(
            $creator->getElementsList(), 
            ResponseFile::class
        );

        $creator->generateResponse($useCaseName, $folderPath, $domainFolder);

        $responseFileInstanceAfterResponseCreation = $creator->getInstanceOf(
            $creator->getElementsList(), 
            ResponseFile::class
        );

        $this->assertEquals(
            $responseFileInstanceBeforeResponseCreation, 
            $responseFileInstanceAfterResponseCreation
        );
    }
}
<?php

namespace AdrienLbt\HexagonalMakerBundle\Tests\Maker;

use AdrienLbt\HexagonalMakerBundle\Maker\MakeHexagonalUseCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

/**
 * @group legacy
 */
class MakeHexagonalUseCaseTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeHexagonalUseCase::class;
    }

    public function getTestDetails(): \Generator
    {
        yield 'create_full_use_case' => [
            $this->createMakerTest()
                ->run(function (MakerTestRunner $runner) {
                    $folder = 'ParentFolder/ChildFolder/Foo';
                    $name = 'Bar';

                    $output = $runner->runMaker([
                        // Folder
                        $folder,
                        // Use case class name
                        $name,
                        // Create Response file
                        'y',
                        // Create Presenter file
                        'y',
                        // Create Request file
                        'y',
                        // No properties in request
                        ''
                    ]);

                    $this->assertStringContainsString('Success', $output);

                    array_map(
                        fn($filePath) => $this->assertFileExists($runner->getPath($filePath)), 
                        [
                            'src/Domain/UseCase/'. $folder .'/'.$name.'.php',
                            'src/Domain/Request/'. $folder .'/'.$name.'Request.php',
                            'src/Domain/Response/'. $folder .'/'.$name.'Response.php',
                            'src/Domain/API/'. $folder .'/'.$name.'PresenterInterface.php'
                        ]
                    );
                })
        ];
    }
}

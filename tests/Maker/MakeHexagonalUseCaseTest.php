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
                    $output = $runner->runMaker([
                        // Folder
                        'Foo',
                        // Use case class name
                        'Bar',
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
                })
        ];
    }
}

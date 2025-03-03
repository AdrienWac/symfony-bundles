<?php

namespace AdrienLbt\HexagonalMakerBundle\Tests\Maker;

use AdrienLbt\HexagonalMakerBundle\Maker\MakeDomainUseCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

/**
 * @group legacy
 */
class MakeDomainUseCaseTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeDomainUseCase::class;
    }

    public function getTestDetails(): \Generator
    {
        yield 'create_full_use_case' => [
            $this->createMakerTest()
                ->run(function(MakerTestRunner $runner) {
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
                    ]);

                    $this->assertStringContainsString('Success', $output);
                })
        ];
    }
}
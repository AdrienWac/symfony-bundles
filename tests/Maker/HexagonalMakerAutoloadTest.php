<?php

namespace AdrienLbt\HexagonalMakerBundle\Tests\Maker;

use AdrienLbt\HexagonalMakerBundle\Command\HexagonalMakerAutoload;
use AdrienLbt\HexagonalMakerBundle\Maker\MakeHexagonalUseCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestDetails;
use Symfony\Bundle\MakerBundle\Test\MakerTestEnvironment;
use Symfony\Bundle\MakerBundle\Test\MakerTestProcess;

/**
 * Test command HexagonalMakerAutoload.
 * Should be registered in the command list.
 * Should be edit autoload declaration in the composer.json application file.
 */
class HexagonalMakerAutoloadTest extends MakerTestCase
{
    private MakerTestDetails $makerTestDetails;
    private MakerTestEnvironment $makerTestEnv;

    private const NAMESPACE_MAKER_BUNDLE = 'Symfony\\Bundle\\MakerBundle\\';

    /**
     * This method is called before each test of this test class is run.
     * Instanciate MakerTestEnvironment and prepare the directory.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->makerTestDetails = new MakerTestDetails(
            new MakeHexagonalUseCase('src/Domain')
        );

        $this->makerTestEnv = MakerTestEnvironment::create($this->makerTestDetails);

        $this->makerTestEnv->prepareDirectory();

        parent::setUp();
    }


    /**
     * Use it to create a new MakerTestDetails instance.
     *
     * @return string
     */
    protected function getMakerClass(): string
    {
        return MakeHexagonalUseCase::class;
    }

    /**
     * Should be listed in the command list.
     *
     * @return void
     */
    public function testRegistrationCommand(): void
    {
        MakerTestProcess::create('php bin/console c:c', $this->makerTestEnv->getPath(), [])
            ->run();

        $output = MakerTestProcess::create('php bin/console list --short --format xml', $this->makerTestEnv->getPath(), [])
            ->run()
            ->getOutput();

        $this->assertMatchesRegularExpression(
            '/<command id="' . HexagonalMakerAutoload::getDefaultName() . '" name="' . HexagonalMakerAutoload::getDefaultName() . '" hidden="0">/',
            $output,
            'The command ' . HexagonalMakerAutoload::getDefaultName() . ' is not registered.'
        );
    }

    /**
     * Should have the autoload declaration in the composer.json file.
     * Should have psr-4 key in the autoload declaration.
     * Should have the Symfony\Bundle\MakerBundle namespace in the psr-4 key.
     * Should have path to vendor for the Symfony\Bundle\MakerBundle namespace.
     *
     * @return void
     */
    public function testEditAutoloadDeclaration(): void
    {
        $applicationComposerJsonPath = $this->getApplicationComposerJsonPath();

        $this->assertFileExists(
            $applicationComposerJsonPath,
            'The composer.json file does not exist.'
        );

        $this->clearComposerJsonFile($applicationComposerJsonPath);

        MakerTestProcess::create('php bin/console make:hexagonal:autoload', $this->makerTestEnv->getPath(), [])
            ->run();

        $composerJson = json_decode(
            file_get_contents($applicationComposerJsonPath),
            true
        );

        $this->assertArrayHasKey(
            'autoload',
            $composerJson,
            'The autoload key does not exist in the composer.json file.'
        );
        $this->assertArrayHasKey(
            'psr-4',
            $composerJson['autoload'],
            'The psr-4 key does not exist in the composer.json file.'
        );
        $this->assertArrayHasKey(
            'Symfony\\Bundle\\MakerBundle\\',
            $composerJson['autoload']['psr-4'],
            'The Symfony\Bundle\MakerBundle namespace does not exist in the composer.json file.'
        );
        $this->assertEquals(
            'vendor/adrienlbt/hexagonal-maker-bundle/src/Maker/Decorator/',
            $composerJson['autoload']['psr-4']['Symfony\\Bundle\\MakerBundle\\'],
            'The src/Application directory is not set for the Symfony\Bundle\MakerBundle namespace in the composer.json file.'
        );
    }

    /**
     * Should add path of vendor in composer autoload file
     * @return void
     */
    public function testEditAutoloadValue(): void
    {
        $autoloadPsr4PathFile = $this->makerTestEnv->getPath() . '/vendor/composer/autoload_psr4.php';

        $this->assertFileExists(
            $autoloadPsr4PathFile,
            'The autoload psr4 file does not exist.'
        );

        $arrayAutoloadPaths = require($autoloadPsr4PathFile);

        $this->assertArrayHasKey(
            self::NAMESPACE_MAKER_BUNDLE,
            $arrayAutoloadPaths,
            'Autoload psr4 declaration not exist for the Symfony Maker bundle.'
        );

        $pathToFiles = $this->buildPathToFilesFromApplicationContext();

        $this->assertContains($pathToFiles, $arrayAutoloadPaths[self::NAMESPACE_MAKER_BUNDLE]);
    }

    /**
     * Build path to bundle files from application context
     * @return string 
     * @example 
     * maker_app_40cd750bba9870f18aada2478b24840a_current/vendor/adrienlbt/
     * hexagonal-maker-bundle/src/Maker/Decorator
     */
    private function buildPathToFilesFromApplicationContext(): string
    {
        $base = $this->makerTestEnv->getPath();

        $composerJsonPath = $this->getPackageComposerJsonPath();

        $composerJson = $this->getComposerJsonFile($composerJsonPath);

        $vendorName = $composerJson['name'];

        return $base . '/vendor/' . $vendorName . '/src/Maker/Decorator';
    }

    /**
     * Return content of composer json
     * @param string $composerFilePath
     * @return array Composer json content
     */
    private function getComposerJsonFile(string $composerFilePath): array
    {
        return json_decode(
            file_get_contents($composerFilePath),
            true
        );
    }

    private function getPackageComposerJsonPath(): string
    {
        return $_SERVER['WORKDIR_PATH'] . '/composer.json';
    }

    /**
     * Return application composer json file path
     * @return string
     */
    private function getApplicationComposerJsonPath(): string
    {
        return $this->makerTestEnv->getPath() . '/composer.json';
    }

    /**
     * Remove the autoload declaration for Symfony\Bundle\MakerBundle namespace in the composer.json file.
     * @param string $composerJsonPath
     * @return void
     */
    private function clearComposerJsonFile(string $composerJsonPath): void
    {
        $composerJson = json_decode(
            file_get_contents($composerJsonPath),
            true
        );

        if (!array_key_exists('autoload', $composerJson)) {
            return;
        }

        if (!array_key_exists('psr-4', $composerJson['autoload'])) {
            return;
        }

        if (!array_key_exists('Symfony\\Bundle\\MakerBundle\\', $composerJson['autoload']['psr-4'])) {
            return;
        }

        unset($composerJson['autoload']['psr-4']['Symfony\\Bundle\\MakerBundle\\']);

        // Clear the composer.json file
        file_put_contents($composerJsonPath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }


    /**
     * Should throw an exception if the composer.json file does not exist.
     * @return void
     */
    public function testMissingComposerJsonThrowException(): void
    {
        $this->assertFileExists(
            $this->makerTestEnv->getPath() . '/composer.json',
            'The composer.json file does not exist.'
        );

        // Remove the composer.json file
        unlink($this->makerTestEnv->getPath() . '/composer.json');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/composer.json file not found/');

        MakerTestProcess::create('php bin/console make:hexagonal:autoload', $this->makerTestEnv->getPath(), [])
            ->run();
    }

    /**
     * @dataProvider getTestDetails
     * @override
     * Skips the test because it avoid to run the parent method.
     * Allow to extend MakerTestCase without the test play.
     * @return void
     */
    public function testExecute(MakerTestDetails $makerTestDetails)
    {
        $this->markTestSkipped('This test is skipped because it is used for help.');
    }

    /**
     * Use to respect MakerTestCase process.
     * @return array
     */
    public function getTestDetails()
    {
        return [
            [
                'makerTestDetails' => new MakerTestDetails(
                    new MakeHexagonalUseCase('src/Domain')
                ),
            ]
        ];
    }
}

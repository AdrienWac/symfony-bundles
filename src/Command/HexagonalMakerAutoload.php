<?php

namespace AdrienLbt\HexagonalMakerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name:'make:hexagonal:autoload',
    description: 'Add autoload declaration for overriding maker bundle classes',
    hidden: false
)]
class HexagonalMakerAutoload extends Command
{
    private const TIMEOUT_DUMP_AUTOLOAD = 500;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonPath = $this->getComposerJsonPath();

        if (!file_exists($composerJsonPath)) {
            $output->writeln('<error>composer.json file not found.</error>');
            return Command::FAILURE;
        }

        $composerJson = json_decode(
            file_get_contents($composerJsonPath),
            true,
            512,
            \JSON_THROW_ON_ERROR
        );

        if (!isset($composerJson['autoload']['psr-4'])) {
            $composerJson['autoload']['psr-4'] = [];
        }
        if (!isset($composerJson['autoload']['psr-4']['Symfony\\Bundle\\MakerBundle\\'])) {
            $composerJson['autoload']['psr-4']['Symfony\\Bundle\\MakerBundle\\'] = 'vendor/adrienlbt/hexagonal-maker-bundle/src/Maker/Decorator/';
        }
        file_put_contents(
            $composerJsonPath,
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $output->writeln('<info>Autoload declaration added to composer.json file.</info>');

        $this->dumpAutoload($output);

        return Command::SUCCESS;
    }

    private function getComposerJsonPath(): string
    {
        return $_SERVER['PWD'] . '/composer.json';
    }

    /**
     * Run composer dump-autoload command
     * @return void
     * @throws ProcessFailedException If an error occured during the process or if the process failed.
     */
    private function dumpAutoload(OutputInterface $output): void
    {
        $output->writeln('<info>Run "composer dump-autoload" to update the autoload files.</info>');

        $process = new Process(['composer', 'dump-autoload']);
        $process->setTimeout(self::TIMEOUT_DUMP_AUTOLOAD);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}

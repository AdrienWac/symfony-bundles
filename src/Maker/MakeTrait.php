<?php

namespace AdrienLbt\HexagonalMakerBundle\Maker;

use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassProperty;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Filesystem\Filesystem;

trait MakeTrait
{
    /**
     * Undocumented function
     *
     * @param string $path
     * @param ConsoleStyle $io
     * @param boolean $overwrite
     * @return ClassSourceManipulator
     */
    private function createClassManipulator(string $path, ConsoleStyle $io, bool $overwrite): ClassSourceManipulator
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(\sprintf('Cannot find file "%s"', $path));
        }

        $manipulator = new ClassSourceManipulator(
            sourceCode: file_get_contents($path),
            overwrite: $overwrite,
            useAttributesForDoctrineMapping: false
        );

        $manipulator->setIo($io);

        return $manipulator;
    }

    /**
     * Retrieves class property names
     *
     * @param string $class
     * @return array<string> Array of class property names
     */
    private function getPropertyNames(string $class): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflClass = new \ReflectionClass($class);

        return array_map(static fn (\ReflectionProperty $prop) => $prop->getName(), $reflClass->getProperties());
    }

    /**
     * Add content into a file with Symfony component File System
     *
     * @param string $filename
     * @param string $content
     * @param ConsoleStyle $io
     * @return void
     */
    public function dumpFile(string $filename, string $content, ConsoleStyle $io): void
    {
        $fs = new Filesystem();
        $absolutePath = $filename;
        $newFile = !file_exists($filename);
        $existingContent = $newFile ? '' : file_get_contents($absolutePath);

        $comment = $newFile ? '<fg=blue>created</>' : '<fg=yellow>updated</>';
        if ($existingContent === $content) {
            $comment = '<fg=green>no change</>';
        }

        $fs->dumpFile($absolutePath, $content);

        $io->comment(\sprintf(
            '%s: %s',
            $comment,
            // $this->makerFileLinkFormatter->makeLinkedPath($absolutePath, $relativePath)
            $absolutePath
        ));
    }

    /**
     * Récupère les noms de classes entités du Domain
     *
     * @return array
     */
    public static function getDomainEntityTypes(
        ?string $domainEntityDirectoryPath = null
    ): array {
        $basePath = $_SERVER['PWD'] ?? 'var/www/html';

        $domainEntityDirectoryPath = \sprintf(
            $basePath . '/%s',
            $domainEntityDirectoryPath ?? 'src/Domain/Entity/'
        );

        if (!file_exists($domainEntityDirectoryPath)) {
            throw new \Exception(sprintf("Domain entity directory not exist at path %s", $domainEntityDirectoryPath));
        }


        $arrayClassesFilePath = self::extractClassFilePath(
            $domainEntityDirectoryPath
        );


        $arrayClassesName = array_map(
            'self::getClassNameFromPath',
            $arrayClassesFilePath
        );

        return $arrayClassesName;
    }

    /**
     * Fonction récursive qui parcourt le contenu d'un dossier pour extraire les noms de clases
     *
     * @param string $domainEntityDirectoryPath
     * @return array
     */
    public static function extractClassFilePath(
        string $domainEntityDirectoryPath
    ): array {
        $result = [];

        $domainEntityDirectoryContent = array_diff(scandir($domainEntityDirectoryPath), ['..', '.']);

        if (empty($domainEntityDirectoryContent)) {
            return $result;
        }

        // Pour chaque element du dossier
        foreach ($domainEntityDirectoryContent as $content) {
            $fullPath = $domainEntityDirectoryPath . '/' . $content;

            if (is_dir($fullPath)) {
                $subDirectoryContent = self::extractClassFilePath($fullPath);
                if (!empty($subDirectoryContent)) {
                    $result = array_merge($result, $subDirectoryContent);
                }
                continue;
            }

            if (self::isPhpFile($fullPath)) {
                $result[] = $fullPath;
            }
        }

        return $result;
    }

    /**
     * Vérifie si le fichier est un fichier PHP
     * - Test de l'extension
     * - Vérification du contenu
     *
     * @param string $filePath
     * @return boolean True si le fichier est un Php. Faux sinon.
     */
    public static function isPhpFile(string $filePath): bool
    {
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
            return false;
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return false;
        }

        $firstBytes = fread($handle, 20);
        fclose($handle);

        if (!str_contains($firstBytes, '<?php')) {
            return false;
        }

        return true;
    }

    /**
     * Récupère le namespace & le nom de la classe depuis le chemin du fichier
     *
     * @param string $filePath
     * @return string|null
     * Ex:
     * A
     * |_B
     *    |_Foo.php
     * getClassNameFromPath(A/B/Foo.php) => A\B\Foo
     * Si le fichier n'existe pas retournera null.
     */
    public static function getClassNameFromPath(string $filePath): ?string
    {
        $namespace = '';
        $className = '';

        if (!file_exists($filePath)) {
            return null;
        }

        $lines = file($filePath);

        $getClassName = function (string $path) {
            $explodePathFile = explode('/', $path);

            $lastElement = end($explodePathFile);

            if (strpos($lastElement, '.php') === false) {
                return null;
            }

            return substr($lastElement, 0, strpos($lastElement, '.php'));
        };

        $className = $getClassName($filePath);

        foreach ($lines as $line) {
            if (preg_match('/^namespace\s+([^;]+);/', $line, $matches)) {
                $namespace = trim($matches[1]);
                break;
            }
        }

        if ($className) {
            return $namespace ? $namespace . '\\' . $className : $className;
        }

        return null;
    }

    /**
     * Add new fields on class
     *
     * @param ConsoleStyle $io
     * @param array $fields
     * @param string $entityClass
     * @param boolean $isFirstField
     * @return ClassProperty|null
     */
    private function askForNextField(
        ConsoleStyle $io,
        array $fields,
        string $entityClass,
        bool $isFirstField,
        array $otherValidTypes
    ): ClassProperty|null {
        $io->writeln('');

        if ($isFirstField) {
            $questionText = 'New property name (press <return> to stop adding fields)';
        } else {
            $questionText = 'Add another property? Enter the property name (or press <return> to stop adding fields)';
        }

        $fieldName = $io->ask($questionText, null, function ($name) use ($fields) {
            // allow it to be empty
            if (!$name) {
                return $name;
            }

            if (\in_array($name, $fields)) {
                throw new \InvalidArgumentException(\sprintf('The "%s" property already exists.', $name));
            }

            return Validator::validatePropertyName($name);
        });

        if (!$fieldName) {
            return null;
        }

        $defaultType = 'string';
        // try to guess the type by the field name prefix/suffix
        // convert to snake case for simplicity
        $snakeCasedField = Str::asSnakeCase($fieldName);

        if ('_at' === $suffix = substr($snakeCasedField, -3)) {
            $defaultType = 'datetime_immutable';
        } elseif ('_id' === $suffix) {
            $defaultType = 'integer';
        } elseif (str_starts_with($snakeCasedField, 'is_')) {
            $defaultType = 'boolean';
        } elseif (str_starts_with($snakeCasedField, 'has_')) {
            $defaultType = 'boolean';
        }

        $type = null;

        // ? Récupère un ensemble de type natif depuis doctrine
        $types = $this->getTypesMap();

        $allValidTypes = array_merge(
            array_keys($types),
            // EntityRelation::getValidRelationTypes(), // Inutile pour moi pour ma partie Request
            $otherValidTypes,
            ['relation', 'enum']
        );

        while (null === $type) {
            $question = new Question('Field type (enter <comment>?</comment> to see all types)', $defaultType);
            $question->setAutocompleterValues($allValidTypes);
            $type = $io->askQuestion($question);

            if ('?' === $type) {
        //         $this->printAvailableTypes($io);
                $io->writeln('');

                $type = null;
            } elseif (!\in_array($type, $allValidTypes)) {
                // $this->printAvailableTypes($io);
                $io->error(\sprintf('Invalid type "%s".', $type));
                $io->writeln('');

                $type = null;
            }
        }
        // ???????


        // this is a normal field
        $classProperty = new ClassProperty(propertyName: $fieldName, type: $type);

        if ('string' === $type) {
            // default to 255, avoid the question
            $classProperty->length = $io->ask('Field length', '255', Validator::validateLength(...));
        } elseif ('decimal' === $type) {
            // 10 is the default value given in \Doctrine\DBAL\Schema\Column::$_precision
            $classProperty->precision = $io->ask(
                'Precision (total number of digits stored: 100.00 would be 5)',
                '10',
                Validator::validatePrecision(...)
            );

            // 0 is the default value given in \Doctrine\DBAL\Schema\Column::$_scale
            $classProperty->scale = $io->ask('Scale (number of decimals to store: 100.00 would be 2)', '0', Validator::validateScale(...));
        } elseif ('enum' === $type) {
            // ask for valid backed enum class
            $classProperty->enumType = $io->ask('Enum class', null, Validator::classIsBackedEnum(...));

            // set type according to user decision
            $classProperty->type = $io->confirm('Can this field store multiple enum values', false) ? 'simple_array' : 'string';
        }

        if ($io->confirm('Can this field be null (nullable)', false)) {
            $classProperty->nullable = true;
        }

        return $classProperty;
    }

    /**
     *
     * @return string[]
    */
    private function getTypesMap(): array
    {
        return Type::getTypesMap();
    }
}

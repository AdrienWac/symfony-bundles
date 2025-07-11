<?php declare(strict_types=1);

namespace AdrienLbt\HexagonalMakerBundle\Maker\Utils\ClassManipulator;

use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassProperty;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Doctrine\ORM\Mapping\Column;

final class ClassManipulator
{
    private ProxyClassSourceManipulator $proxyClassSourceManipulator;

    public function __construct(
        private string $sourceCode,
        private bool $overwrite = false,
        private bool $useAttributesForDoctrineMapping = true,
    )
    {
        $makerBundleClassSourceManipulator = new ClassSourceManipulator(
            $sourceCode,
            $overwrite,
            $useAttributesForDoctrineMapping
        );

        $this->proxyClassSourceManipulator = new ProxyClassSourceManipulator($makerBundleClassSourceManipulator);
    }

    /**
     * @todo Vérifier le fonctionnement avec les types de Classe
     * @todo Pour un type d'une entité, le type n'est pas ajouté dans le constructeur
     * @todo Les use sont ajoutés à la fin du fichier
     * Créer une propriété de classe.
     * Possibilité d'ajouter la définition en tant que paramètre du
     * constructeur via $withConstructorPropertyPromotion
     *
     * @param ClassProperty $mapping
     * @param boolean $withConstructorPropertyPromotion
     * @return void
     */
    public function addClassProperty(
        ClassProperty $mapping,
        bool $withConstructorPropertyPromotion = true
    ): void {
        $typeHint = DoctrineHelper::getPropertyTypeForColumn($mapping->type);

        if (is_null($typeHint)) {
            $typeHint = $this->proxyClassSourceManipulator->addUseStatementIfNecessary($mapping->type);
        }

        $nullable = $mapping->nullable ?? false;

        $attributes[] = $this->proxyClassSourceManipulator->buildAttributeNode(Column::class, $mapping->getAttributes(), 'ORM');

        $defaultValue = null;
        $commentLines = [];

        $propertyType = $typeHint;
        if ($propertyType && 'mixed' !== $propertyType) {
            // all property types
            $propertyType = '?' . $propertyType;
        }

        if ($withConstructorPropertyPromotion) {
            $this->addConstructorPropertyPromotion(
                name: $mapping->propertyName,
                defaultValue: $defaultValue,
                attributes: $attributes,
                comments: $mapping->comments,
                propertyType: $propertyType
            );
        } else {
            $this->proxyClassSourceManipulator->addProperty(
                name: $mapping->propertyName,
                defaultValue: $defaultValue,
                attributes: $attributes,
                comments: $mapping->comments,
                propertyType: $propertyType
            );
        }

        $this->proxyClassSourceManipulator->addGetter(
            $mapping->propertyName,
            $typeHint,
            // getter methods always have nullable return values
            // because even though these are required in the db, they may not be set yet
            // unless there is a default value
            'mixed' !== $propertyType,
            $commentLines
        );

        // don't generate setters for id fields
        if (!($mapping->id ?? false)) {
            $this->proxyClassSourceManipulator->addSetter($mapping->propertyName, $typeHint, $nullable && 'mixed' !== $propertyType);
        }
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param [type] $defaultValue
     * @param array $attributes
     * @param array $comments
     * @param string|null $propertyType
     * @return void
     */
    private function addConstructorPropertyPromotion(
        string $name,
        $defaultValue = '__default_value_none',
        array $attributes = [],
        array $comments = [],
        ?string $propertyType = null
    ): void {
        if ($this->proxyClassSourceManipulator->propertyExists($name)) {
            // we never overwrite properties
            return;
        }

        $newPropertyNode = $this->proxyClassSourceManipulator
            ->buildParam(
                $name,
                $defaultValue,
                $attributes,
                $comments,
                $propertyType
            )
            ->getNode();

        $this->proxyClassSourceManipulator->addNodeAfterConstructorProperties($newPropertyNode);
    }
}
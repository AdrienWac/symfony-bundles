# Hexagonal Maker Bundle

The Hexagonal Maker Bundle is a code generator, for Symfony, who automise the use case creation process for the Hexagonal Architecture. It's use the Symfony's Maker Bundle. 

## ⚙️Install
Add the bundle with composer 
``` bash
composer require --dev adrienlbt/hexagonal-maker-bundle
``` 

If Symfony Flex doesn't add automatically the bundle, active it manually: 
``` php
// config/bundles.php
return [
    // ...
    AdrienLbt\HexagonalMakerBundle\HexagonalMakerBundle::class => ['dev' => true]
];
```

## ⚙️Configure
If Symfony Flew doesn't create config file automaticallly, create it manually: 
``` yaml
# config/packages/hexagonal_maker_bundle.yaml
hexagonal_maker:
  application_path: 'Application'
  domain_path: 'Domain'
  infrastructure_path: 'Infrastructure'
```

### Override namespace 
Actually (and for this first version), we duplicate the code of Symfony's Maker Bundle. For use our command we need to override the namespace for use our implementation of generator. For this you can run the following command:
``` bash
bin/console make:hexagonal:dump-autoload
```

The result of this command, add namespace overiding in your composer.json file.
```json
// composer.json
{
    ...,
    "autoload": {
        "psr-4": {
            ...,
            "Symfony\\Bundle\\MakerBundle\\": "vendor/adrienlbt/hexagonal-maker-bundle/src/Maker/Decorator/"
        }
    }
}
```

## Usage

### Create Use Case
Run command like follow and follow instructions.

``` bash
bin/console make:hexagonal:usecase
```




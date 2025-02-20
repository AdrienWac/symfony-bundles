<?php

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Domain\UseCase\UseCaseInterface;
<?= $use_statements; ?>

class <?= $class_name . " " ?> implements UseCaseInterface
{

    public function __construct()
    {

    }

    public function execute(
        <?= Str::asCamelCase($class_name) . "Request" ?> <?= "$" . Str::asLowerCamelCase($class_name) . "Request" ?>,
        <?= "PresenterInterface" ?> <?= "$" . Str::asLowerCamelCase($class_name) . "Presenter" ?>
    ): void
    {
        
    }
}

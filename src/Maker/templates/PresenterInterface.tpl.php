<?php

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Domain\API\PresenterInterface;
<?= $use_statements; ?>

interface <?= $class_name ?> extends PresenterInterface
{
    public function present(<?= $use_case_name ?>Response $<?= Str::asLowerCamelCase($use_case_name) ?>Response): void;
}

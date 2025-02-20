<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Domain\Request\RequestInterface;
<?= $use_statements; ?>

class <?= $class_name . " " ?> implements RequestInterface
{

    public function __construct()
    {}
}

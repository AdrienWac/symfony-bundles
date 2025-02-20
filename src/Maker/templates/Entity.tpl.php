<?php

use Symfony\Bundle\MakerBundle\Maker\Common\EntityIdTypeEnum;

?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?= $use_statements; ?>

class <?= $class_name . "\n" ?>
{
<?php if (EntityIdTypeEnum::UUID === $id_type) : ?>
    private ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }
<?php elseif (EntityIdTypeEnum::ULID === $id_type) : ?>
    private ?Ulid $id = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }
<?php else : ?>
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
<?php endif ?>
}

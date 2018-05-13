<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;

class AuthorBehavior extends Behavior
{
    public function setAuthor($entity, $userId, $mode)
    {
        if ($mode == 'add') {
            $entity->created_by = $userId;
        } else {
            $entity->modified_by = $userId;
        }
        return $entity;
    }
}
<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrdersStudent Entity
 *
 * @property int $id
 * @property int $order_id
 * @property int $student_id
 * @property string $result
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\Student $student
 */
class OrdersStudent extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'order_id' => true,
        'student_id' => true,
        'result' => true,
        'description' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'order' => true,
        'student' => true
    ];
}

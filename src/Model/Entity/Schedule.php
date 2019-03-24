<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedule Entity
 *
 * @property int $id
 * @property int $order_id
 * @property \Cake\I18n\FrozenDate $end_date
 * @property string $teacher1
 * @property string $teacher2
 * @property string $teacher3
 * @property bool $del_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\Holiday[] $holidays
 */
class Schedule extends Entity
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
        'end_date' => true,
        'teacher1' => true,
        'teacher2' => true,
        'teacher3' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'order' => true,
        'holidays' => true
    ];
}

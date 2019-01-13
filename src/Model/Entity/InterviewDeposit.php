<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InterviewDeposit Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $type
 * @property \Cake\I18n\FrozenDate $payment_date
 * @property string $notes
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 */
class InterviewDeposit extends Entity
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
        'student_id' => true,
        'type' => true,
        'status' => true,
        'payment_date' => true,
        'notes' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true
    ];
}

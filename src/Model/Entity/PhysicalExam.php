<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PhysicalExam Entity
 *
 * @property int $id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $exam_date
 * @property int $result
 * @property string $notes
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 */
class PhysicalExam extends Entity
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
        'exam_date' => true,
        'result' => true,
        'notes' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true
    ];
}

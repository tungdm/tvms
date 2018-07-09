<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JclassesStudent Entity
 *
 * @property int $id
 * @property int $jclass_id
 * @property int $student_id
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Jclass $jclass
 * @property \App\Model\Entity\Student $student
 */
class JclassesStudent extends Entity
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
        'jclass_id' => true,
        'student_id' => true,
        'note' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'jclass' => true,
        'student' => true
    ];
}

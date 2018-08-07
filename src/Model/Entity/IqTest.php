<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * IqTest Entity
 *
 * @property int $id
 * @property int $student_id
 * @property \Cake\I18n\FrozenDate $test_date
 * @property int $q1
 * @property int $q2
 * @property int $q3
 * @property int $q4
 * @property int $q5
 * @property int $q6
 * @property int $q7
 * @property int $q8
 * @property int $q9
 * @property int $q10
 * @property int $q11
 * @property int $q12
 * @property int $q13
 * @property int $q14
 * @property int $q15
 * @property int $q16
 * @property int $q17
 * @property int $q18
 * @property int $q19
 * @property int $q20
 * @property int $q21
 * @property int $q22
 * @property int $q23
 * @property int $q24
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 */
class IqTest extends Entity
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
        'test_date' => true,
        'total' => true,
        'q1' => true,
        'q2' => true,
        'q3' => true,
        'q4' => true,
        'q5' => true,
        'q6' => true,
        'q7' => true,
        'q8' => true,
        'q9' => true,
        'q10' => true,
        'q11' => true,
        'q12' => true,
        'q13' => true,
        'q14' => true,
        'q15' => true,
        'q16' => true,
        'q17' => true,
        'q18' => true,
        'q19' => true,
        'q20' => true,
        'q21' => true,
        'q22' => true,
        'q23' => true,
        'q24' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true
    ];
}

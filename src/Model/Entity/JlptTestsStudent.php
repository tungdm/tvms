<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JlptTestsStudent Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $jlpt_test_id
 * @property int $general_score
 * @property int $reading_score
 * @property int $listening_score
 * @property int $total_score
 * @property string $result
 * @property int $flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\JlptTest $jlpt_test
 */
class JlptTestsStudent extends Entity
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
        'jlpt_test_id' => true,
        'general_score' => true,
        'reading_score' => true,
        'listening_score' => true,
        'total_score' => true,
        'result' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true,
        'jlpt_test' => true
    ];
}

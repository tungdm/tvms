<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JtestsStudent Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $jtest_id
 * @property int $vocabulary_score
 * @property int $grammar_score
 * @property int $listening_score
 * @property int $conversation_score
 * @property int $total_score
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Jtest $jtest
 */
class JtestsStudent extends Entity
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
        'id' => true,
        'student_id' => true,
        'jtest_id' => true,
        'vocabulary_score' => true,
        'grammar_score' => true,
        'listening_score' => true,
        'conversation_score' => true,
        'total_score' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true,
        'jtest' => true
    ];
}

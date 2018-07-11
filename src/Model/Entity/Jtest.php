<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Jtest Entity
 *
 * @property int $id
 * @property int $jclass_id
 * @property \Cake\I18n\FrozenDate $test_date
 * @property int $lesson_from
 * @property int $lesson_to
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Jclass $jclass
 * @property \App\Model\Entity\JtestAttendance[] $jtest_attendances
 * @property \App\Model\Entity\JtestContent[] $jtest_content
 */
class Jtest extends Entity
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
        'test_date' => true,
        'lesson_from' => true,
        'lesson_to' => true,
        'status' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'jclass' => true,
        'jtest_attendances' => true,
        'jtest_contents' => true,
        'students' => true
    ];
}

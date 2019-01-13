<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JlptTest Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $test_date
 * @property string $level
 * @property int $status
 * @property bool $del_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\JlptContent[] $jlpt_contents
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\Student[] $students
 */
class JlptTest extends Entity
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
        'test_date' => true,
        'level' => true,
        'status' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'jlpt_contents' => true,
        'events' => true,
        'students' => true,
    ];
}

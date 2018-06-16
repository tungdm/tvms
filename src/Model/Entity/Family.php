<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Family Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $fullname
 * @property \Cake\I18n\FrozenDate $birthday
 * @property int $relationship
 * @property int $job_id
 * @property string $address
 * @property string $bank_num
 * @property string $cmnd_num
 * @property int $phone
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Job $job
 */
class Family extends Entity
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
        'fullname' => true,
        'birthday' => true,
        'relationship' => true,
        'job_id' => true,
        'address' => true,
        'bank_num' => true,
        'cmnd_num' => true,
        'phone' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true,
        'job' => true
    ];
}

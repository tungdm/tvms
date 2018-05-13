<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Profile Entity
 *
 * @property int $id
 * @property int $staff_id
 * @property string $gender
 * @property \Cake\I18n\FrozenDate $birthday
 * @property int $job_id
 * @property string $email
 * @property string $phone
 * @property string $fullname
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\Job $job
 */
class Profile extends Entity
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
        'staff_id' => true,
        'gender' => true,
        'birthday' => true,
        'job_id' => true,
        'email' => true,
        'phone' => true,
        'fullname' => true,
        'status' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'staff' => true,
        'job' => true
    ];

    // protected function _getFullName()
    // {
    //     return $this->_properties['first_name'] . '  ' .
    //         $this->_properties['last_name'];
    // }
}

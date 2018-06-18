<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Student Entity
 *
 * @property int $id
 * @property string $code
 * @property string $fullname
 * @property string $fullname_kata
 * @property string $email
 * @property string $phone
 * @property string $gender
 * @property string $image
 * @property int $job_id
 * @property \Cake\I18n\FrozenDate $birthday
 * @property int $marital_status
 * @property int $subject
 * @property float $height
 * @property float $weight
 * @property string $religion
 * @property string $blood_group
 * @property string $preferred_hand
 * @property string $educational_level
 * @property string $nation
 * @property int $presenter_id
 * @property bool $is_lived_in_japan
 * @property string $expectation
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Job $job
 * @property \App\Model\Entity\Presenter $presenter
 * @property \App\Model\Entity\Address[] $addresses
 */
class Student extends Entity
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
        'code' => true,
        'fullname' => true,
        'fullname_kata' => true,
        'email' => true,
        'phone' => true,
        'gender' => true,
        'image' => true,
        'job_id' => true,
        'birthday' => true,
        'marital_status' => true,
        'subject' => true,
        'height' => true,
        'weight' => true,
        'religion' => true,
        'blood_group' => true,
        'preferred_hand' => true,
        'left_eye_sight' => true,
        'right_eye_sight' => true,
        'left_eye_sight_hospital' => true,
        'right_eye_sight_hospital' => true,
        'color_blind' => true,
        'educational_level' => true,
        'nation' => true,
        'country' => true,
        'presenter_id' => true,
        'is_lived_in_japan' => true,
        'reject_stay' => true,
        'lived_from' => true,
        'lived_to' => true,
        'expectation' => true,
        'status' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'job' => true,
        'presenter' => true,
        'addresses' => true,
        'cards' => true,
        'families' => true,
        'educations' => true,
        'experiences' => true,
        'language_abilities' => true,
    ];
}

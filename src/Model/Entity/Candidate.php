<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Candidate Entity
 *
 * @property int $id
 * @property string $fullname
 * @property string $fb_name
 * @property string $fb_link
 * @property \Cake\I18n\FrozenDate $contact_date
 * @property string $phone
 * @property string $zalo_phone
 * @property string $gender
 * @property string $message
 * @property string $city_id
 * @property \Cake\I18n\FrozenDate $birthday
 * @property string $educational_level
 * @property string $job
 * @property bool $is_potental
 * @property bool $del_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\ConsultantNote[] $consultant_notes
 */
class Candidate extends Entity
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
        'fullname' => true,
        'source' => true,
        'fb_name' => true,
        'fb_link' => true,
        'contact_date' => true,
        'phone' => true,
        'zalo_phone' => true,
        'gender' => true,
        'message' => true,
        'city_id' => true,
        'birthday' => true,
        'educational_level' => true,
        'job' => true,
        'cur_job' => true,
        'status' => true,
        'potential' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'city' => true,
        'consultant_notes' => true
    ];
}

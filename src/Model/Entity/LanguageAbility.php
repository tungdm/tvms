<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LanguageAbility Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $lang_code
 * @property string $certificate
 * @property string $from_date
 * @property string $to_date
 * @property \Cake\I18n\FrozenDate $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenDate $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Student $student
 */
class LanguageAbility extends Entity
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
        'lang_code' => true,
        'certificate' => true,
        'type' => true,
        'from_date' => true,
        'to_date' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'student' => true
    ];
}

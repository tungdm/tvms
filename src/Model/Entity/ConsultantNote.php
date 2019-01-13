<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ConsultantNote Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenDate $consultant_date
 * @property string $note
 * @property bool $del_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Candidate $candidate
 * @property \App\Model\Entity\User $user
 */
class ConsultantNote extends Entity
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
        'candidate_id' => true,
        'user_id' => true,
        'consultant_date' => true,
        'note' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'candidate' => true,
        'user' => true
    ];
}

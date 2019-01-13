<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JlptContent Entity
 *
 * @property int $id
 * @property int $jlpt_test_id
 * @property int $user_id
 * @property int $skill
 * @property int $flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\JlptTest $jlpt_test
 * @property \App\Model\Entity\User $user
 */
class JlptContent extends Entity
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
        'jlpt_test_id' => true,
        'user_id' => true,
        'skill' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'jlpt_test' => true,
        'user' => true
    ];
}

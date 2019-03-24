<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NotificationSetting Entity
 *
 * @property int $id
 * @property string $title
 * @property string $template
 * @property int $receivers_group
 * @property string $exclude
 * @property int $send_before
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\User $created_by_user
 * @property \App\Model\Entity\User $modified_by_user
 */
class NotificationSetting extends Entity
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
        'title' => true,
        'template' => true,
        'receivers_groups' => true,
        'send_before' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'created_by_user' => true,
        'modified_by_user' => true
    ];
}

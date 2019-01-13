<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GuildsCompany Entity
 *
 * @property int $id
 * @property int $guild_id
 * @property int $company_id
 * @property bool $del_flag
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Guild $guild
 * @property \App\Model\Entity\Company $company
 */
class GuildsCompany extends Entity
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
        'guild_id' => true,
        'company_id' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'guild' => true,
        'company' => true
    ];
}

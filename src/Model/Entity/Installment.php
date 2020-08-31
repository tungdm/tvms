<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Installment Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $updated
 * @property int $updated_by
 *
 * @property \App\Model\Entity\Guild[] $guilds
 */
class Installment extends Entity
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
        'name' => true,
        'quarter' => true,
        'quarter_year' => true,
        'admin_company_id' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'installment_fees' => true,
    ];
}

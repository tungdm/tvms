<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GuildsAdminCompany Entity
 *
 * @property int $id
 * @property int $guild_id
 * @property int $admin_company_id
 * @property \Cake\I18n\FrozenDate $signing_date
 * @property float $subsidy
 * @property float $first_three_years_fee
 * @property float $two_years_later_fee
 * @property float $pre_training_fee
 *
 * @property \App\Model\Entity\Guild $guild
 * @property \App\Model\Entity\AdminCompany $admin_company
 */
class GuildsAdminCompany extends Entity
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
        'admin_company_id' => true,
        'signing_date' => true,
        'subsidy' => true,
        'first_three_years_fee' => true,
        'two_years_later_fee' => true,
        'pre_training_fee' => true,
        'guild' => true,
        'admin_company' => true
    ];
}

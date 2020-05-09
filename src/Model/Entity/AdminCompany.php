<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdminCompany Entity
 *
 * @property int $id
 * @property string $alias
 * @property string $name_romaji
 * @property string $name_kanji
 * @property string $address_romaji
 * @property string $address_kanji
 * @property string $license
 * @property string $deputy_name
 * @property string $deputy_role
 * @property string $phone_number
 * @property string $fax_number
 * @property string $email
 * @property \Cake\I18n\FrozenDate $incorporation_date
 * @property int $capital_vn
 * @property int $capital_jp
 * @property int $latest_revenue_per_year
 * @property int $staffs_number
 */
class AdminCompany extends Entity
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
        'alias' => true,
        'short_name' => true,
        'name_vn' => true,
        'name_en' => true,
        'branch_vn' => true,
        'branch_jp' => true,
        'address_vn' => true,
        'address_en' => true,
        'license' => true,
        'license_at' => true,
        'deputy_name' => true,
        'deputy_role_vn' => true,
        'deputy_role_jp' => true,
        'signer_name' => true,
        'signer_role_vn' => true,
        'signer_role_jp' => true,
        'phone_number' => true,
        'fax_number' => true,
        'email' => true,
        'incorporation_date' => true,
        'capital_vn' => true,
        'capital_jp' => true,
        'latest_revenue_vn' => true,
        'latest_revenue_jp' => true,
        'staffs_number' => true,
        'edu_center_name_vn' => true,
        'edu_center_name_jp' => true,
        'edu_center_address_vn' => true,
        'edu_center_address_en' => true,
        'deleted' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true
    ];
}

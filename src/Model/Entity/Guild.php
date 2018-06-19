<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Guild Entity
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modifed
 * @property int $modifed_by
 */
class Guild extends Entity
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
        'name_romaji' => true,
        'name_kanji' => true,
        'phone_vn' => true,
        'phone_jp' => true,
        'address_romaji' => true,
        'address_kanji' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true
    ];
}

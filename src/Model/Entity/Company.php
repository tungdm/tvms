<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Company Entity
 *
 * @property int $id
 * @property string $name_romaji
 * @property string $name_kanji
 * @property string $address_romaji
 * @property string $address_kanji
 * @property string $phone_vn
 * @property string $phone_jp
 * @property int $guild_id
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Guild $guild
 * @property \App\Model\Entity\Order[] $orders
 */
class Company extends Entity
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
        'type' => true,
        'name_kanji' => true,
        'address_romaji' => true,
        'address_kanji' => true,
        'phone_vn' => true,
        'deputy_name_romaji' => true,
        'deputy_name_kanji' => true,
        'phone_jp' => true,
        'del_flag' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'guilds' => true,
        'orders' => true
    ];
}

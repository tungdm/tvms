<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstallmentFee Entity
 *
 * @property int $id
 * @property int $installment_guild_id
 * @property int $management_fee
 * @property int $air_ticket_fee
 * @property int $training_fee
 * @property int $other_fees
 * @property int $total_jp
 * @property int $total_vn
 * @property \Cake\I18n\FrozenDate $invoice_date
 * @property \Cake\I18n\FrozenDate $receiving_money_date
 * @property string $status
 * @property string $notes
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $updated
 * @property int $updated_by
 *
 * @property \App\Model\Entity\InstallmentGuild $installment_guild
 */
class InstallmentFee extends Entity
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
        'installment_id' => true,
        'guild_id' => true,
        'management_fee' => true,
        'air_ticket_fee' => true,
        'training_fee' => true,
        'other_fees' => true,
        'total_jp' => true,
        'total_vn' => true,
        'invoice_date' => true,
        'receiving_money_date' => true,
        'status' => true,
        'notes' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
    ];
}

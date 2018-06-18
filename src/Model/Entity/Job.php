<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Job Entity
 *
 * @property int $id
 * @property string $job_name
 * @property string $description
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Human[] $humans
 */
class Job extends Entity
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
        'job_name' => true,
        'job_name_jp' => true,
        'description' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'humans' => true
    ];
}

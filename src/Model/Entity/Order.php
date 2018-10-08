<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\FrozenDate $interview_date
 * @property float $salary_from
 * @property float $salary_to
 * @property string $interview_type
 * @property string $skill_test
 * @property string $requirement
 * @property string $experience
 * @property int $male_num
 * @property int $female_num
 * @property float $height
 * @property float $weight
 * @property int $age_from
 * @property int $age_to
 * @property string $work_time
 * @property string $work_at
 * @property \Cake\I18n\FrozenDate $departure_date
 * @property int $company_id
 * @property int $job_id
 * @property string $status
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Job $job
 */
class Order extends Entity
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
        'interview_date' => true,
        'salary_from' => true,
        'salary_to' => true,
        'interview_type' => true,
        'skill_test' => true,
        'requirement' => true,
        'experience' => true,
        'male_num' => true,
        'female_num' => true,
        'height' => true,
        'weight' => true,
        'age_from' => true,
        'age_to' => true,
        'work_time' => true,
        'work_at' => true,
        'departure_date' => true,
        'departure' => true,
        'status' => true,
        'dis_company_id' => true,
        'company_id' => true,
        'job_id' => true,
        'application_date' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'company' => true,
        'job' => true,
        'students' => true,
        'events' => true,
    ];
}

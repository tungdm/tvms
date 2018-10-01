<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * Orders Model
 *
 * @property \App\Model\Table\CompaniesTable|\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\JobsTable|\Cake\ORM\Association\BelongsTo $Jobs
 *
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'order_id',
            'dependent' => true,
        ]);
        $this->belongsTo('Jobs', [
            'foreignKey' => 'job_id',
        ]);
        $this->belongsToMany('Students', [
            'through' => 'OrdersStudents'
        ]);

        $this->belongsTo('CreatedByUsers', [
            'foreignKey' => 'created_by',
            'className' => 'Users'
        ]);
        $this->belongsTo('ModifiedByUsers', [
            'foreignKey' => 'modified_by',
            'className' => 'Users'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            // ->date('interview_date')
            ->requirePresence('interview_date', 'create')
            ->notEmpty('interview_date');

        $validator
            ->numeric('salary_from')
            ->allowEmpty('salary_from');

        $validator
            ->numeric('salary_to')
            ->allowEmpty('salary_to');

        $validator
            ->scalar('interview_type')
            ->allowEmpty('interview_type');

        $validator
            ->scalar('skill_test')
            ->allowEmpty('skill_test');

        $validator
            ->scalar('requirement')
            ->allowEmpty('requirement');

        $validator
            ->scalar('experience')
            ->allowEmpty('experience');

        $validator
            ->integer('male_num')
            ->allowEmpty('male_num');

        $validator
            ->integer('female_num')
            ->allowEmpty('female_num');

        $validator
            ->numeric('height')
            ->allowEmpty('height');

        $validator
            ->numeric('weight')
            ->allowEmpty('weight');

        $validator
            ->integer('age_from')
            ->allowEmpty('age_from');

        $validator
            ->integer('age_to')
            ->allowEmpty('age_to');

        $validator
            ->scalar('work_time')
            ->maxLength('work_time', 255)
            ->requirePresence('work_time', 'create')
            ->notEmpty('work_time');

        $validator
            ->scalar('work_at')
            ->maxLength('work_at', 255)
            ->requirePresence('work_at', 'create')
            ->notEmpty('work_at');

        $validator
            ->scalar('departure_date')
            ->allowEmpty('departure_date');
        $validator
            ->scalar('application_date')
            ->allowEmpty('application_date');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmpty('modified_by');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['job_id'], 'Jobs'));

        return $rules;
    }

    public function findWithStatus(Query $query, array $options)
    {
        $now = Time::now()->i18nFormat('yyyy-MM-dd');
        $query->formatResults(function ($results) use ($now) {
            return $results->map(function ($row) use ($now) {
                if ($now < $row['interview_date']) {
                    $row['status'] = "1";
                } elseif ($now == $row['interview_date']) {
                    $row['status'] = "2";
                } else {
                    $row['status'] = "3";
                }
                return $row;
            });
        });
        return $query;
    }
}

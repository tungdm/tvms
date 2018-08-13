<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Experiences Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\JobsTable|\Cake\ORM\Association\BelongsTo $Jobs
 *
 * @method \App\Model\Entity\Experience get($primaryKey, $options = [])
 * @method \App\Model\Entity\Experience newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Experience[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Experience|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Experience patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Experience[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Experience findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ExperiencesTable extends Table
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

        $this->setTable('experiences');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Jobs', [
            'foreignKey' => 'job_id',
            'joinType' => 'INNER'
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
            ->scalar('from_date')
            ->maxLength('from_date', 255)
            ->requirePresence('from_date', 'create')
            ->notEmpty('from_date');

        $validator
            ->scalar('to_date')
            ->maxLength('to_date', 255)
            ->requirePresence('to_date', 'create')
            ->notEmpty('to_date');

        $validator
            ->scalar('salary')
            ->maxLength('salary', 255)
            ->allowEmpty('salary');

        $validator
            ->scalar('company')
            ->maxLength('company', 255)
            ->requirePresence('company', 'create')
            ->notEmpty('company');

        $validator
            ->scalar('company_jp')
            ->maxLength('company_jp', 255)
            ->allowEmpty('company_jp');

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->allowEmpty('address');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['job_id'], 'Jobs'));

        return $rules;
    }
}

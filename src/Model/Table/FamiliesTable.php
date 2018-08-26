<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Families Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\JobsTable|\Cake\ORM\Association\BelongsTo $Jobs
 *
 * @method \App\Model\Entity\Family get($primaryKey, $options = [])
 * @method \App\Model\Entity\Family newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Family[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Family|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Family patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Family[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Family findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FamiliesTable extends Table
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

        $this->setTable('families');
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
            ->scalar('fullname')
            ->maxLength('fullname', 255)
            ->requirePresence('fullname', 'create')
            ->notEmpty('fullname');

        $validator
            // ->date('birthday')
            ->allowEmpty('birthday');

        $validator
            ->integer('relationship')
            ->requirePresence('relationship', 'create')
            ->notEmpty('relationship');

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->allowEmpty('address');

        $validator
            ->scalar('bank_num')
            ->maxLength('bank_num', 50)
            ->allowEmpty('bank_num');

        $validator
            ->scalar('cmnd_num')
            ->maxLength('cmnd_num', 50)
            ->allowEmpty('cmnd_num');

        $validator
            ->scalar('phone')
            ->maxLength('cmnd_num', 11)
            ->allowEmpty('phone');

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

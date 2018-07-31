<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IqTests Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 *
 * @method \App\Model\Entity\IqTest get($primaryKey, $options = [])
 * @method \App\Model\Entity\IqTest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\IqTest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\IqTest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\IqTest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\IqTest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\IqTest findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class IqTestsTable extends Table
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

        $this->setTable('iq_tests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
            ->date('test_date')
            ->allowEmpty('test_date');

        $validator
            ->integer('q1')
            ->allowEmpty('q1');

        $validator
            ->integer('q2')
            ->allowEmpty('q2');

        $validator
            ->integer('q3')
            ->allowEmpty('q3');

        $validator
            ->integer('q4')
            ->allowEmpty('q4');

        $validator
            ->integer('q5')
            ->allowEmpty('q5');

        $validator
            ->integer('q6')
            ->allowEmpty('q6');

        $validator
            ->integer('q7')
            ->allowEmpty('q7');

        $validator
            ->integer('q8')
            ->allowEmpty('q8');

        $validator
            ->integer('q9')
            ->allowEmpty('q9');

        $validator
            ->integer('q10')
            ->allowEmpty('q10');

        $validator
            ->integer('q11')
            ->allowEmpty('q11');

        $validator
            ->integer('q12')
            ->allowEmpty('q12');

        $validator
            ->integer('q13')
            ->allowEmpty('q13');

        $validator
            ->integer('q14')
            ->allowEmpty('q14');

        $validator
            ->integer('q15')
            ->allowEmpty('q15');

        $validator
            ->integer('q16')
            ->allowEmpty('q16');

        $validator
            ->integer('q17')
            ->allowEmpty('q17');

        $validator
            ->integer('q18')
            ->allowEmpty('q18');

        $validator
            ->integer('q19')
            ->allowEmpty('q19');

        $validator
            ->integer('q20')
            ->allowEmpty('q20');

        $validator
            ->integer('q21')
            ->allowEmpty('q21');

        $validator
            ->integer('q22')
            ->allowEmpty('q22');

        $validator
            ->integer('q23')
            ->allowEmpty('q23');

        $validator
            ->integer('q24')
            ->allowEmpty('q24');

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

        return $rules;
    }
}

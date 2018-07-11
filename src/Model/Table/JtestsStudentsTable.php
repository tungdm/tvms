<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JtestsStudents Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\JtestsTable|\Cake\ORM\Association\BelongsTo $Jtests
 *
 * @method \App\Model\Entity\JtestsStudent get($primaryKey, $options = [])
 * @method \App\Model\Entity\JtestsStudent newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JtestsStudent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JtestsStudent|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JtestsStudent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JtestsStudent[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JtestsStudent findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class JtestsStudentsTable extends Table
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

        $this->setTable('jtests_students');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Jtests', [
            'foreignKey' => 'jtest_id',
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
            ->requirePresence('id', 'create')
            ->notEmpty('id');

        $validator
            ->integer('vocabulary_score')
            ->allowEmpty('vocabulary_score');

        $validator
            ->integer('grammar_score')
            ->allowEmpty('grammar_score');

        $validator
            ->integer('listening_score')
            ->allowEmpty('listening_score');

        $validator
            ->integer('conversation_score')
            ->allowEmpty('conversation_score');

        $validator
            ->integer('total_score')
            ->allowEmpty('total_score');

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
        $rules->add($rules->existsIn(['jtest_id'], 'Jtests'));

        return $rules;
    }
}

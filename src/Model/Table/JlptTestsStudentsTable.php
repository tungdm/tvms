<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JlptTestsStudents Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\JlptTestsTable|\Cake\ORM\Association\BelongsTo $JlptTests
 *
 * @method \App\Model\Entity\JlptTestsStudent get($primaryKey, $options = [])
 * @method \App\Model\Entity\JlptTestsStudent newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JlptTestsStudent[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JlptTestsStudent|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JlptTestsStudent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JlptTestsStudent[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JlptTestsStudent findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class JlptTestsStudentsTable extends Table
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

        $this->setTable('jlpt_tests_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id'
        ]);
        $this->belongsTo('JlptTests', [
            'foreignKey' => 'jlpt_test_id'
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
            ->integer('general_score')
            ->allowEmpty('general_score');

        $validator
            ->integer('reading_score')
            ->allowEmpty('reading_score');

        $validator
            ->integer('listening_score')
            ->allowEmpty('listening_score');

        $validator
            ->integer('total_score')
            ->allowEmpty('total_score');

        $validator
            ->scalar('result')
            ->maxLength('result', 11)
            ->allowEmpty('result');

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
        $rules->add($rules->existsIn(['jlpt_test_id'], 'JlptTests'));

        return $rules;
    }
}

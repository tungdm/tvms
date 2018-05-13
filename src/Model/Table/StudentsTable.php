<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Students Model
 *
 * @method \App\Model\Entity\Student get($primaryKey, $options = [])
 * @method \App\Model\Entity\Student newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Student[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Student|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Student patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Student[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Student findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudentsTable extends Table
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

        $this->setTable('students');
        $this->setDisplayField('human_id');
        $this->setPrimaryKey('human_id');

        $this->addBehavior('Timestamp');
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
            ->integer('human_id')
            ->allowEmpty('human_id', 'create');

        $validator
            ->scalar('fullname_kata')
            ->maxLength('fullname_kata', 255)
            ->requirePresence('fullname_kata', 'create')
            ->notEmpty('fullname_kata');

        $validator
            ->boolean('is_marrired')
            ->allowEmpty('is_marrired');

        $validator
            ->numeric('height')
            ->allowEmpty('height');

        $validator
            ->numeric('weight')
            ->allowEmpty('weight');

        $validator
            ->scalar('religion')
            ->maxLength('religion', 255)
            ->allowEmpty('religion');

        $validator
            ->scalar('blood_group')
            ->maxLength('blood_group', 50)
            ->allowEmpty('blood_group');

        $validator
            ->scalar('preferred_hand')
            ->maxLength('preferred_hand', 255)
            ->allowEmpty('preferred_hand');

        $validator
            ->scalar('educational_level')
            ->maxLength('educational_level', 255)
            ->allowEmpty('educational_level');

        $validator
            ->scalar('nation')
            ->maxLength('nation', 255)
            ->allowEmpty('nation');

        $validator
            ->integer('presenter')
            ->allowEmpty('presenter');

        $validator
            ->scalar('work_experience')
            ->allowEmpty('work_experience');

        $validator
            ->boolean('is_lived_in_japan')
            ->allowEmpty('is_lived_in_japan');

        $validator
            ->scalar('expectation')
            ->maxLength('expectation', 255)
            ->allowEmpty('expectation');

        $validator
            ->scalar('purpose_before')
            ->allowEmpty('purpose_before');

        $validator
            ->scalar('purpose_after')
            ->allowEmpty('purpose_after');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->dateTime('modifed')
            ->allowEmpty('modifed');

        $validator
            ->integer('modifed_by')
            ->allowEmpty('modifed_by');

        return $validator;
    }
}

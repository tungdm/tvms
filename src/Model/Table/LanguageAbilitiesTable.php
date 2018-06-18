<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LanguageAbilities Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 *
 * @method \App\Model\Entity\LanguageAbility get($primaryKey, $options = [])
 * @method \App\Model\Entity\LanguageAbility newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LanguageAbility[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LanguageAbility|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LanguageAbility patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LanguageAbility[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LanguageAbility findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LanguageAbilitiesTable extends Table
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

        $this->setTable('language_abilities');
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
            ->scalar('lang_code')
            ->maxLength('lang_code', 2)
            ->requirePresence('lang_code', 'create')
            ->notEmpty('lang_code');

        $validator
            ->scalar('certificate')
            ->maxLength('certificate', 255)
            ->allowEmpty('certificate');

        $validator
            ->scalar('from_date')
            ->maxLength('from_date', 255)
            ->allowEmpty('from_date');

        $validator
            ->scalar('to_date')
            ->maxLength('to_date', 255)
            ->allowEmpty('to_date');

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

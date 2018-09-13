<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\Rule\ExistsIn;
use Cake\Validation\Validator;

/**
 * Addresses Model
 *
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsTo $Students
 * @property |\Cake\ORM\Association\BelongsTo $Wards
 * @property |\Cake\ORM\Association\BelongsTo $Districts
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\Address get($primaryKey, $options = [])
 * @method \App\Model\Entity\Address newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Address[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Address|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Address patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Address[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Address findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AddressesTable extends Table
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

        $this->setTable('addresses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Wards', [
            'foreignKey' => 'ward_id'
        ]);
        $this->belongsTo('Districts', [
            'foreignKey' => 'district_id'
        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id'
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
            ->integer('type')
            ->allowEmpty('type');

        $validator
            ->scalar('street')
            ->maxLength('street', 255)
            ->allowEmpty('street');

        $validator
            ->scalar('city_id')
            ->allowEmpty('city_id');

        $validator
            ->scalar('district_id')
            ->allowEmpty('district_id');

        $validator
            ->scalar('ward_id')
            ->allowEmpty('ward_id');

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

        // $rules->add($rules->existsIn(['ward_id'], 'Wards'));
        $rules->add(function ($entity, $options) {
            $rule = new ExistsIn(['ward_id'], 'Wards');
            return $entity->ward_id === '' || $rule($entity, $options);
        });

        // $rules->add($rules->existsIn(['district_id'], 'Districts'));
        $rules->add(function ($entity, $options) {
            $rule = new ExistsIn(['district_id'], 'Districts');
            return $entity->district_id === '' || $rule($entity, $options);
        });

        // $rules->add($rules->existsIn(['city_id'], 'Cities'));
        $rules->add(function ($entity, $options) {
            $rule = new ExistsIn(['city_id'], 'Cities');
            return $entity->city_id === '' || $rule($entity, $options);
        });

        return $rules;
    }
}

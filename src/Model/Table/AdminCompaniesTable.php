<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminCompanies Model
 *
 * @method \App\Model\Entity\AdminCompany get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdminCompany newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdminCompany[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdminCompany|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdminCompany patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdminCompany[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdminCompany findOrCreate($search, callable $callback = null, $options = [])
 */
class AdminCompaniesTable extends Table
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

        $this->setTable('admin_companies');
        $this->setDisplayField('alias');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

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
            ->scalar('alias')
            ->maxLength('alias', 255)
            ->allowEmpty('alias');

        $validator
            ->scalar('name_vn')
            ->maxLength('name_vn', 255)
            ->allowEmpty('name_vn');

        $validator
            ->scalar('name_en')
            ->maxLength('name_en', 255)
            ->allowEmpty('name_en');

        $validator
            ->scalar('address_vn')
            ->allowEmpty('address_vn');

        $validator
            ->scalar('address_en')
            ->allowEmpty('address_en');

        $validator
            ->scalar('license')
            ->maxLength('license', 255)
            ->allowEmpty('license');

        $validator
            ->scalar('deputy_name')
            ->maxLength('deputy_name', 255)
            ->allowEmpty('deputy_name');

        $validator
            ->scalar('deputy_role_vn')
            ->maxLength('deputy_role_vn', 255)
            ->allowEmpty('deputy_role_vn');
        
        $validator
            ->scalar('deputy_role_jp')
            ->maxLength('deputy_role_jp', 255)
            ->allowEmpty('deputy_role_jp');

        $validator
            ->scalar('signer')
            ->maxLength('signer', 255)
            ->allowEmpty('signer');
        $validator
            ->scalar('phone_number')
            ->maxLength('phone_number', 255)
            ->allowEmpty('phone_number');

        $validator
            ->scalar('fax_number')
            ->maxLength('fax_number', 255)
            ->allowEmpty('fax_number');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->numeric('capital_vn')
            ->allowEmpty('capital_vn');

        $validator
            ->numeric('capital_jp')
            ->allowEmpty('capital_jp');

        $validator
            ->numeric('latest_revenue_vn')
            ->allowEmpty('latest_revenue_vn');

        $validator
            ->numeric('latest_revenue_jp')
            ->allowEmpty('latest_revenue_jp');

        $validator
            ->integer('staffs_number')
            ->allowEmpty('staffs_number');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GuildsAdminCompanies Model
 *
 * @property \App\Model\Table\GuildsTable|\Cake\ORM\Association\BelongsTo $Guilds
 * @property \App\Model\Table\AdminCompaniesTable|\Cake\ORM\Association\BelongsTo $AdminCompanies
 *
 * @method \App\Model\Entity\GuildsAdminCompany get($primaryKey, $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GuildsAdminCompany findOrCreate($search, callable $callback = null, $options = [])
 */
class GuildsAdminCompaniesTable extends Table
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

        $this->setTable('guilds_admin_companies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Guilds', [
            'foreignKey' => 'guild_id'
        ]);
        $this->belongsTo('AdminCompanies', [
            'foreignKey' => 'admin_company_id'
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
            ->date('signing_date')
            ->allowEmpty('signing_date');

        $validator
            ->numeric('subsidy')
            ->allowEmpty('subsidy');

        $validator
            ->numeric('first_three_years_fee')
            ->allowEmpty('first_three_years_fee');

        $validator
            ->numeric('two_years_later_fee')
            ->allowEmpty('two_years_later_fee');

        $validator
            ->numeric('pre_training_fee')
            ->allowEmpty('pre_training_fee');

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
        $rules->add($rules->existsIn(['guild_id'], 'Guilds'));
        $rules->add($rules->existsIn(['admin_company_id'], 'AdminCompanies'));

        return $rules;
    }
}

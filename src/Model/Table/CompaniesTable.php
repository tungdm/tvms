<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Companies Model
 *
 * @property \App\Model\Table\GuildsTable|\Cake\ORM\Association\BelongsTo $Guilds
 * @property \App\Model\Table\OrdersTable|\Cake\ORM\Association\HasMany $Orders
 *
 * @method \App\Model\Entity\Company get($primaryKey, $options = [])
 * @method \App\Model\Entity\Company newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Company[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Company|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Company patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Company[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Company findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CompaniesTable extends Table
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

        $this->setTable('companies');
        $this->setDisplayField('name_romaji');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Guilds', [
            'foreignKey' => 'guild_id'
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'company_id',
            'dependent' => true,
        ]);

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
            ->scalar('name_romaji')
            ->maxLength('name_romaji', 255)
            ->requirePresence('name_romaji', 'create')
            ->notEmpty('name_romaji');

        $validator
            ->scalar('name_kanji')
            ->maxLength('name_kanji', 255)
            ->allowEmpty('name_kanji');

        $validator
            ->scalar('address_romaji')
            ->maxLength('address_romaji', 255)
            ->allowEmpty('address_romaji');

        $validator
            ->scalar('address_kanji')
            ->maxLength('address_kanji', 255)
            ->allowEmpty('address_kanji');

        $validator
            ->scalar('phone_vn')
            ->maxLength('phone_vn', 11)
            ->allowEmpty('phone_vn');

        $validator
            ->scalar('phone_jp')
            ->maxLength('phone_jp', 255)
            ->allowEmpty('phone_jp');

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
        $rules->add($rules->existsIn(['guild_id'], 'Guilds'));

        return $rules;
    }
}

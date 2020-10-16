<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Guilds Model
 *
 * @method \App\Model\Entity\Guild get($primaryKey, $options = [])
 * @method \App\Model\Entity\Guild newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Guild[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Guild|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Guild patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Guild[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Guild findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GuildsTable extends Table
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

        $this->setTable('guilds');
        $this->setDisplayField('name_romaji');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->hasMany('InstallmentFees', [
            'foreignKey' => 'guild_id',
            'dependent' => true
        ]);
        
        $this->belongsToMany('Companies', [
            'foreignKey' => 'guild_id',
            'targetForeignKey' => 'company_id',
            'joinTable' => 'guilds_companies',
        ]);
        
        $this->belongsToMany('AdminCompanies', [
            'foreignKey' => 'guild_id',
            'targetForeignKey' => 'admin_company_id',
            'joinTable' => 'guilds_admin_companies',
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
            ->scalar('phone_vn')
            ->maxLength('phone_vn', 11)
            ->allowEmpty('phone_vn');

        $validator
            ->scalar('phone_jp')
            ->maxLength('phone_jp', 255)
            ->allowEmpty('phone_jp');

        $validator
            ->scalar('address_romaji')
            ->maxLength('address_romaji', 255)
            ->allowEmpty('address_romaji');

        $validator
            ->scalar('address_kanji')
            ->maxLength('address_kanji', 255)
            ->allowEmpty('address_kanji');    
            
        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->dateTime('modified')
            ->allowEmpty('modifed');

        $validator
            ->integer('modified_by')
            ->allowEmpty('modified_by');

        return $validator;
    }
}

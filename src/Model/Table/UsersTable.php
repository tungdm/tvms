<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\RolesTable|\Cake\ORM\Association\BelongsTo $Roles
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
        ]);
        $this->hasOne('Profiles', [
            'foreignKey' => 'user_id',
            'propertyName' => 'profile',
            'dependent' => true
        ]);
        $this->hasMany('Permissions', [
            'foreignKey' => 'user_id',
            'propertyName' => 'permissions',
            'dependent' => true
        ]);
        $this->hasMany('RememberMeTokens', [
            'foreignKey' => 'user_id',
            'dependent' => true,
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
            ->scalar('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmpty('username');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->allowEmpty('scope');

        $validator
            ->scalar('avatar')
            ->maxLength('avatar', 255)
            ->allowEmpty('avatar');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }

    public function findAuth(Query $query, array $options)
    {
        $query
            ->select(['id', 'username', 'password', 'avatar', 'Roles.name', 'Profiles.email'])
            ->contain(['Profiles', 'Roles', 'Permissions']);

        return $query;
    }

    // public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) 
    // {
    //     $entity->created_by = 1;
    // }
}

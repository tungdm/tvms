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
        $this->setDisplayField('fullname');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
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
        $this->hasMany('Events', [
            'foreignKey' => 'user_id',
            'dependent' => true,
        ]);
        $this->hasMany('Jclasses', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('HistoriesCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Histories'
        ]);
        $this->hasMany('HistoriesModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Histories'
        ]);

        $this->hasMany('StudentsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Students'
        ]);
        $this->hasMany('StudentsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Students'
        ]);

        $this->hasMany('OrdersCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Orders'
        ]);
        $this->hasMany('OrdersModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Orders'
        ]);

        $this->hasMany('JclassesCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Jclasses'
        ]);
        $this->hasMany('JclassesModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Jclasses'
        ]);

        $this->hasMany('JtestsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Jtests'
        ]);
        $this->hasMany('JtestsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Jtests'
        ]);

        $this->hasMany('JlptTestsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'JlptTests'
        ]);
        $this->hasMany('JlptTestsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'JlptTests'
        ]);

        $this->hasMany('GuildsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Guilds'
        ]);
        $this->hasMany('GuildsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Guilds'
        ]);

        $this->hasMany('CompaniesCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Companies'
        ]);
        $this->hasMany('CompaniesModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Companies'
        ]);

        $this->hasMany('PresentersCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Presenters'
        ]);
        $this->hasMany('PresentersModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Presenters'
        ]);

        $this->hasMany('JobsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Jobs'
        ]);
        $this->hasMany('JobsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Jobs'
        ]);

        $this->hasMany('CharsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Characteristics'
        ]);
        $this->hasMany('CharsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Characteristics'
        ]);

        $this->hasMany('StrengthsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Strengths'
        ]);
        $this->hasMany('StrengthsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Strengths'
        ]);

        $this->hasMany('PurposesCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'Purposes'
        ]);
        $this->hasMany('PurposesModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'Purposes'
        ]);

        $this->hasMany('AfterPlansCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'AfterPlans'
        ]);
        $this->hasMany('AfterPlansModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'AfterPlans'
        ]);

        $this->hasMany('NotificationSettingsCreatedBy', [
            'foreignKey' => 'created_by',
            'className' => 'NotificationSettings'
        ]);
        $this->hasMany('NotificationSettingsModifiedBy', [
            'foreignKey' => 'modified_by',
            'className' => 'NotificationSettings'
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
            ->allowEmpty('password');

        $validator
            ->allowEmpty('scope');

        $validator
            ->allowEmpty('image');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 2)
            ->allowEmpty('gender');

        $validator
            // ->date('birthday')
            ->allowEmpty('birthday');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 11)
            ->allowEmpty('phone');

        $validator
            ->scalar('fullname')
            ->maxLength('fullname', 255)
            ->allowEmpty('fullname');

        $validator
            ->integer('status')
            ->allowEmpty('status');

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
            ->select(['id', 'username', 'fullname', 'password', 'image', 'role_id', 'Roles.name', 'email', 'del_flag'])
            ->contain(['Roles', 'Permissions']);

        return $query;
    }

    public function findPassword(Query $query, array $options)
    {
        $userId = $options['userId'];
        return $query->where(['id' => $userId])->select(['password']);
    }
}

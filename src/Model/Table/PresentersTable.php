<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Presenters Model
 *
 * @property |\Cake\ORM\Association\HasMany $Students
 *
 * @method \App\Model\Entity\Presenter get($primaryKey, $options = [])
 * @method \App\Model\Entity\Presenter newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Presenter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Presenter|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Presenter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Presenter[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Presenter findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PresentersTable extends Table
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

        $this->setTable('presenters');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->hasMany('Students', [
            'foreignKey' => 'presenter_id'
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->allowEmpty('address');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 11)
            ->allowEmpty('phone');

        $validator
            ->scalar('type')
            ->maxLength('type', 2)
            ->allowEmpty('type');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmpty('modified_by');

        return $validator;
    }
}

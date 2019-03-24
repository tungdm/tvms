<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NotificationSettings Model
 *
 * @method \App\Model\Entity\NotificationSetting get($primaryKey, $options = [])
 * @method \App\Model\Entity\NotificationSetting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NotificationSetting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NotificationSetting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotificationSetting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NotificationSetting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NotificationSetting findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotificationSettingsTable extends Table
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

        $this->setTable('notification_settings');
        $this->setDisplayField('title');
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
            ->scalar('title')
            ->maxLength('title', 250)
            ->allowEmpty('title');

        $validator
            ->scalar('template')
            ->allowEmpty('template');

        $validator
            ->integer('send_before')
            ->allowEmpty('send_before');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmpty('modified_by');

        return $validator;
    }
}

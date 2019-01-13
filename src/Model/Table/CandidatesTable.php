<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Candidates Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\ConsultantNotesTable|\Cake\ORM\Association\HasMany $ConsultantNotes
 *
 * @method \App\Model\Entity\Candidate get($primaryKey, $options = [])
 * @method \App\Model\Entity\Candidate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Candidate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Candidate|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Candidate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Candidate findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CandidatesTable extends Table
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

        $this->setTable('candidates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('ConsultantNotes', [
            'foreignKey' => 'candidate_id'
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
            ->scalar('fullname')
            ->maxLength('fullname', 255)
            ->allowEmpty('fullname');

        $validator
            ->scalar('fb_name')
            ->maxLength('fb_name', 255)
            ->allowEmpty('fb_name');

        $validator
            ->scalar('fb_link')
            ->maxLength('fb_link', 255)
            ->allowEmpty('fb_link');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 11)
            ->allowEmpty('phone');

        $validator
            ->scalar('zalo_phone')
            ->maxLength('zalo_phone', 11)
            ->allowEmpty('zalo_phone');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 2)
            ->allowEmpty('gender');

        $validator
            ->scalar('message')
            ->allowEmpty('message');

        $validator
            ->scalar('educational_level')
            ->maxLength('educational_level', 255)
            ->allowEmpty('educational_level');

        $validator
            ->scalar('job')
            ->maxLength('job', 500)
            ->allowEmpty('job');


        $validator
            ->boolean('del_flag')
            ->allowEmpty('del_flag');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmpty('modified_by');

        return $validator;
    }
}

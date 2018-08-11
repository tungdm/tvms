<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Students Model
 *
 * @property \App\Model\Table\JobsTable|\Cake\ORM\Association\BelongsTo $Jobs
 * @property \App\Model\Table\PresentersTable|\Cake\ORM\Association\BelongsTo $Presenters
 * @property \App\Model\Table\AddressesTable|\Cake\ORM\Association\HasMany $Addresses
 *
 * @method \App\Model\Entity\Student get($primaryKey, $options = [])
 * @method \App\Model\Entity\Student newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Student[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Student|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Student patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Student[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Student findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudentsTable extends Table
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

        $this->setTable('students');
        $this->setDisplayField('fullname');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->belongsTo('Jobs', [
            'foreignKey' => 'job_id'
        ]);
        $this->hasMany('Addresses', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Cards', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Families', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Educations', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Experiences', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('LanguageAbilities', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('InputTests', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('IqTests', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Documents', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->hasMany('Histories', [
            'foreignKey' => 'student_id',
            'dependent' => true,
        ]);
        $this->belongsTo('Presenters', [
            'foreignKey' => 'presenter_id',
        ]);
        $this->belongsToMany('Orders', [
            'through' => 'OrdersStudents',
            'dependent' => true,
        ]);
        $this->belongsToMany('Jclasses', [
            'through' => 'JclassesStudents',
            'dependent' => true,
        ]);
        $this->belongsToMany('Jtests', [
            'through' => 'JtestsStudents',
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
            ->scalar('fullname')
            ->maxLength('fullname', 255)
            ->allowEmpty('fullname');

        $validator
            ->scalar('fullname_kata')
            ->maxLength('fullname_kata', 255)
            ->allowEmpty('fullname_kata');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 11)
            ->allowEmpty('phone');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 2)
            ->allowEmpty('gender');

        $validator
            ->allowEmpty('image');

        $validator
            ->date('birthday')
            ->allowEmpty('birthday');

        $validator
            ->integer('marital_status')
            ->allowEmpty('marital_status');

        $validator
            ->integer('subject')
            ->allowEmpty('subject');

        $validator
            ->numeric('height')
            ->allowEmpty('height');

        $validator
            ->numeric('weight')
            ->allowEmpty('weight');

        $validator
            ->scalar('religion')
            ->maxLength('religion', 255)
            ->allowEmpty('religion');

        $validator
            ->scalar('blood_group')
            ->maxLength('blood_group', 50)
            ->allowEmpty('blood_group');

        $validator
            ->scalar('preferred_hand')
            ->maxLength('preferred_hand', 255)
            ->allowEmpty('preferred_hand');

        $validator
            ->integer('left_eye_sight')
            ->allowEmpty('left_eye_sight');

        $validator
            ->integer('right_eye_sight')
            ->allowEmpty('right_eye_sight');

        $validator
            ->integer('left_eye_sight_hospital')
            ->allowEmpty('left_eye_sight_hospital');

        $validator
            ->integer('right_eye_sight_hospital')
            ->allowEmpty('right_eye_sight_hospital');

        $validator
            ->scalar('color_blind')
            ->maxLength('color_blind', 255)
            ->allowEmpty('color_blind');

        $validator
            ->scalar('educational_level')
            ->maxLength('educational_level', 255)
            ->allowEmpty('educational_level');

        $validator
            ->scalar('nation')
            ->maxLength('nation', 255)
            ->allowEmpty('nation');
        
        $validator
            ->scalar('country')
            ->maxLength('country', 255)
            ->allowEmpty('country');

        $validator
            ->scalar('is_lived_in_japan')
            ->maxLength('is_lived_in_japan', 2)
            ->allowEmpty('is_lived_in_japan');
        
        $validator
            ->scalar('reject_stay')
            ->maxLength('reject_stay', 2)
            ->allowEmpty('reject_stay');

        $validator
            ->scalar('lived_from')
            ->maxLength('lived_from', 255)
            ->allowEmpty('lived_from');

        $validator
            ->scalar('lived_to')
            ->maxLength('lived_to', 255)
            ->allowEmpty('lived_to');

        $validator
            ->allowEmpty('expectation');

        $validator
            ->integer('status')
            ->allowEmpty('status');
        
        $validator
            ->date('enrolled_date')
            ->allowEmpty('enrolled_date');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->dateTime('modified')
            ->allowEmpty('modified');

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
        $rules->add($rules->existsIn(['job_id'], 'Jobs'));

        return $rules;
    }
}

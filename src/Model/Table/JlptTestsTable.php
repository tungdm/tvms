<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * JlptTests Model
 *
 * @property \App\Model\Table\EventsTable|\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\JlptContentsTable|\Cake\ORM\Association\HasMany $JlptContents
 * @property \App\Model\Table\StudentsTable|\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\JlptTest get($primaryKey, $options = [])
 * @method \App\Model\Entity\JlptTest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\JlptTest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\JlptTest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\JlptTest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\JlptTest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\JlptTest findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class JlptTestsTable extends Table
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

        $this->setTable('jlpt_tests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Author');

        $this->hasMany('Events', [
            'foreignKey' => 'jlpt_test_id',
            'dependent' => true,
        ]);
        $this->hasMany('JlptContents', [
            'foreignKey' => 'jlpt_test_id',
            'dependent' => true,
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'jlpt_test_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'jlpt_tests_students'
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
            // ->date('test_date')
            ->allowEmpty('test_date');

        $validator
            ->scalar('level')
            ->maxLength('level', 8)
            ->allowEmpty('level');

        $validator
            ->integer('status')
            ->allowEmpty('status');

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

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Jtests Model
 *
 * @property \App\Model\Table\JclassesTable|\Cake\ORM\Association\BelongsTo $Jclasses
 * @property |\Cake\ORM\Association\HasMany $JtestContents
 * @property |\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\Jtest get($primaryKey, $options = [])
 * @method \App\Model\Entity\Jtest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Jtest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Jtest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Jtest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Jtest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Jtest findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class JtestsTable extends Table
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

        $this->setTable('jtests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Jclasses', [
            'foreignKey' => 'jclass_id',
        ]);
        $this->hasMany('JtestContents', [
            'foreignKey' => 'jtest_id',
            'dependent' => true,
        ]);
        $this->belongsToMany('Students', [
            'through' => 'JtestsStudents',
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'jtest_id',
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
            ->date('test_date')
            ->requirePresence('test_date', 'create')
            ->notEmpty('test_date');

        $validator
            ->integer('lesson_from')
            ->requirePresence('lesson_from', 'create')
            ->notEmpty('lesson_from');

        $validator
            ->integer('lesson_to')
            ->requirePresence('lesson_to', 'create')
            ->notEmpty('lesson_to');

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
        $rules->add($rules->existsIn(['jclass_id'], 'Jclasses'));

        return $rules;
    }
}

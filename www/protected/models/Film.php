<?php

/**
 * This is the model class for table "film".
 *
 * The followings are the available columns in table 'film':
 *
 * The followings are the available model relations:
 * @property Session[] $sessions
 */
class Film extends BaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Film the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'film';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'sessions' => array(self::HAS_MANY, 'Session', 'film_id'),
		);
	}
}
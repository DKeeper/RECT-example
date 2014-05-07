<?php

/**
 * This is the model class for table "cinema".
 *
 * The followings are the available columns in table 'cinema':
 *
 * The followings are the available model relations:
 * @property CinemaHall[] $cinemaHalls
 */
class Cinema extends BaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Cinema the static model class
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
		return 'cinema';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'cinemaHalls' => array(self::HAS_MANY, 'CinemaHall', 'cinema_id'),
		);
	}
}
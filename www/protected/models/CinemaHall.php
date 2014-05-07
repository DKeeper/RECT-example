<?php

/**
 * This is the model class for table "cinema_hall".
 *
 * The followings are the available columns in table 'cinema_hall':
 * @property integer $cinema_id
 * @property string $number
 * @property integer $seats
 *
 * The followings are the available model relations:
 * @property Cinema $cinema
 * @property Session[] $sessions
 */
class CinemaHall extends BaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CinemaHall the static model class
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
		return 'cinema_hall';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
        $purifier = new CHtmlPurifier();
        $purifier->options = array(
            'HTML.AllowedElements' => array
            (
                'strong'    => TRUE,
                'em'        => TRUE,
                'ul'        => TRUE,
                'ol'        => TRUE,
                'li'        => TRUE,
                'p'         => TRUE,
                'span'      => TRUE,
            ),
            'HTML.AllowedAttributes' => array
            (
                '*.style'   => TRUE,
                '*.title'   => TRUE,
            ),
        );
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('description', 'filter', 'filter' => array($purifier, 'purify')),
			array('cinema_id, number, seats', 'required'),
			array('cinema_id, seats', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cinema_id, number, seats, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'cinema' => array(self::BELONGS_TO, 'Cinema', 'cinema_id'),
			'sessions' => array(self::HAS_MANY, 'Session', 'hall_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cinema_id' => 'Cinema',
			'number' => 'Number',
			'seats' => 'Seats',
			'description' => 'Description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('cinema_id',$this->cinema_id);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('seats',$this->seats);
		$criteria->compare('description',$this->description,true);

        return $this->getSearchDataProvider($this,$criteria);
	}
}
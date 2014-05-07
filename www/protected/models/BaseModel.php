<?php
/**
 * @author Капенкин Дмитрий <dkapenkin@rambler.ru>
 * @date 07.05.14
 * @time 13:11
 * Created by JetBrains PhpStorm.
 */
/**
 * @property integer $id
 * @property string $name
 * @property string $description
 */
class BaseModel extends CActiveRecord
{
    public function rules(){
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
        return array(
            array('name, description', 'filter', 'filter' => array($purifier, 'purify')),
            array('description', 'default', 'value' => null),
            array('id, name, description', 'safe', 'on'=>'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('description',$this->description,true);

        return $this->getSearchDataProvider($this,$criteria);
    }

    /**
     * @param $model BaseModel
     * @param $criteria
     * @return CActiveDataProvider
     */
    public function getSearchDataProvider($model,$criteria) {
        $config = array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>Yii::app()->params['pagination']['AdminGridPerPage'],
            ),
        );
        return new CActiveDataProvider($model, $config);
    }
}

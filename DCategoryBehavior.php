<?php
/**
 * @author ElisDN <mail@elisdn.ru>
 * @link http://www.elisdn.ru
 *
 * @property string $urlAttribute
 * @property string $titleAttribute
 * @property string $aliasAttribute
 * @property string $parentAttribute
 * @property string $linkActiveAttribute
 * @property string $requestPathAttribute
 *  
 * @property integer[] $array
 * @property mixed $assocList
 * @property mixed $aliasList
 * @property mixed $menuArray
 */
class DCategoryBehavior extends CActiveRecordBehavior
{
    /**
     * @var string model attribute used for showing title
     */
    public $titleAttribute = 'name';
    /**
     * @var string model attribute, which defined alias
     */
    public $aliasAttribute = 'alias';
    /**
     * @var string model property, which contains url.
     * Optionally your model can have 'url' attribute or getUrl() method,
     * which construct correct url for using our getMenuArray().
     */
    public $urlAttribute = 'url';
    /**
     * @var string model property, which return true for active menu item.
     * Optionally declare own getLinkActive() method in your model.
     */
    public $linkActiveAttribute = 'linkActive';
    /**
     * @var string set this request property if you can use default getLinkActive() method
     */
    public $requestPathAttribute = 'path';
    /**
     * @var array default criteria for all queries
     */
    public $defaultCriteria = array();

    protected $_primaryKey;
    protected $_tableSchema;
    protected $_tableName;

    /**
     * Return primary keys of all items
     * @return array
     */
    public function getArray()
    {
        $criteria = $this->getOwnerCriteria();
        $criteria->select = $this->primaryKeyAttribute;

        $command = $this->createFindCommand($criteria);
        $result = $command->queryColumn();

        return $result;
    }

    /**
     * Returns associated array ($id=>$title, $id=>$title, ...)
     * @return array
     */
    public function getAssocList()
    {
        $this->cached();

        $items = $this->getFullAssocData(array(
            $this->primaryKeyAttribute,
            $this->titleAttribute,
        ));

        $result = array();
        foreach($items as $item){
            $result[$item[$this->primaryKeyAttribute]] = $item[$this->titleAttribute];
        }

        return $result;
    }

    /**
     * Returns associated array ($alias=>$title, $alias=>$title, ...)
     * @return array
     */
    public function getAliasList()
    {
        $this->cached();

        $items = $this->getFullAssocData(array(
            $this->aliasAttribute,
            $this->titleAttribute,
            $this->parentAttribute,
        ));

        $result = array();
        foreach($items as $item){
            $result[$item[$this->aliasAttribute]] = $item[$this->titleAttribute];
        }

        return $result;
    }

    /**
     * Returns items for zii.widgets.CMenu widget
     * @return array
     */
    public function getMenuArray()
    {
        $criteria = $this->getOwnerCriteria();

        $items = $this->cached($this->getOwner())->findAll($criteria);

        $result = array();

        foreach ($items as $item){
            $result[] = array(
                'id'=>$item->getPrimaryKey(),
                'label'=>$item->{$this->titleAttribute},
                'url'=>$item->{$this->urlAttribute},
                'itemOptions'=>array('class'=>'item_' . $item->getPrimaryKey()),
                'active'=>$item->{$this->linkActiveAttribute},
            );
        }

        return $result;
    }

    /**
     * Finds model by alias attribute
     * @param $alias
     * @return CActiveRecord model
     */
    public function findByAlias($alias)
    {
        $model = $this->cached($this->getOwner())->find(array(
            'condition'=>$this->aliasAttribute . '=:alias',
            'params'=>array(':alias'=>$alias),
        ));
        return $model;
    }

    /**
     * Optional redeclare this method in your model for use (@link getMenuArray())
     * or define in (@link requestPathAttribute) your $_GET attribute for url matching
     * @return bool true if current request url matches with category alias
     */
    public function getLinkActive()
    {
        return mb_strpos(Yii::app()->request->getParam($this->requestPathAttribute), $this->getOwner()->{$this->aliasAttribute}, null, 'UTF-8') === 0;
    }

    /**
     * Redeclare this method in your model for use of (@link getMenuArray()) method
     * @return string
     */
    public function getLUrl()
    {
        return '#';
    }

    protected function getFullAssocData($attributes)
    {
        $criteria = $this->getOwnerCriteria();
        $criteria->select = implode(', ', array_unique($attributes + array($this->primaryKeyAttribute)));

        $command = $this->createFindCommand($criteria);

        return $command->queryAll();
    }

    protected function createFindCommand($criteria)
    {
        $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
        $command = $builder->createFindCommand($this->tableName, $criteria);
        return $command;
    }

    protected function cached($model=null)
    {
        if ($model === null)
            $model = $this->getOwner();

        $connection = $model->getDbConnection();
        return $model->cache($connection->queryCachingDuration);
    }

    protected function getPrimaryKeyAttribute()
    {
        if ($this->_primaryKey === null)
            $this->_primaryKey = $this->tableSchema->primaryKey;
        return $this->_primaryKey;
    }

    protected function getTableSchema()
    {
        if ($this->_tableSchema === null)
            $this->_tableSchema = $this->getOwner()->getMetaData()->tableSchema;
        return $this->_tableSchema;
    }

    protected function getTableName()
    {
        if ($this->_tableName === null)
            $this->_tableName = $this->getOwner()->tableName();
        return $this->_tableName;
    }

    protected function getOwnerCriteria()
    {
        $criteria = $this->getOwner()->getDbCriteria();
        $criteria->mergeWith($this->defaultCriteria);
        return $criteria;
    }
}
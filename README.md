Category ActiveRecord Behavior for Yii
==========================
Contains popular methods for work with plain and hierarchical categories in Yii

Installation
------------

Extract to `protected/components`.

Usage example
-------------

Attach any from this behaviors to your model. Use DCategoryBehavior for plain models and DCategoryTreeBehavior for hierarchical models.
~~~
[php]
class Tag extends CActiveRecord
{
    // ...
    
    public function behaviors()
    {
        return array(
            'CategoryBehavior'=>array(
                'class'=>'DCategoryBehavior',
                'titleAttribute'=>'title',
                'defaultCriteria'=>array(
                    'order'=>'t.title ASC'
                ),
            ),
        );
    }    
        
    private $_url;

    // Generates URL. Use simple `$model->url` instead of `Yii::app()->createUrl(...)`;
    public function getUrl()
    {
        if ($this->_url === null)
            $this->_url = Yii::app()->createUtl('blog/tag', array('tag'=>$this->title);
        return $this->_url;
    } 
    
    // ...
}

// Static pages
class Page extends CActiveRecord
{
    // ...
    
    public function behaviors()
    {
        return array(
            'CategoryBehavior'=>array(
                'class'=>'DCategoryBehavior',
                'titleAttribute'=>'title',
                'aliasAttribute'=>'alias',
                'urlAttribute'=>'url',
                'requestPathAttribute'=>'alias',
                'defaultCriteria'=>array(
                    'order'=>'t.title ASC'
                ),
            ),
        );
    }
    
    private $_url;

    // Generates URL for every page. Use simple `$model->url` instead of `Yii::app()->createUrl(...)`;
    public function getUrl()
    {
        if ($this->_url === null)
            $this->_url = Yii::app()->request->baseUrl . '/page/' . $this->cache(3600)->getPath() . Yii::app()->urlManager->urlSuffix;
        return $this->_url;
    }  
    
    // ...
}

// Base class for all category models.
abstract class Category extends CActiveRecord
{    
    // Override in subclasses
    protected $urlPrefix = '';
    
    // ...
    
    public function behaviors()
    {
        return array(
            'CategoryTreeBehavior'=>array(
                'class'=>'DCategoryTreeBehavior',
                'titleAttribute'=>'title',
                'aliasAttribute'=>'alias',
                'urlAttribute'=>'url',
                'requestPathAttribute'=>'path',
                'parentAttribute'=>'parent_id',
                'parentRelation'=>'parent',
                'defaultCriteria'=>array(
                    'order'=>'t.position ASC, t.title ASC'
                ),
            ),
        );
    } 
    
    public function rules(){
        // ...
    }
    
    public function attributeLabels(){
        // ...
    }  
    
    private $_url;

    // Generates URL. Use simple `$model->url` instead of `Yii::app()->createUrl(...)`;
    public function getUrl()
    {
        if ($this->_url === null)
            $this->_url = Yii::app()->request->baseUrl . '/' . $this->urlPrefix . $this->cache(3600)->getPath() . Yii::app()->urlManager->urlSuffix;
        return $this->_url;
    }   
    
    // ...
}

/* 
 * Existing of redeclared custom field `urlPrefix` in all subclasses allows simple 
 * generate URL in base class without overriding of `getUrl()` method in childs
 */
class BlogCategory extends Category
{
    protected $urlPrefix = 'blog/';

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
	} 
    
    public function tableName()
    {
		return '{{blog_category}}';
	}

    public function relations()
	{
		return array_merge(parent::relations(), array(
            'parent' => array(self::BELONGS_TO, 'BlogCategory', 'parent_id'),
		));
	}  
}
~~~

Use any from public behavior methods.

Specification
-------------

***DCategoryBehavior***

Common parameters:

<table>
    <tr>
        <th>Attribute</th>
        <th>Description</th>
        <th>Default</th>
    </tr>
    <tr>
        <td style="white-space: nowrap;">titleAttribute</td>
        <td>Model attribute, which used for showing title.</td>
        <td>title</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">aliasAttribute</td>
        <td>Model attribute, which defined alias.</td>
        <td>alias</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">urlAttribute</td>
        <td>Model property, which contains url. Optionally your model can have `url` attribute or `getUrl()` method, which construct correct url for using our `getMenuArray()`.</td>
        <td>url</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">linkActiveAttribute</td>
        <td>Model property, which return true for active menu item. Optionally declare own public `getLinkActive()` method in your model.</td>
        <td>linkActive</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">requestPathAttribute</td>
        <td>Set this request property if you can use default `getLinkActive()` method from this Behavior for `getMenuArray()`.</td>
        <td>path</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">defaultCriteria</td>
        <td>Default criteria for all queries.</td>
        <td>array()</td>
    </tr>
</table>

Common methods:

<table>
    <tr>
        <th>Method</th>
        <th>Description</th>
    </tr>
    <tr>
        <td style="white-space: nowrap;">findByAlias($alias)</td>
        <td>Finds model by alias attribute.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getArray()</td>
        <td>Returns primary keys of all items.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getAssocList()</td>
        <td>Returns associated array ($id=>$title, $id=>$title, ...).</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getAliasList()</td>
        <td>Returns associated array ($alias=>$title, $alias=>$title, ...).</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getMenuArray()</td>
        <td>Returns items for zii.widgets.CMenu widget.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getLinkActive()</td>
        <td>Optional redeclare this method in your model for use `getMenuArray()` or define in `requestPathAttribute` your $_GET attribute for url matching. Returns true if current request url matches with category alias.</td>
    </tr>
</table>

***DCategoryTreeBehavior***

Content DCategoryBehavior specification and addons:

Additional parameters:

<table>
    <tr>
        <th style="white-space: nowrap;">Attribute</th>
        <th>Description</th>
        <th>Default</th>
    </tr>
    <tr>
        <td style="white-space: nowrap;">parentAttribute</td>
        <td>Parent attribute.</td>
        <td>parent_id</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">parentRelation</td>
        <td>Parent BELONGS_TO relation.</td>
        <td>parent</td>
    </tr>
</table>

Additional and overrided methods:

<table>
    <tr>
        <th>Method</th>
        <th>Description</th>
    </tr>
    <tr>
        <td style="white-space: nowrap;">findByPath($path)</td>
        <td>Finds model by path.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">isChildOf($parent)<sup>*</sup></td>
        <td>Checks for current model is child of parent.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getChildsArray($parent=0)<sup>*</sup></td>
        <td>Returns array of primary keys of children items.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getAssocList($parent=0)<sup>*</sup></td>
        <td>Returns associated array ($id=>$fullTitle, $id=>$fullTitle, ...).</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getAliasList($parent=0)<sup>*</sup></td>
        <td>Returns associated array ($alias=>$fullTitle, $alias=>$fullTitle, ...).</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getTabList($parent=0)<sup>*</sup></td>
        <td>Returns tabulated array ($id=>$title, $id=>$title, ...).</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getMenuArray($sub=0, $parent=0)<sup>*</sup></td>
        <td>Returns items for zii.widgets.CMenu widget.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getPath($separator='/')</td>
        <td>Constructs full path for current model.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getBreadcrumbs($lastLink=false)</td>
        <td>Constructs breadcrumbs for zii.widgets.CBreadcrumbs widget. Use `getBreadcrumbs(true)` if you can have link in last element.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getFullTitle($inverse=false, $separator=' - ')</td>
        <td>Constructs full title for current model.</td>
    </tr>
</table>

<sup>*</sup> Argument `$parent` may contains number, model object or array of numbers. You may use:

- `Model::model()->getChildsArray()`;
- `Model::model()->getChildsArray(5)`;
- `Model::model()->getChildsArray(array(1, 3, 5))`;
- `Model::model()->getChildsArray($model)` or `$model->getChildsArray()`;

Using for `dropDownList()` method:

~~~
[php]
<div class="row">
    <?php echo $form->labelEx($model, 'category_id'); ?><br />
    <?php echo $form->dropDownList(
        $model,
        'category_id',
        array_merge(array(''=>'[None]'), Category::model()->published()->getTabList())
    ); ?><br />
    <?php echo $form->error($model, 'category_id'); ?>
</div>
~~~

Using for CMenu widget (with caching):

~~~
[php]
<h2>All categories:</h2>
<?php $this->widget('zii.widgets.CMenu', array(
    'items'=>Category::model()->cache(3600)->getMenuArray(10))
); ?>

<h2>Subcategories of <?php echo $category->title; ?>:</h2>
<?php $this->widget('zii.widgets.CMenu', array(
    'items'=>$category->cache(3600)->getMenuArray())
); ?>
~~~

Usage sample in E-shop
---

Configuration file config/main.php:

~~~
[php]
return array(
    'components'=>array(
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
                // ...
                
                'shop/<action:cart|order>'=>'shop/<action>',
                
                // http://site.com/shop/printers/home/laser/15
                'shop/<path:.+>/<id:\d+>'=>'shop/view',
                
                // http://site.com/shop/printers/home/laser
                'shop/<path:.+>'=>'shop/category',
                
                'shop'=>'shop/index',
                
                // ...
            ),
        ),
    ),
)
~~~

Category model:

~~~
[php]
class ShopCategory extends Category
{
    protected $urlPrefix = 'shop/';
    
    // ...
    
    public function relations()
	{
		return array_merge(parent::relations(), array(
            'parent' => array(self::BELONGS_TO, 'BlogCategory', 'parent_id'),
		));
	}  
}
~~~

Product model:

~~~
[php]
class ShopProduct extends CActiveRecord
{  
    // ...

	public function relations()
	{
		return array(
            'category' => array(self::BELONGS_TO, 'ShopCategory', 'category_id'),
		);
	}

    private $_url;

    public function getUrl(){
        if ($this->_url === null)
            $this->_url = Yii::app()->request->baseUrl . '/shop/' . $this->category->path . '/' . $this->id;
        return $this->_url;
    }
}
~~~

Controller:

~~~
[php]
class ShopController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria;
        $criteria->order = 't.id DESC';
        
        $dataProvider = new CActiveDataProvider(
            ShopProduct::model()->cache(300),
            array(
                'criteria'=>$criteria,
                'pagination'=>array(
                    'pageSize'=>20,
                    'pageVar'=>'page',
                )
            )
        );

        $this->render('index', array(
            'dataProvider'=>$dataProvider,
        ));
    }
    

    public function actionCategory($path)
    {
        $category = ShopCategory::model()->findByPath($path);
        if (!$category)
            throw new CHttpException(404, 'Category not found');

        $criteria = new CDbCriteria;
        $criteria->order = 't.id DESC';
        
        $criteria->addInCondition('t.category_id', array_merge(
            array($category->id), $category->getChildsArray()
        ));
        
        $dataProvider = new CActiveDataProvider(
            ShopProduct::model()->cache(300),
            array(
                'criteria'=>$criteria,
                'pagination'=>array(
                    'pageSize'=>20,
                    'pageVar'=>'page',
                )
            )
        );

        $this->render('category', array(
            'dataProvider'=>$dataProvider,
            'category' => $category,
        ));
    }
    
    public function actionView($id)
    {
        $product = ShopProduct::model()->with('category')->findByPk($id);

        // Mirrors protection) 
        if (Yii::app()->request->requestUri != $product->url) 
            $this->redirect($product->url);
        
        if (!$product) 
            throw new CHttpException(404, 'Not found');

        $this->render('view', array(
            'product'=>$product,
        ));
    }
}
~~~

View shop/index.php:

~~~
[php]
<?php
$this->pageTitle = 'Catalog';
$this->breadcrumbs array('Catalog');
?>

<h1>Catalog</h1>

<p>Categories:</p>
<?php $this->widget('zii.widgets.CMenu', array('items' => ShopCategory::model()->getMenuArray()));?>

<?php echo $this->renderPartial('_loop', array('dataProvider'=>$dataProvider)); ?>
~~~

View shop/category.php:

~~~
[php]
<?php
$this->pageTitle = 'Catalog - ' . $category->getFullTitle();
$this->breadcrumbs = array_merge(
    array(
        'Catalog'=>$this->createUrl('shop/index'),
    ), 
    $category->getBreadcrumbs()
);
?>

<h1><?php echo CHtml::encode($category->title); ?></h1>

<p>Subcategories:</p>
<?php $this->widget('zii.widgets.CMenu', array('items' => $category->getMenuArray()));?>

<?php echo $this->renderPartial('_loop', array('dataProvider'=>$dataProvider)); ?>
~~~

View shop/view.php:

~~~
[php]
<?php
$this->pageTitle = $product->title;
$this->breadcrumbs=array(
    'Catalog'=>$this->createUrl('shop/index'),
);

if ($product->category)
    $this->breadcrumbs = array_merge($this->breadcrumbs, $product->category->getBreadcrumbs(true));

$this->breadcrumbs[]= $product->title;
?>

<h1><?php echo CHtml::encode($product->title); ?></h1>

<?php if ($product->category): ?>
    <p>Category: <?php echo CHtml::link($product->category->title, $product->category->url); ?></p>
<?php endif; ?>

<p>Price: <?php echo $product->price; ?></p>
~~~





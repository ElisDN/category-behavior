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
    
    // ...
}

class Category extends CActiveRecord
{
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
    
    public function getUrl()
    {
        // ...
    }    
    
    // ...
}
~~~

Use any from public behavior methods.

Specification
-------------

***DCategoryBehavior***

Parameters:

<table>
    <tr>
        <th style="white-space: nowrap;">Attribute</th>
        <th>Description</th>
        <th>Default</th>
    </tr>
    <tr>
        <td style="white-space: nowrap;">titleAttribute</td>
        <td>Model attribute used for showing title.</td>
        <td>title</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">aliasAttribute</td>
        <td>Model attribute, which defined alias.</td>
        <td>alias</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">urlAttribute</td>
        <td>Model property, which contains url. Optionally your model can have 'url' attribute or `getUrl()` method, which construct correct url for using our `getMenuArray()`.</td>
        <td>url</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">linkActiveAttribute</td>
        <td>Model property, which return true for active menu item. Optionally declare own `getLinkActive()` method in your model.</td>
        <td>linkActive</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">requestPathAttribute</td>
        <td>Set this request property if you can use default `getLinkActive()` method.</td>
        <td>path</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">defaultCriteria</td>
        <td>Default criteria for all queries.</td>
        <td>array()</td>
    </tr>
</table>

Methods:

<table>
    <tr>
        <td style="white-space: nowrap;">findByAlias()</td>
        <td>Finds model by alias attribute.</td>
    </tr>
    <tr>
        <td style="white-space: nowrap;">getArray()</td>
        <td>Return primary keys of all items.</td>
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

Parameters:

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

Methods:

<table>
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
        <td style="white-space: nowrap;">getMenuArray($parent=0, $sub=0)<sup>*</sup></td>
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
        <td style="white-space: nowrap;">getFullTitle($separator=' - ')</td>
        <td>Constructs full title for current model.</td>
    </tr>
</table>

<sup>*</sup> Argument `$parent` may contains number, model object or array of numbers. You may use:

- `Model::model()->getChildsArray()`;
- `Model::model()->getChildsArray(5)`;
- `Model::model()->getChildsArray(array(1, 3, 5))`;
- `Model::model()->getChildsArray($model)` or `$model->getChildsArray()`;

Using for `dropDownList()`:

~~~
[php]
<div class="row">
    <?php echo $form->labelEx($model, 'category_id'); ?><br />
    <?php echo $form->dropDownList(
        $model,
        'category_id',
        array(''=>'[Select category]') + Category::model()->published()->getTabList()
    ); ?><br />
    <?php echo $form->error($model, 'category_id'); ?>
</div>
~~~

Using for CMenu widget:

~~~
[php]
<?php $this->widget('zii.widgets.CMenu', array(
    'items'=>Category::model()->getMenuArray(0, 1000))
); ?>
~~~



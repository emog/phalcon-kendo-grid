

[![Dependency Status](https://www.versioneye.com/user/projects/54de663d271c93aa12000002/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54de663d271c93aa12000002)


# About
This is a [Phalcon Framework](http://phalconphp.com/) adapter for [Kendo DataSource](http://www.telerik.com/kendo-ui).
# Support
### Currently supported
* QueryBuilder interface
* ResultSet interface
* Pagination
* Global search (by value)
* Ordering
* Multiple column ordering
* Column-based search

# Installation
### Installation via Composer
* Install a composer
* Create `composer.json` file inside your project directory
* Paste into it
```json
{
    "require": {
        "m1ome/phalcon-kendo-grid": "1.*"
    }
}
```
* Run `composer update`

# Example usage
It uses Phalcon [QueryBuilder](http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model_Query_Builder.html) for pagination in KendoGrid.

In example we have a stantart MVC application, with database enabled. Don't need to provide a normal bootstrap PHP file, for Phalcon documentation, visit official site.

### Controller (using QueryBuilder):
```php
<?php

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $builder = $this->modelsManager->createBuilder()
                          ->columns('id, name, email, balance')
                          ->from('Example\Models\User');

          $kendoGrid = new \EmoG\KendoGrid\KendoGrid();
          $kendoGrid->fromBuilder($builder)->sendResponse();
        }
    }
}
```

### Controller (using ResultSet):
```php
<?php

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $resultset  = $this->modelsManager->createQuery("SELECT * FROM \Example\Models\User")
                             ->execute();

          $kendoGrid = new \EmoG\KendoGrid\KendoGrid();
          $kendoGrid->fromResultSet($resultset)->sendResponse();
        }
    }
}
```

### Controller (using Array):
```php
<?php

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $array  = $this->modelsManager->createQuery("SELECT * FROM \Example\Models\User")
                             ->execute()->toArray();

          $kendoGrid = new \EmoG\KendoGrid\KendoGrid();
          $kendoGrid->fromArray($array)->sendResponse();
        }
    }
}
```

### Model:
```php
<?php
/**
* @property integer id
* @property string name
* @property string email
* @property float balance
*/
class User extends \Phalcon\Mvc\Model {
}
```

### View:
```html
<html>
    <head>
        <title>Simple KendoGrid Application</title>
        <script type="text/javascript">
            $(document).ready(function() {
                 var grid = $("#grid").kendoGrid({                          
                            dataSource: {
                                dataType: "json",
                                transport: {
                                    read: "/test/index"
                                },                          
                                serverPaging: true,
                                serverFiltering: true,
                                serverSorting: true,
                            },
                            noRecords: true,
                            height: 500, 
                            sortable: true,
                            pageable: true,
                            columns: [{
                                field: "name",
                                title: "Name",                      
                            }, {
                                field: "address",
                                title: "Address",              
                
                            }, {
                                field: "phone",
                                title: "Phone",
                            }]
                        });
            });
        </script>
    </head>
    <body>
       <div id="grid"></div>
    </body>
</html>

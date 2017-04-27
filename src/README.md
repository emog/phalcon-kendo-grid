
![Build Status](https://img.shields.io/badge/branch-master-blue.svg) [![Build Status](https://travis-ci.org/m1ome/phalcon-datatables.svg?branch=master)](https://travis-ci.org/m1ome/phalcon-datatables) [![Coverage Status](https://coveralls.io/repos/m1ome/phalcon-datatables/badge.svg)](https://coveralls.io/r/m1ome/phalcon-datatables)

[![Total Downloads](https://poser.pugx.org/m1ome/phalcon-datatables/downloads.svg)](https://packagist.org/packages/m1ome/phalcon-datatables)  [![License](https://poser.pugx.org/m1ome/phalcon-datatables/license.svg)](https://packagist.org/packages/m1ome/phalcon-datatables)
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
It uses Phalcon [QueryBuilder](http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model_Query_Builder.html) for pagination in DataTables.

In example we have a stantart MVC application, with database enabled. Don't need to provide a normal bootstrap PHP file, for Phalcon documentation, visit official site.

### Controller (using QueryBuilder):
```php
<?php
use \DataTables\DataTable;

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $builder = $this->modelsManager->createBuilder()
                          ->columns('id, name, email, balance')
                          ->from('Example\Models\User');

          $dataTables = new DataTable();
          $dataTables->fromBuilder($builder)->sendResponse();
        }
    }
}
```

### Controller (using ResultSet):
```php
<?php
use \DataTables\DataTable;

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $resultset  = $this->modelsManager->createQuery("SELECT * FROM \Example\Models\User")
                             ->execute();

          $dataTables = new DataTable();
          $dataTables->fromResultSet($resultset)->sendResponse();
        }
    }
}
```

### Controller (using Array):
```php
<?php
use \DataTables\DataTable;

class TestController extends \Phalcon\Mvc\Controller {
    public function indexAction() {
        if ($this->request->isAjax()) {
          $array  = $this->modelsManager->createQuery("SELECT * FROM \Example\Models\User")
                             ->execute()->toArray();

          $dataTables = new DataTable();
          $dataTables->fromArray($array)->sendResponse();
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
        <script type="text/javascript" language="javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
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

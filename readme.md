# Sidecar

## Install

`composer require revosystems/sidecar`

> You need to have `tailwind 2.x` and `apline 3.x` in you main template


Add the blades path to the tailwindcss config file

```
purge: [
    ...
    './vendor/revosystems/sidecar/**/*.blade.php',
  ],
```
And define your brand color like so:

```
theme: {
    extend: {
      colors: {
        'brand': '#F2653A',
      }
    },
  },
```

You should also include the chart.js and choices.js to your main template's header with this line:
```
{!! \Revo\Sidecar\Sidecar::dependencies() !!}
```
### Configuration
Publish the configuration tot adapt it to you project

`php artisan vendor:publish`

In `config/sidecar.php` you will find the following parameters that can be adapted for your project

PARAMETER          | Default value              | Description
-------------------|----------------------------|-------------
translationsPrefix | admin                      | The prefix it will use for the translations withing the package
routePrefix        | sidecar                    | Sidecar provides some routes (for the widgets, search, etc..) by default it will be `yourproject.com/sidecar/xxx` you can update the prefix here
routeMiddleware    | ['web', 'auth', 'reports'] | The middlewares to use in the the custom sidecar routes (for widgets, search, etc...)
indexLayout		   | admin.reports.layout		| The layout the report view will extend (this one needs to have tailwind and jquery imported)
reportsPath		   | \\App\\Reports\\			| The path where `sidecar` will search for the reports
scripts-stack	   | scripts					| Since `sidecar` does some javascript, it will push it to the scripts stack `https://laravel.com/docs/8.x/blade` you layout needs to have the @stack('scripts') defined
exportRoute			| sidecar.report.export		| When exporting, `sidecar` provides its own route, however if you want to customeize it (for example to use it in a job) you can change the route that will be called

In `assets/css/sidecar.css` you will find the default styles

Also `assets/js/sidecar.js` contains the js necessary to sidecar work properly

You should add those files to your assets compilation.
### Global Variables
You can customize some runtime variables implementing the `serving callback`

```
class AppServiceProvider extends ServiceProvider	
    public function boot() {
        Sidecar::$usesMultitenant = true;	// When true, all the caches and jobs will use the `auth()->user()->id` as prefix
	    Sidecar::serving(function(){	
	            \Revo\Sidecar\ExportFields\Date::$timezone = auth()->user()->timezone;							// The timezone to display the dates
	            \Revo\Sidecar\ExportFields\Date::$openingTime = auth()->user()->getBusiness()->openingTime;		// To define a day change time instead of 00:00
	            \Revo\Sidecar\ExportFields\Currency::setFormatter('es_ES', auth()->user()->currency ?? 'EUR');	// For the currency field
        });
    }
```		

## Reports

To create your report you should create a new file caled `WathereverYouWantReport` (note it needs to end with Report) in the folder you defined in the `reportsPath` of the config file
This report class needs to extend the main `Revo\Sidecar\Report` class

And define the main model class and implement the fields method

```
<?php

namespace App\Reports;

use Revo\Sidecar\Report;
use App\Post;

class OrdersReport extends Report {

	protected $model  = Post::class;

	public function getFields() : array{
     	return [ ];	
    }
}
```

Now we just need to define the fields we want to show from our report

We can also add default query filters overriding the `query()` method

```
public function query() : Builder {
    return parent::query()->withTrashed();
}
```

We can also add MainActions as a button in the top right corner.

```
public function mainActions(): array
{
    return [
        MainAction::make(?string $title = null, ?string $icon = null, ?string $url = '')
    ];
}
```

#### More features

Param          | Description
---------------|-------------
`$title`       | You can customize the title of the report filling this field (or overriding the `getTitle()` function)
`$tooltip`     | You can add a tooltip to explain a bit about the report just filling this field
`$with`        | Even `sidecar` detects automatically the needed withs depending on the export fields, you can also add some extra ones filling this field
`$pagination`  | By default it will paginate for 50 rows to display, you can modify the defaul value for your report
`$exportable`  | Reports are exportable by default, you can set it to false to disable the feature for this report


### Fields

You can define the export fields with a simple array, most of them share the same features

##### Creation
`ExportField::make($field, $title, $dependsOnField)`

Param | Description
------ |------------
$field | The field to show, it will basically do a `data_get($row, $field)` the get the value, so it can contain a dot notation
$title | The title to use in the header / filters / group by for the field
$dependsOnField | There are some cases were the display field, depends on another field, for example a `user.name` would depend on `user_id` on the posts

##### Default options

Function               | Description
-----------------------|-------------
sortable()             | Indicates that the field can be sorted and it will show the sort arrows for it
hideMobile()           | Appends the class `hide-mobile` to the field column
onGroupingBy()         | You can defined how the field works when the report is being grouped by, almost all export fields already provide a default behavior that makes sense but you can override it with this
filterable()           | Makes the field filterable, setting the second parameter to true, will use an ajax query to get the filter options
icon()		           | You can define a font-awesome icon to be displayed instead of the title on the filters list
filterOptions()        | If you don't want the default filter options the `ExportField` provides, you can set your own with this function
groupable()	           | Define if a field is groupable
groupableWithGraph()   | Define if a field is groupable and should display a graph
comparable()           | Define if a field is comparable
onlyWhenGrouping()     | Mark a field to be displayed only when the report is being grouped
tdClasses()			   | Add your own TD Classes that you want to be appended to the column
hidden()			   | To not display the field 
filterOnClick()		   | Some fields can add a link when clicked that filters the report for its value
route()				   | You can define a route that will be linked (using the field as the parameter)
onTable()			   | There are some fields that are on another table (after a join) you can define the table with this function (usualy goes along with a `HasOne::defaultJoin`)
withTooltip()          | You can give a tooltip to the fields that will be shown in the header to explain a bit more about it


##### Text
· When filtering it will perform a `like` and you can enter you custom search text

##### Number
· It will align the row to the right
· When grouping by, by default will do a sum (you can change to it with the `onGroupingBy()` funciton)
· When filtering it will allow you to chose the operator and the amount
· It provides the function `trimZeros()` that will remove the trailing zeros of decimal values


##### Decimal
· Extends from number

##### Currency
· Extends from number

##### Percentage
· Extends from number and adds a % symbol to the html export  

##### Date
· This field allows different grouping by options (hour, day, dayOfWeek, week, month, quarter)
· When enabling the filterable, by default uses the last 7 days
· The filterOnClick option, performs a depth grouping filter, so if you group by month, and you click Novemeber, it will filter just november, grouping by week
· Date field also comes with a `timeFilterable()` that shows a time frame filter as well

##### Computed
· This field allows you to perform operations on the field, for example `Computed::make('guests/total')` 
· You can define the groupingBy operation as well `Computed::make('guests/total')->onGroupingBy('sum(guests)/sum(total)')` 

##### Id
· Id field shows the `id` of the row
· When grouping by, it will perform a count
· It is very common to use the id only when grouping `Id::make()->onlyWhenGrouping()`

##### BelongsTo
· When you have a belongs to relationship on your model, you can use this field to automatically create the filters/groups by
· By default it will use the `name` field on the relationship, use the `relationShipDisplayField($field)` function to use another field
· It will perform the needed joins when sorting / grouping By   
· You can scope the belongs by filter options by providing the list of ids `filterOptionsIdsScope()`    
· If you need to perform the join always you can call the `defaultJoin()`       
> You would usually filter the whole query as well to avoid accessing issues   

##### BelongsToThrough
· When you have a belongs to through relationship on your model, you can use this field to automatically create the filters/groups by, it will perform 
· By default it will use the `name` field on the relationship, use the `relationShipDisplayField($field)` function to use another field
· You need to define the pivot relation using the `through()` function `BelongsToThrough::make('user')->trough('comment')`
· It will perform the needed joins when sorting / grouping By

##### HasMany
· This field, will display all the hasMany models related, imploding the name with a `, `. By default it will use the `name` field on the relation, you can change it with `relationShipDisplayField($field)`
· This field is not filterable neither groupable

##### HasOne
· It provides the `defaultJoin()` option to perform the join in every query
· It is not filterable neither groupable

##### Enum
· It works for fields that are enum (consts) like a `status` or `type` 
· There is the `options()` function where you define the array of `[["value" => "displayName"]]` that will be used to display and filter

##### Icon
· A simple field, that will show the fontawesome icon using the `field` value as icon name  

#### Create your ExportField
You can create your own Export Fields by just extending any of the previous fields, or the main `Sidecar\ExportFields\ExportField`

```
<?php

namespace App\Reports\Sidecar;

use Revo\Sidecar\ExportFields\Link;

class Preview extends Link
{
    protected ?string $linkClasses = "showPopup";

    public static function make($field, $title = null, $dependsOnField = null)
    {
        $field = parent::make($field, $title, $dependsOnField);
        $field->route = $title;
        return $field;
    }

    public function getTitle(): string
    {
        return "";
    }

    public function getLinkTitle($row) : string {
        return '<i class="fa fa-eye fa-fw"></i>';
    }

    public function toHtml($row): string
    {
        $link = route($this->route, $this->getValue($row));
        return "<a href='{$link}' class='{$this->linkClasses}' style='color:gray;'>{$this->getLinkTitle($row)}</a>";
    }
}
```

The `ExportField` has the function `toHtml($row)` that is the one that will display the value when in the browser, when exporting to CSV it will use the `getValue($row)` so you can customize both.

> It is common to override the `getFilterKey()` with something like `non-filterable` to avoid collisions with the common fields, in the case of this `Preview` file, would collide with the `Id` field as it is the one to use in the link




### Widgets

A report can define a set of widgets to summarize what is being displayed (not paginated) so when it is not grouping it will show them. Right now there are just two possible widgets


```
<?php

namespace App\Reports\V2;

use App\Models\Orders\Order;

use Revo\Sidecar\Widgets\Count;
use Revo\Sidecar\Widgets\Sum;

class OrdersReport extends Report
{
    protected $model  = Order::class;

    public function getFields() : array{
        return [
            ...
        ];	
    }

    public function getWidgets() : array
    {
        return [
            Count::make('id', __('admin.count')),
            Sum::make('guests', trans_choice('admin.guest', 2)),
        ];
    }

}
```


## Dashboard

`Sidecar` also comes with nice dashboard panels that can be embeded into any page

You just need to create one by extending the `Revo\Sidecar\Panel` and define the `dimesion` and the `metric`
You need to pass the filters to apply to this report.


```
<?php

namespace App\Reports\V2\Sidecar\Widgets;

use App\Models\Orders\Order;
use App\Reports\V2\OrdersReport;
use Illuminate\Database\Eloquent\Builder;
use Revo\Sidecar\ExportFields\Currency;
use Revo\Sidecar\ExportFields\Date;
use Revo\Sidecar\ExportFields\ExportField;
use Revo\Sidecar\Filters\Filters;
use Revo\Sidecar\Panels\Panel;

class Sales extends Panel
{
    protected $model            = Order::class;
    protected ?string $tooltip  = "salesByDayDesc";

    public function query() : Builder{
        return parent::query()->whereNull('canceled')->whereNull('merged')->whereNotNull('opened');
    }

    public function __construct()
    {
        $filters = (new Filters())->groupingBy(['opened' => 'day'])
                                  ->forPeriod('opened', 'last30days')
                                  ->sortBy('opened', 'asc');
        parent::__construct("salesByDay", $filters);
    }

    public function metricField(): ExportField
    {
        return Currency::make('total');
    }

    public function dimensionField(): ExportField
    {
        return Date::make('opened')->filterable();
    }

    public function getFullReportLink(): ?string
    {
        return url('reports/v2/orders?' . $this->filters->getQueryString() );
    }
}
```

> You can implement the `getFullReportLink()` to link to a full report
> Note `$filters->getQueryString()` returns the query string to use for the `url`

Finally you can render them in your blade with something like this

```
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
    @foreach($panels as $panel)
        {!! $panel->render() !!}
    @endforeach
</div>

```

This dashboard panels will be cached until the next day, and will be loaded with ajax.

#### Panel Type

There are different types of `Panels` defined by the enum `PanelType`, `bar`, `list`, `trend` (default), and `pie`

```
class TopPaymentMethods extends Panel
{
    ...

    public PanelType $type = PanelType::pie;

    ...
}
``

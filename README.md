# wp-kit/magic-meta

This is a wp-kit component that handles [```Eloquent```](https://laravel.com/docs/5.4/eloquent) appending and querying of [```PostMeta```](https://codex.wordpress.org/Post_Meta_Data_Section).

When using ```Eloquent```, wouldn't it be great if we could append ```PostMeta``` to the Model just as Wordpress does natively? And wouldn't be great to query data based on [```meta_query```](https://codex.wordpress.org/Class_Reference/WP_Meta_Query#Accepted_Arguments) and [```tax_query```](https://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters) parameters? This is exactly what ```wp-kit/magic-meta``` handles.

## Installation

If you're using [```Themosis```](http://framework.themosis.com/), install via [```Composer```](https://getcomposer.org/) in the root of your ```Themosis``` installation, otherwise install in your ```Composer``` driven theme folder:

```php
composer require "wp-kit/magic-meta"
```

## Usage

### Model

```wp-kit/magic-meta``` comes with two traits, so all you need to do is include these in your model. 

Based on [```drewjbartlett/wordpress-eloquent```](https://github.com/drewjbartlett/wordpress-eloquent) you can the ```Post``` model provided and use the ```IsMagic``` trait.

```wp-kit/magic-meta``` relies on a ```::getMeta``` method on the Model to return the ```meta_value```, this is exactly what ```drewjbartlett/wordpress-eloquent``` provides.

```php
namespace Theme\Models;

use WPEloquent\Model\Post;
use WPKit\MagicMeta\Traits\IsMagic;
use WPKit\MagicMeta\Traits\TransformsQuery;

class SomePostType extends Post {
	
	use IsMagic;
	use TransformsQuery;

	protected $magic_meta = [
		'_some_meta_key' => 'appended_key',
		'_location' => 'location'
	];
	
}
```

### Query

***Parameters***

You can use [```::transformQuery```](https://github.com/wp-kit/magic-meta/blob/master/src/MagicMeta/Traits/MagicMeta.php#L70) [Query Scope](https://laravel.com/docs/5.4/eloquent#query-scopes) on [```Illuminate\Database\Query\Builder```](https://github.com/illuminate/database/blob/master/Query/Builder.php) to check for any of the following parameters. We also allow the query to check for any magic meta at root level of the parameters:

```php
[
	's' => '',
	'meta_query' => [],
	'tax_query' => [],
	'appended_key' => 'something', // queries PostMeta key '_some_meta_key'
	'location' => 'london' // queries PostMeta key '_location'
]
```

***Using transformQuery***

```php
namespace App\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\SomePostType;

class SomePostTypeController extends Controller {
	
	public function index(Request $request) {
	
		return response()->json( SomePostType::select( 'posts.*' )->type( 'some_type' )->transformQuery( $request ) );
		
	}
		 
	
}
```

## To Do

* Make transformQuery agnostic to Request parameters, using Collection instead in case users are using jsonapi standard etc.

## Get Involved

To learn more about how to use ```wp-kit``` check out the docs:

[View the Docs](https://github.com/wp-kit/theme/tree/docs/README.md)

Any help is appreciated. The project is open-source and we encourage you to participate. You can contribute to the project in multiple ways by:

- Reporting a bug issue
- Suggesting features
- Sending a pull request with code fix or feature
- Following the project on [GitHub](https://github.com/wp-kit)
- Sharing the project around your community

For details about contributing to the framework, please check the [contribution guide](https://github.com/wp-kit/theme/tree/docs/Contributing.md).

## Requirements

Wordpress 4+

PHP 5.6+

## License

wp-kit/magic-meta is open-sourced software licensed under the MIT License.

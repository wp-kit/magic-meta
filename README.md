# wp-kit/magic-meta

This is a Wordpress PHP Component that handles [```Eloquent```](https://laravel.com/docs/5.4/eloquent) appending and querying of [```PostMeta```](https://codex.wordpress.org/Post_Meta_Data_Section).

When using ```Eloquent```, wouldn't it be great if we could append ```PostMeta``` to the Model just as Wordpress does natively? And wouldn't be great to query data based on ```meta_query``` and ```tax_query``` parameters? This is exactly what ```wp-kit/magic-meta``` handles.

## Installation

If you're using ```Themosis```, install via ```Composer``` in the root of your ```Themosis``` installation, otherwise install in your ```Composer``` driven theme folder:

```php
composer require "wp-kit/magic-meta"
```

## Usage

### Model

```wp-kit/magic-meta``` comes with one trait, so all you need to do is include this in your Model. 

```wp-kit/magic-meta``` depends on [```drewjbartlett/wordpress-eloquent```](https://github.com/drewjbartlett/wordpress-eloquent) so it's best to extend the Post model provided and use the Magic Meta trait.

```php
namespace App\Models;

use WPEloquent\Model\Post;
use WPKit\MagicMeta\Traits\MagicMeta;

class SomePostType extends Post {
	
	use MagicMeta;

	protected $magic_meta = [
		'_some_meta_key' => 'appended_key',
		_location' => 'location'
	];
	
}
```

### Query

***Parameters***

You can use ```::transformQuery``` scope on ```QueryBuilder``` to check for any of the following parameters. We also allow the query to check for any magic meta at root level of the parameters:

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

## Requirements

Wordpress 4+

PHP 5.6+

## License

wp-kit/magic-meta is open-sourced software licensed under the MIT License.

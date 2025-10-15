# This is a work in progress basic MVC framework in php

## 🧭 Routing Configuration Guide

All application routes **must** be declared in the `config/routes.php` file inside the `ROUTES` constant array.
This routing system allows static and dynamic path definitions, method mapping, and optional middleware support.

---

## 📌 Basic Route Structure

Each route is defined as a **key-value pair**:

```php
'path' => [
    'controller' => ControllerName::class, // or full namespace string
    'method' => 'methodName',
    'middlewares' => [ /* Optional middlewares */ ]
]
```

- `path`: URL path that triggers the route.
- `controller`: Full controller class name (with namespace).
- `method`: Method inside the controller that handles the request.
- `middlewares` (optional): Array of instantiated middleware classes.

---

## 🏠 Example Routes

```php
const ROUTES = [
    '/' => [
        'controller' => HomeController::class,
        'method' => 'index'
    ],
    '/register' => [
        'controller' => AuthController::class,
        'method' => 'register',
        'middlewares' => [new GuestMiddleware()]
    ],
    '/profile/update' => [
        'controller' => ProfileController::class,
        'method' => 'update',
        'middlewares' => [new LoggedInMiddleware(), new ExampleMiddleware()]
    ],
    '/profile/{id:int}' => [
        'controller' => ProfileController::class,
        'method' => 'show'
    ],
    '/users/{username}' => [
        'controller' => UserController::class,
        'method' => 'show_by_username'
    ]
];
```

---

## 🧩 Dynamic Routes

Dynamic route parameters are defined inside **curly braces** `{}`.
You can also specify a **type** for validation after a colon `:`. Currently, only `int` validation is supported.

**Examples:**

- `/profile/{id:int}` → matches `/profile/42` and passes `id = 42` (integer validated).
- `/users/{username}` → matches `/users/johndoe` and passes `username = "johndoe"`.

#### Values will be passed to the controller method

**Example:**
path: `/profile/42`

```php
public function show(int $id) // $id == 42
{}
```

---

## 🧭 Controller Definition

The `controller` value **must reference the full class name**, either using the `::class` constant or as a fully qualified string.

✅ Both of the following are valid:

```php
'/' => [
    'controller' => HomeController::class,
    'method' => 'index'
],
'/' => [
    'controller' => "Src\\Controllers\\HomeController",
    'method' => 'index'
]
```

---

## 🔐 Middleware

The `middlewares` key is optional. If defined, it must contain an **array of instantiated middleware objects**.
These will be executed **in the order they are listed** before the controller method is called.

**Example:**

```php
'/dashboard' => [
    'controller' => DashboardController::class,
    'method' => 'index',
    'middlewares' => [
        new LoggedInMiddleware(),
        new AdminMiddleware()
    ]
]
```

---

## 🏗️ Controller Guidelines

- **Namespace & Autoloading:**
  All classes should be inside a namespace so the autoloader can include them automatically.

- **Controller Structure:**
  Controllers should extend from the **Core Controller** class and accept a `Request` object in the constructor, passing it to the parent.

```php
namespace Src\Controllers;

use Core\Controller;
use Core\Request;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}
```

- **Controller Methods:**
  All methods must **return a `Response` object**.

- **Rendering Views:**
  Use `render` or `render_with_layout` to return HTML content inside a `Response`.

```php
public function index()
{
    return $this->render_with_layout('home/index');
}
```

---

## 🏛️ Models

Model classes should extend from the **base Model class** to gain basic SQL methods like `save`, `delete`, and `find_by_id`.

- By default, the table name is the plural of the class name. You can override it by defining `$table_name`.

**Example (`users` table name):**

```php
class User extends Model
{}
```

**Example (`app_users` table name):**

```php
class User extends Model
{
    protected static string $table_name = "app_users";
}
```

---

## 🛡️ Middleware

Middlewares must implement the **`MiddlewareInterface`** and define the `__invoke` method.

**Example:**

```php
use Core\Request;
use Core\Response;
use Core\MiddlewareInterface;
use Closure;

class LoggedInMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Closure $next): Response
    {
        if (!SessionManager::is_logged()) {
            $response = new Response();
            return $response->redirect('login', 401);
        }
        return $next($request);
    }
}
```

---

Todo:

- Validator
- Query builder
- Continue/improve readme
- Improve framework overall

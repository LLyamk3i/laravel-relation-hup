### **Laravel Relationship Hub Trick**
A structured way to organize and call relationships dynamically using an intermediary **relationship hub**.

## **🚀 Overview**
This package introduces a **relationship hub** in Laravel that centralizes relationships under a dedicated class. It also enables **method forwarding** using a custom trait to avoid key mismatches and make relationship calls more readable.

## **🛠 Features**
✅ Encapsulates relationships inside a dedicated hub class  
✅ Uses **method forwarding** for dynamic relationship resolution  
✅ Eliminates key mismatches using an **enum-based approach**  
✅ Supports **HasMany** and **HasOne** relationships dynamically  

## **📦 Installation**
```bash
composer install
php artisan migrate
```

## **🔧 Implementation**
### **1️⃣ Define the Enum for Relationships**
```php
<?php

namespace App\Enums;

enum PostRelations: string {
    case LIST = 'posts/list';
    case LATEST = 'posts/latest';
    case OLDER = 'posts/older';
    case ACTIVES = 'posts/actives';
}
```

---

### **2️⃣ Create a Trait for Forwarding Relationship Calls**
```php
<?php

namespace App\Concerns\Eloquent;

trait ForwardToHubRelationshipMethod
{
    public function __call($method, $parameters)
    {
        if (! str_contains($method, '/')) {
            return parent::__call(method: $method, parameters: $parameters);
        }

        [$hub, $relationship] = explode('/', $method);

        if (! method_exists($this, $hub)) {
            throw new \BadMethodCallException(\sprintf('Hub method %s does not exist', $hub));
        }

        $object = \call_user_func(callback: [$this, $hub]);

        return $this->forwardCallTo(object: $object, method: $relationship, parameters: $parameters);
    }
}
```

---

### **3️⃣ Define the `PostRelationships` Hub Class**
```php
<?php

namespace App\Models\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final readonly class PostRelationships
{
    public function __construct(private readonly User $model) {}

    public function list(): HasMany
    {
        return $this->model->hasMany(related: Post::class);
    }

    public function latest(): HasOne
    {
        return $this->model->hasOne(related: Post::class)->latestOfMany();
    }

    public function older(): HasOne
    {
        return $this->model->hasOne(related: Post::class)->oldestOfMany();
    }

    public function actives(): HasOne
    {
        return $this->model->hasOne(related: Post::class)->where('enabled', true);
    }
}
```

---

### **4️⃣ Update the `User` Model**
```php
<?php

namespace App\Models;

use App\Concerns\Eloquent\ForwardToHubRelationshipMethod;
use App\Models\User\PostRelationships;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use ForwardToHubRelationshipMethod;

    public function posts(): PostRelationships
    {
        return new PostRelationships(model: $this);
    }
}
```

---

### **5️⃣ Update the `Post` Model**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'slug', 'enabled', 'user_id'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
```

---

## **🧪 Running Tests**
Using **PestPHP**, you can test the relationships like this:

```php
<?php

use App\Models\Post;
use App\Models\User;
use App\Enums\PostRelations;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has many posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    expect(User::with(PostRelations::LIST->value)->first()->getRelation(PostRelations::LIST->value))->toHaveCount(3);
});

it('has latest post', function () {
    $user = User::factory()->create();
    $latestPost = Post::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

    expect(User::with(PostRelations::LATEST->value)->first()->getRelation(PostRelations::LATEST->value))->toBeInstanceOf(Post::class);
});
```

---

## **💡 How It Works**
- The **`PostRelationships`** class encapsulates user-post relationships.
- The **`ForwardToHubRelationshipMethod`** trait dynamically forwards method calls like `posts/list` to `PostRelationships::list()`.
- The **enum `PostRelations`** ensures relationship keys are consistent and error-free.

---

## **🎯 Benefits**
✔ **Cleaner & Organized** – Keeps relationships in one place  
✔ **Prevents Key Mismatches** – Uses enums instead of string-based keys  
✔ **More Readable Queries** – Relationship calls are clear & logical  

---

## **📜 Example Usage**
```php
$user = User::first();
$posts = $user->posts()->list()->get();
$latestPost = $user->posts()->latest()->first();
$oldestPost = $user->posts()->older()->first();
$activePost = $user->posts()->actives()->first();
```

---

## **🎉 Conclusion**
This approach enhances maintainability and improves readability in Laravel projects. 🚀 Happy coding!

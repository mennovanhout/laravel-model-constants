# Laravel Model Constants
This package will generate constant files for your models to reduce typo's and no need to debug to find out with columns you have.

## Benefits
1. The files automatically get re-generated after every migration, so you can be sure that they are up 2 date.
2. When a column is removed you will get IDE errors.
3. Less possibilities for typo's
4. You can use these constants inside FormRequests, Models and wherever you normally type a string (column name)
5. This package is created with all design patterns in mind. Whether you use domain driven design or Laravels default approach

## How to install it
`composer require mennovanhout/laravel-model-constants`

## How to use it
This packages hooks into Laravels migration system and will generate the files after each migration or batch of migrations.

You can run it manually with: `artisan model:constants`

## Example of a generated constant file

```php
<?php

namespace Domain\Authentication\Models;

class UserColumns
{
	 const ID = 'id';
	 const NAME = 'name';
	 const EMAIL = 'email';
	 const EMAIL_VERIFIED_AT = 'email_verified_at';
	 const PASSWORD = 'password';
	 const REMEMBER_TOKEN = 'remember_token';
	 const CREATED_AT = 'created_at';
	 const UPDATED_AT = 'updated_at';
}
```

## Example of a model

```php
<?php

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        UserColumns::NAME,
        UserColumns::EMAIL,
        UserColumns::PASSWORD,
    ];

    protected $hidden = [
        UserColumns::PASSWORD,
        UserColumns::REMEMBER_TOKEN,
    ];

    protected $casts = [
        UserColumns::EMAIL_VERIFIED_AT => 'datetime',
    ];
}
```

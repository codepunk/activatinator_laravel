# Codepunk Activatinator

## Introduction

Codepunk Activatinator is an extension of Laravel's Auth framework that 
requires users to activate their account via email (or other notification) before 
being allowed to view any content normally visible to authenticated users.

Activatinator mimics the logic and extendability of the Laravel Auth "Password Reset"
functionality. See Laravel's [Authentication](https://laravel.com/docs/authentication) 
and [Resetting Passwords](https://laravel.com/docs/passwords) pages for more information.
Many of the customizations provided by Laravel when it comes to resetting passwords 
are also provided by the Activatinator.

## What's with the name?

The over-syllabic "Activatinator" is a shout-out to Heinz Doofenshmirtz, the villain
in the "Phineas and Ferb" cartoon series. According to the 
[Phineas and Ferb Wiki](http://phineasandferb.wikia.com/wiki/List_of_Doofenshmirtz%27s_schemes_and_inventions), 
"Doofenshmirtz's schemes and inventions, primarily known as 'Inators,' are plans and 
devices created by Dr. Heinz Doofenshmirtz as a means of dominating and taking over 
the Tri-State Area or other locations."

Since coding can often times feel like hatching a brilliant (and sometimes evil) 
scheme to take over the world, Codepunk borrows Doofenshmirtz's "Inator"-style naming 
convention.

## Install

1. If you haven't already done so:
   
   1. Create a new Laravel project:

      ```bash
      $ laravel new my_project
      ```

   2. Update your `.env` file to point to a valid database and email client.

   3. From within your new project directory, set up the Laravel authentication framework:

      ```bash
      $ php artisan make:auth
      ``` 

2. Require the Codepunk Activatinator!

   * Via Composer:

     ```bash
     $ composer require codepunk/activatinator
     ```
   
   * Manually:
     
     In the `require` section of your project's `composer.json' file, add the following:
     
     ```bash
     "codepunk/activatinator": "^1.2.1"
     ```
     
     (Or whatever the latest version happens to be)
     
     Then, update your project by executing the following command:
     
     ```bash
     $ composer update
     ```
     
3. Publish the Activatinator package:
   
   ```bash
   $ php artisan vendor:publish --force
   ```
   
   And choose the Codepunk ActivatinatorServiceProvider option.

4. Update the database with the required changes:
   
   ```bash
   $ php artisan migrate:refresh
   ```
5. Make changes to your `App\User` model:
   
   * Add the following `use` statements:
   
     ```php
     use Codepunk\Activatinator\Activable;
     use Codepunk\Activatinator\Contracts\Activable as ActivableContract;
     ```
   
   * Update the following:
     
     ```php
     class User extends Authenticatable
     ```
     
     to this:
     
     ```php
     class User extends Authenticatable
         implements ActivableContract
     ```
   
   * Update the following:
        
     ```php
     use Notifiable;
     ```
     
     to this:
     
     ```php
     use Notifiable, Activable;
     ```
     
   * **NOTE**: If you are also using Laravel Passport to implement OAuth in your application, then we want the `oauth\token` endpoint to fail when the user has not yet been authenticated. To implement this behavior, add the `Codepunk\Activatinator\Traits\ValidatesForPassport` trait to your `App\User` model (in addition to Passport's `HasApiTokens` trait as described in the Laravel Passport documentation):
   
     ```php
     use Notifiable, Activable, HasApiTokens, ValidatesForPassport;
     ```  

6. Make changes to `app/Http/Controllers/Auth/LoginController.php`:
   
   Update the following:
     
     ```php
     use Illuminate\Foundation\Auth\AuthenticatesUsers;
     ```
     
     to this:
     
     ```php
     use Codepunk\Activatinator\AuthenticatesUsers;
     ```

6. Make changes to `app/Http/Controllers/Auth/RegisterController.php`:
   
   Update the following:
     
     ```php
     use Illuminate\Foundation\Auth\RegistersUsers;
     ```
     
     to this:
     
     ```php
     use Codepunk\Activatinator\RegistersUsers;
     ```

6. Make changes to `resources/views/auth/login.blade.php`:
   
   Find these lines:
   
   ```html
   <div class="card-body">
       <form method="POST" action="{{ route('login') }}">
   ```
   
   And add the following line in between so it looks like this:

   ```html
   <div class="card-body">
       @include('codepunk::activatinator-alerts')
       <form method="POST" action="{{ route('login') }}">
   ```

### License

Codepunk Activatinator is open-sourced software licensed under the 
[MIT license](http://opensource.org/licenses/MIT)

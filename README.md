UserFilesBundle
===============

UserFilesBundle does the following:

- Uses [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) to store users in database,
- Provides supplementary files (templates, Controllers, etc.) to manage those users,
- Adds fields as firstname, lastname, avatar, etc.
- Displays a "challenge" for registration
- Allows the possibility to disable registration (for registering only one or more users)

[UserFiles Bundle dedicated web page](https://975l.com/en/pages/user-files-bundle).

Bundle installation
===================

Step 1: Download the Bundle
---------------------------
Add the following to your `composer.json > require section`
```
"require": {
    "c975L/user-files-bundle": "1.*"
},
```
Then open a command console, enter your project directory and update composer, by executing the following command, to download the latest stable version of this bundle:

```bash
$ composer update
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Step 2: Enable the Bundles
--------------------------
Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new FOS\UserBundle\FOSUserBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new c975L\EmailBundle\c975LEmailBundle(),
            new c975L\UserFilesBundle\c975LUserFilesBundle(),
        ];
    }
}
```

Step 3: Configure the Bundles
-----------------------------
Then, in the `app/config.yml` file of your project, define the following:

```yml
#Swiftmailer Configuration
swiftmailer:
    transport: "%mailer_transport%"
    host:      "%mailer_host%"
    username:  "%mailer_user%"
    password:  "%mailer_password%"
    spool:     { type: memory }
    auth_mode:  login
    port:       587

#Doctrine Configuration
doctrine:
    dbal:
        driver:   "%database_driver%"
        host:     "%database_host%"
        port:     "%database_port%"
        dbname:   "%database_name%"
        user:     "%database_user%"
        password: "%database_password%"
        charset:  UTF8
    orm:
        auto_generate_proxy_classes: "%kernel.debug%"
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true

#FosUserBundle
fos_user:
    db_driver:                  orm
    firewall_name:              main
    user_class:                 c975L\UserFilesBundle\Entity\User
    use_listener:               true
    use_flash_notifications:    true
    use_username_form_type:     false
    model_manager_name:         null
    from_email:
        address:                "%mailer_user%"
        sender_name:            "%mailer_user%"
    profile:
        form:
            type:               c975L\UserFilesBundle\Form\ProfileType
            name:               c975l_user_files_profile
    change_password:
        form:
            type:               FOS\UserBundle\Form\Type\ChangePasswordFormType
            name:               fos_user_change_password_form
    registration:
        confirmation:
            enabled:            true
            template:           FOSUserBundle:Registration:email.txt.twig
        form:
            type:               c975L\UserFilesBundle\Form\RegistrationType
            name:               c975l_user_files_registration
    resetting:
        token_ttl:              86400
        email:
            template:           FOSUserBundle:Resetting:email.txt.twig
        form:
            type:               FOS\UserBundle\Form\Type\ResettingFormType
            name:               fos_user_resetting_form
    service:
        mailer:                 fos_user.mailer.default
        email_canonicalizer:    fos_user.util.canonicalizer.default
        username_canonicalizer: fos_user.util.canonicalizer.default
        token_generator:        fos_user.util.token_generator.default
        user_manager:           fos_user.user_manager.default

#EmailBundle
c975_l_email:
    #Email address used to send emails
    sentFrom: 'contact@example.com'

#UserFilesBundle
c975_l_user_files:
    #Name of site to be displayed
    site: 'Example.com'
    #Indicate the Route to be used after Logout
    logoutRoute: 'home'
    #If registration is allowed or not
    registration: false #true(default)
    #(Optional) If you want to display the gravatar linked to the email user's account
    gravatar: true #null(default)
    #(Optional) If you want to save the email sent to user when deleting his/her account in the database linked to c975L/EmailBundle
    databaseEmail: true #false(default)
    #(Optional) If you want to archive the user in `user_archives` table (you need to create this table, see below)
    archiveUser: true #false(default)
```
Then add the correct values in the `app/parameters.yml`

```yml
parameters:
    database_driver: pdo_mysql
    database_host: localhost
    database_port: 80
    database_name: database_name
    database_user: databse_user
    database_password: database_password
    mailer_transport: smtp
    mailer_host: mail.example.com
    mailer_user: contact@example.com
    mailer_password: email_password
```
And finally in `app/security.yml`

```yml
security:
    encoders:
        FOS\UserBundle\Model\UserInterface: bcrypt

    role_hierarchy:
        ROLE_MODERATOR:   ROLE_USER
        ROLE_ADMIN:       [ROLE_MODERATOR, ROLE_USER]
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_MODERATOR, ROLE_USER]

    providers:
        fos_userbundle:
            id: fos_user.user_provider.username_email

    firewalls:
        main:
            pattern: ^/
            form_login:
                provider: fos_userbundle
                csrf_token_generator: security.csrf.token_manager
                login_path: fos_user_security_login
                check_path: fos_user_security_check
                default_target_path: userfiles_dashboard
            remember_me:
                secret: '%secret%'
                lifetime: 31536000
                path: /
                secure: true
            anonymous:    true
```

Step 4: Create MySql table
--------------------------
Use `/Resources/sql/user.sql` to create the table `user` if not already existing. The `DROP TABLE` is commented to avoid dropping by mistake.
You can also create the table `user_archives` + stored procedure to archive the user when deleting account, for this copy/paste the code from file `/Resources/sql/user.sql`, then set config value `archiveUser` to true.

Step 5: Enable the Routes
-------------------------
Then, enable the routes by adding them to the `app/config/routing.yml` file of your project:

```yml
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"

c975_l_user_files:
    resource: "@c975LUserFilesBundle/Controller/"
    type:     annotation
    #Multilingual website use: prefix: /{_locale}
    prefix:   /
```

Overriding Templates
--------------------
It is strongly recommended to use the [Override Templates from Third-Party Bundles feature](http://symfony.com/doc/current/templating/overriding.html) to integrate fully with your site.

For this, simply, create the following structure `app/Resources/c975LUserFilesBundle/views/` in your app and then duplicate the file `layout.html.twig` in it, to override the existing Bundle files, then apply your needed changes.

You also have to override:
- `app/Resources/c975LUserFilesBundle/views/emails/layout.html.twig` to set data related to your emails.
- `app/Resources/c975LUserFilesBundle/views/fragments/registerAcceptanceInfo.html.twig` to display links (Terms of use, Privacy policy, etc.) displayed in the register form.
- `app/Resources/c975LUserFilesBundle/views/fragments/deleteAccountInfo.html.twig` that will list the implications, by deleting account, for user, displayed in the delete account page.
- `app/Resources/c975LUserFilesBundle/views/fragments/dashboardActions.html.twig` to add your own actions (or whatever) in the dashboard i.e.

You can add a navbar menu via `{% include('@c975LUserFiles/fragments/navbarMenu.html.twig') %}`. You can override it, if needed, or simply override `/fragments/navbarMenuActions.html.twig` to add actions above it.

Routes
------
The Routes are those used by FOSUserBundle + `userfiles_dashboard`, `userfiles_signout` and `userfiles_delete_account`.

Using HwiOauth (Social network sign in)
---------------------------------------
You can display links on the login page to sign in with social network/s. **This bundle doesn't implement this functionality**, it only displays button/s on the login page. You have to configure [HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle) by your own.
If you use it, simply indicate in `app/config/routing.yml`
```yml
c975_l_user_files:
    #Indicates the networks you want to appear on the login page
    hwiOauth: ['facebook', 'google', 'live'] #Default null
```
You also have to upload images on your website named `web/images/signin-[network].png` (width="200" height="50"), where `network` is the name defined in the config.yml file.

Overriding Entity
-----------------
To add more fields (address, etc.) to the Entity `User`, you need to override it, but you **MUST** extend `FOS\UserBundle\Model\User`, **NOT** extend `c975L/UserFilesBundle/Entity/User`. It gives the following code:

Create the file `src/UserFilesBundle/UserFilesBundle.php`:
```php
<?php

namespace UserFilesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserFilesBundle extends Bundle
{
    public function getParent()
    {
        return 'c975LUserFilesBundle';
    }
}
```

Copy/paste the file `Entity/User.php` in `src/UserFilesBundle/Entity/`
```php
<?php

//Change the namespace
namespace UserFilesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Request;
use c975L\UserFilesBundle\Validator\Constraints as UserFilesBundleAssert;
use c975L\UserFilesBundle\Validator\Constraints\Challenge;
//...

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    //Keep all the fields and functions and add your own
}
```

In `app/config/config.yml` change the `user_class`
```yml
fos_user:
    user_class:                 UserFilesBundle\Entity\User
```

Overridding Forms
-----------------
To override Form, create the file `src/UserFilesBundle/UserFilesBundle.php` as explained above and duplicate the Form in `src/UserFilesBundle/Form/[FormName]Type.php`, (i.e. for Profile Form)
```php
<?php

//Change the namespace
namespace UserFilesBundle\Form;

//...
use c975L\UserFilesBundle\Form\ProfileType as BaseProfileType;

class ProfileType extends BaseProfileType
{
    //Do your stuff...
}

```

In `app/config/services.yml` add a service (i.e. for Profile Form):
```yml
services:
    app.user_files.profile:
        class: UserFilesBundle\Form\ProfileType
        arguments: ['@security.token_storage']
        tags:
            - { name: form.type }
```

In `app/config/config.yml` change the `type` linked to the form (i.e. for Profile Form)
```yml
fos_user:
    profile:
        form:
            type:               UserFilesBundle\Form\ProfileType
```

Overriding Controller
---------------------
To override Controller, create the file `src/UserFilesBundle/UserFilesBundle.php` as explained above and duplicate the Controller in `src/UserFilesBundle/Controller/UserController.php`
```php
<?php

//Change the namespace
namespace UserFilesBundle\Controller;

//...
use c975L\UserFilesBundle\Controller\UserController as BaseController;

class UserController extends BaseController
{
//DELETE USER
    /**
     * @Route("/delete",
     *      name="userfiles_delete_account")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function deleteAccountAction(Request $request)
    {
        parent::deleteAccountAction($request);
        //Do your stuff...
    }
}
```

The two functions `signoutUserFunction()` and `deleteAccountUserFunction()` are here to easily allow adding functions for sign out and delete user. Simply Override them in the Controller as described above.

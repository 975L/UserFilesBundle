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
            new c975L\UserFilesBundle\c975LUserFilesBundle(),
        ];
    }
}
```

Step 3: Configure the Bundles
-----------------------------
Then, in the `app/config.yml` file of your project, define data for SwiftMailer, Doctrine and `sentFrom` as the email address used to send emails.

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
        driver:   pdo_mysql
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
            name:               user_files_profile
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
            name:               user_files_registration
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
    sentFrom: 'contact@example.com'

#UserFilesBundle
c975_l_user_files:
    #Name of site to be displayed
    site: 'Example.com'
    #If registration is allowed or not
    registration: false #true (default)
    #(Optional) If you want to display the gravatar linked to the email user's account
    gravatar: true #null (default)
```
Then add the correct values in the `app/parameters.yml`

```yml
parameters:
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
            logout:
                path: fos_user_security_logout
                target: home
                invalidate_session: true
            anonymous:    true
```

Step 4: Create MySql table
--------------------------
- Use `/Resources/sql/user.sql` to create the table `user`. The `DROP TABLE` is commented to avoid dropping by mistake.

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

Overriding templates
--------------------
It is strongly recommended to use the [Override Templates from Third-Party Bundles feature](http://symfony.com/doc/current/templating/overriding.html) to integrate fully with your site.

For this, simply, create the following structure `app/Resources/c975LUserFilesBundle/views/` in your app and then duplicate the file `layout.html.twig` in it, to override the existing Bundle files, then apply your needed changes.

You also have to override:
- `app/Resources/c975LUserFilesBundle/views/emails/layout.html.twig` to set data related to your emails.
- `app/Resources/c975LUserFilesBundle/views/registerAcceptanceInfo.html.twig` to display links (Terms of use, Privacy policy, etc.) displayed in the register form.
- `app/Resources/c975LUserFilesBundle/views/deleteAccountInfo.html.twig` that will list the implications, by deleting account, for user, displayed in the delete account page.
- `app/Resources/c975LUserFilesBundle/views/dashboardActions.html.twig` to add your own actions (or whatever) in the dashboard i.e.
```php
<ul>
{# PageEdit dashboard #}
    <li>
        <a href="{{ path('pageedit_dashboard') }}">
            {{ 'label.dashboard'|trans({}, 'pageedit') ~ ' (PageEdit)' }}</a>
    </li>
</ul>
`̀̀``

Routes
------
The Routes are those used by FOSUserBundle + `userfiles_dashboard`, `userfiles_signout` and `userfiles_delete_account`.
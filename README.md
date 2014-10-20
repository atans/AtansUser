## AtansUser
A user registration, authentication, management and role-based access control module for Zend Framework 2

## Requirements
- Zend Framework 2 (latest master)
- ZfcRbac (~2.1.2)

Installation
============

### With composer

1.Add this project in your composer.json:

    "require": {
        "atans/atans-user": "dev-master"
    }

2.Now tell composer to download AtansUser by running the command:

    $ php composer.phar update

### Post installation


1.Enabling it in your application.config.php file.

```
<?php
return array(
    'modules' => array(
        // ...
        'ZfcBase',
        'ZfcRbac',
        'ZfcAdmin',
        'AtansCommon',
        'AtansUser',
    ),
    // ...
);
```

2.Copy `./vendor/atans/atans-user/config/atansuser.global.php.dist` to `./config/autoload/atansuser.global.php` and  `./vendor/atans/atans-user/config/zfcrbac.global.php.dist` to `./config/autoload/zfcrbac.global.php`

3.Then Import the SQL schema located in `./vendor/atans/atans-user/data/schema.sql`

Login
=====

Visit `http://pathtozf/user/login`
Username: admin
password: atansuser

Checking permissions in a controller or in a view
=================================================
See: ZF-Commons/zfc-rbac [06. Using the Authorization Service.] (https://github.com/ZF-Commons/zfc-rbac/blob/master/docs/06.%20Using%20the%20Authorization%20Service.md#checking-permissions-in-a-controller-or-in-a-view)

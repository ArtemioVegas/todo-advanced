ToDo Demo Application
========================

The "ToDo Demo Application" is application written for the purpose of studying the Symfony framework.

Requirements
------------

  * PHP 5.4 or higher;
  * PDO-SQLite PHP extension enabled;
  * Symfony 2.8
  * and the [usual Symfony application requirements][1].

Installation
------------

Execute this command to install the project:

```bash
composer install
```

Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd symfony-demo/
$ php app/console server:run
```

Alternatively, you can [configure a fully-featured web server][2] like Nginx
or Apache to run the application.

[1]: https://symfony.com/doc/2.8/reference/requirements.html
[2]: https://symfony.com/doc/2.8/setup/web_server_configuration.html
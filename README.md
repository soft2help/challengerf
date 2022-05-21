# Repository for rf challenge
## Usage

I already have a endpoint where you can test it 

https://challengerf.soft2help.net/

To check Api docs

https://challengerf.soft2help.net/api/doc

To make login

https://challengerf.soft2help.net/login


For demo porpuses

* Admin access:

**username:** admin@admin.com

**password:** Rf2022_

* User access:

**username:** tiago.bem at realfevr.com

**password:** Rf2022_
 


##############

**username:** bruno.coelho at realfevr.com

**password:** Rf2022_


After access in the right upper side icon you have allowed views, with user access you can configure subscriptions to the players and in admin dashboard you can send notifications


## Stack and instructions to install and run

The used stack for this project is:
 - Apache2 + PHP7.2 minimum
 - Mysql database
 - PHP Framework Symfony 4.4.8
 - Frontend Design with Pug + Jquery + Gulp

In apache configuration you should have something like:

```
<VirtualHost _default_:443>
    ServerAdmin mail@domain.com
    ServerAlias domain.com
    VirtualDocumentRoot "/var/www/public/"

    <Directory "/var/www/public/">
            AllowOverride All
    </Directory>
    Include /etc/apache2/ssl/options-ssl-apache.conf
</VirtualHost>
```

After copy/clone in document root you should install dependencies with *composer and change owner
```
cd /var/www
sudo composer install
sudo chown -R www-data:www-data .
```

Create Database and give user and password privilleges to that database

CREATE DATABASE rfchalleng;
CREATE USER 'rfchallenge'@'%' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVILEGES ON `rfchallenge` . * TO 'rfchallenge'@'%';
FLUSH PRIVILEGES;


Configure .env file to load configurations to database access  i will put some example in database url, to see notation
```
DATABASE_URL=mysql://rfchallenge:PASSWORD@localhost:3306/rfchallenge
MAIL_NOREPLY=noreply@mail.net
MAIL_PASSWORD=
MAIL_USERNAME=
MAIL_HOST=
MESSENGER_TRANSPORT_DSN=doctrine://default
```

After that you should create tables with command
```
/var/www$ sudo -u www-data php bin/console doctrine:schema:update --force
```

Populate players table with fixture task
```
/var/www$ sudo -u www-data php bin/console  doctrine:fixtures:load --group=importplayers --append
``` 
*--append its important because append the fixture instead of flushing the database

To add a Admin user fixture you can use 

```
/var/www$ sudo -u www-data php bin/console  doctrine:fixtures:load --group=defaultsuperadmin --append
``` 

To add a default user fixture you can use 

```
/var/www$ sudo -u www-data php bin/console  doctrine:fixtures:load --group=defaultuser --append
``` 

Before use command line you can check helper appending --help to the command, for example:
```
/var/www$ sudo php bin/console user:admin:add --help
```

Create admin user with command line to access backoffice
```
/var/www$ sudo php bin/console user:admin:add --username=admin@admin.com --password=Rf2022_
```

Create user to access backoffice (use a real email to receive notifications from subcribed players)

```
/var/www$ sudo php bin/console user:user:add --username=user@user.com --password=Rf2022_
```

Delete notifications older than one week
```
/var/www$ sudo php bin/console app:delete:notifications
```

To try endpoints endpoints without frontend, you can use oauth and use Bearer token, to do this you should create a client
```
/var/www$ sudo php bin/console fos:oauth-server:create-client  --grant-type="password"
```

Pick client Id and client secret from output, and then call endpoint 

/oauth/v2/token?client_id={clientIdFromOutput}&client_secret={clientSecretFromOutput}&grant_type=password&username={username}&password={password}

then you will receive json response with bearer token to use in api, the response have this output
{
    "access_token": "Mjg2OTJjZjU2NmI5NTQxZGIzZDhjOGViMDJjZmFiNzRkZThjZDY2MTIyM2U4YWE5NTZmNmYxMT",
    "expires_in": 86400,
    "token_type": "bearer",
    "scope": "profesional",
    "refresh_token": "YjRhMGEwMjhkOTgyZmY0NzRlMGM3NGQ1YjUyNWE2NWJmMTU4NGEwNTkyYWQwMTMzOD"
}

To use api put Authorization header with Bearer Mjg2OTJ... and also put Content-Type: application/json

To check documentation about api you can use public endpoint from your browser

**/api/doc**

To start queue system to send notifications in background, to put it as a background process check  https://symfony.com/doc/current/messenger.html#supervisor-configuration
```
/var/www$ sudo  php bin/console messenger:consume async -vv
```

if anything failed with messenger you can check it
```
/var/www$ sudo  php bin/console messenger:failed:show
```

To run unit test 

```
/var/www$ sudo  php bin/phpunit
```

you can append --debug if you want more details

##TODO
- Websocket connection with nodejs websocket server, to send notifications in real time when user is logged in or receive player updates
- List of Subscriptions by player in admin dashboard
- More unit tests
# Repository for rf challenge

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
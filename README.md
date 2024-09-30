# Description
Webapp that creates your free minecraft server! Offers web-menagment, adding forge, optifine, custom mods and many more!

# Prerequisites
Installed these packages:
* MSSQL drivers
* PHP 8.1 or higher
* NPM 8.5.1 or higher

# Installation 
Just run following commands:
```
composer install
npm install
```
If you need - run migrations:
```
php bin/console doctrine:migrations:migrate
```
Last - run server
```
symfony server:start --port=80
```

# OS 
Based on available architecture - *significantly* more admin options and server management options are available on Unix based operating system - if you can, switch to that.  

# License
Since it's just for fun it's free of charge, feel free to use it!

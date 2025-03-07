# Description
Webapp that creates your free minecraft server! Offers web-menagment, adding forge, optifine, custom mods and many more!

# Prerequisites
Installed these packages:
* MSSQL drivers
* PHP 8.1 or higher
* NPM 8.5.1 or higher
* Python 3.10 or higher

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
Allow script execution (forge downloader) as sudo
```
sudo chmod 755 bin/forgeDownloader.py
```
Install required python packages
```
sudo pip install traceback os argparse requests selenium
```
Install firefox GeckoDriver
```
wget https://github.com/mozilla/geckodriver/releases/download/v0.35.0/geckodriver-v0.35.0-linux64.tar.gz
tar -xvzf geckodriver-v0.35.0-linux64.tar.gz
sudo mv geckodriver /usr/local/bin/
sudo chmod +x /usr/local/bin/geckodriver
```
Next - we need to install java versions for multiple versions for all minecraft versions. You may change install path in file, default: %project.dir%/public/java
<br>Unix:
```
chmod +x bin/javaInstaller.sh
./bin/javaInstaller.sh
```
Windows:
```
powershell -ExecutionPolicy Bypass -File bin\javaInstaller.ps1
```
Last - run server
```
symfony server:start --port=80
```
## Optional
If forge downloader doesn't save files nor works directly when running command - try installing this package and run these commands:
```
sudo apt-get install xvfb
Xvfb :99 -ac &
export DISPLAY=:99
firefox --headless -CreateProfile selenium_profile
```
These should display environment that firefox can use with headless options, run Xvfb in the background and puts DISPLAY variable to global
# OS 
Based on available architecture - *significantly* more admin options and server management options are available on Unix based operating system - if you can, switch to that.  

# License
Since it's just for fun it's free of charge, feel free to use it!

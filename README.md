# Minecraft Server Webapp

## Description

A web application that allows you to create a free Minecraft server! Features include:

- Web-based server management
- Support for Forge, OptiFine, and custom mods
- Many additional features for customization

## Prerequisites

Ensure you have the following installed:

- **MSSQL drivers**
- **PHP 8.1** or higher
- **NPM 8.5.1** or higher
- **Python 3.10** or higher

## Installation

### 1. Install Dependencies

Run the following commands:

```sh
composer install
npm install
```

### 2. Run Database Migrations (if needed)

```sh
php bin/console doctrine:migrations:migrate
```

### 3. Allow Script Execution (Forge Downloader on) (Unix)

```sh
sudo chmod 755 bin/forgeDownloader.py
```

### 4. Install Required Python Packages

```sh
pip install traceback os argparse requests selenium
```

### 5. Install Firefox GeckoDriver (Unix)

```sh
wget https://github.com/mozilla/geckodriver/releases/download/v0.35.0/geckodriver-v0.35.0-linux64.tar.gz
tar -xvzf geckodriver-v0.35.0-linux64.tar.gz
sudo mv geckodriver /usr/local/bin/
sudo chmod +x /usr/local/bin/geckodriver
```

### 6. Install Java for Multiple Minecraft Versions

By default, Java will be installed in `%project.dir%/public/java`. You can change the install path in the script file.

#### **For Unix**

```sh
chmod +x bin/javaInstaller.sh
./bin/javaInstaller.sh
```

#### **For Windows**

```sh
powershell -ExecutionPolicy Bypass -File bin\javaInstaller.ps1
```

### 7. Start the Server with Cron Enabled

```sh
php bin/console app:serve-with-cron
```

#### **Optional Command Parameters:**

- Set a custom port:
  ```sh
  php bin/console app:serve-with-cron --port=xyz
  ```
- Run as a background task:
  ```sh
  php bin/console app:serve-with-cron -d
  ```
- Add extra commands:
  ```sh
  php bin/console app:serve-with-cron --extra [command]
  ```

### 8. Stopping the Server

To stop the Symfony server and the cron job, run:

```sh
php bin/console app:stop-server
```

## Troubleshooting

### **Issue: Server Starts but Then Stops (Unix)**

If the server starts but stops unexpectedly after displaying a message, ensure the `var` directory has write permissions:

1. Open the sudoers file:
   ```sh
   sudo visudo
   ```
2. Add the following line at the end:
   ```sh
   youruser ALL=(ALL) NOPASSWD: /usr/local/bin/symfony serve
   ```
3. If you don’t know where Symfony CLI is installed, check with:
   ```sh
   which symfony
   ```

### **Issue: Forge Downloader Doesn't Save Files (Unix)**

If the Forge downloader fails to save files or run properly, try the following:

```sh
sudo apt-get install xvfb
Xvfb :99 -ac &
export DISPLAY=:99
firefox --headless -CreateProfile selenium_profile
```

This creates an environment for Firefox to use with headless options, runs Xvfb in the background, and sets the `DISPLAY` variable globally.

## Operating System Recommendation

For maximum functionality, including more admin and server management options, it is **strongly recommended** to run the web app on a Unix-based operating system.

## License

This project is free and open-source! Feel free to use and modify it as you like.

---

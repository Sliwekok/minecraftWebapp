# Define the Java versions to install and their download URLs
$javaVersions = @{
    "8"  = "https://download.oracle.com/java/8/latest/jdk-8u371-windows-x64.zip"
    "16" = "https://download.oracle.com/java/16/latest/jdk-16.0.2_windows-x64_bin.zip"
    "17" = "https://download.oracle.com/java/17/latest/jdk-17.0.8_windows-x64_bin.zip"
    "21" = "https://download.oracle.com/java/21/latest/jdk-21_windows-x64_bin.zip"
}

# Define the root installation directory
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$javaInstallRoot = Join-Path -Path $scriptDir -ChildPath "..\public\java"

# Ensure the installation directory exists
if (-not (Test-Path -Path $javaInstallRoot)) {
    New-Item -ItemType Directory -Path $javaInstallRoot | Out-Null
}

# Function to download and extract a ZIP file
Function Install-Java {
    param (
        [string]$version,
        [string]$downloadUrl,
        [string]$installPath
    )

    # Define the temporary file path for the downloaded ZIP file
    $tempZipFile = Join-Path -Path $env:TEMP -ChildPath "jdk${version}.zip"

    # Check if the version is already installed
    if (Test-Path -Path $installPath) {
        Write-Host "Java $version is already installed at $installPath. Skipping..."
        return
    }

    # Download the JDK ZIP file
    Write-Host "Downloading Java $version from $downloadUrl..."
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempZipFile -UseBasicParsing

    # Extract the ZIP file to the target installation path
    Write-Host "Extracting Java $version to $installPath..."
    Expand-Archive -Path $tempZipFile -DestinationPath $installPath

    # Clean up the temporary ZIP file
    Remove-Item -Path $tempZipFile

    Write-Host "Java $version installed successfully at $installPath."
}

# Iterate through the versions and install each one
foreach ($version in $javaVersions.Keys) {
    $downloadUrl = $javaVersions[$version]
    $installPath = Join-Path -Path $javaInstallRoot -ChildPath "jdk$version"
    Install-Java -version $version -downloadUrl $downloadUrl -installPath $installPath
}

Write-Host "Java installation completed."
Write-Host "Java versions are installed under $javaInstallRoot."

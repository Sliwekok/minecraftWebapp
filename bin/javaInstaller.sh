#!/bin/bash

# Define the Java versions to install and their desired installation paths
declare -A java_versions
java_versions=(
   ["8"]="https://github.com/adoptium/temurin8-binaries/releases/download/jdk8u432-b06/OpenJDK8U-jdk_x64_linux_hotspot_8u432b06.tar.gz"
   ["16"]="https://github.com/adoptium/temurin16-binaries/releases/download/jdk-16.0.2+7/OpenJDK16U-jdk_x64_linux_hotspot_16.0.2_7.tar.gz"
   ["17"]="https://github.com/adoptium/temurin17-binaries/releases/download/jdk-17.0.8+7/OpenJDK17U-jdk_x64_linux_hotspot_17.0.8_7.tar.gz"
   ["21"]="https://github.com/adoptium/temurin21-binaries/releases/download/jdk-21.0.1+12/OpenJDK21U-jdk_x64_linux_hotspot_21.0.1_12.tar.gz"
)
# Root directory to install Java (relative to the script's location)
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")
JAVA_INSTALL_ROOT="$SCRIPT_DIR/../public/java"

# Create the root installation directory if it doesn't exist
mkdir -p $JAVA_INSTALL_ROOT

# Iterate over the versions and install each one
for version in "${!java_versions[@]}"; do
  echo "Installing Java $version..."

  # Define target installation directory
  target_dir="${JAVA_INSTALL_ROOT}/jdk${version}"

  # Check if Java version is already installed
  if [ -d "$target_dir" ]; then
    echo "Java $version is already installed in $target_dir. Skipping..."
    continue
  fi

  # Download the JDK tarball
  download_url="${java_versions[$version]}"
  temp_tarball="/tmp/jdk${version}.tar.gz"
  echo "Downloading from $download_url..."
  wget -q -O "$temp_tarball" "$download_url"

  # Extract the JDK tarball to the target directory
  echo "Extracting Java $version to $target_dir..."
  mkdir -p "$target_dir"
  tar -xzf "$temp_tarball" --strip-components=1 -C "$target_dir"

  # Clean up temporary tarball
  rm -f "$temp_tarball"

  echo "Java $version installed successfully in $target_dir."
done

echo "Java installation completed."
echo "Java versions are installed under $JAVA_INSTALL_ROOT."

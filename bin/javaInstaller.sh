#!/bin/bash

# Define the Java versions to install and their desired installation paths
declare -A java_versions
java_versions=(
  ["8"]="https://download.oracle.com/java/8/latest/jdk-8u371-linux-x64.tar.gz"
  ["16"]="https://download.oracle.com/java/16/latest/jdk-16.0.2_linux-x64_bin.tar.gz"
  ["17"]="https://download.oracle.com/java/17/latest/jdk-17.0.8_linux-x64_bin.tar.gz"
  ["21"]="https://download.oracle.com/java/21/latest/jdk-21_linux-x64_bin.tar.gz"
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

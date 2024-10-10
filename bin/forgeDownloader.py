from selenium import webdriver
from selenium.webdriver.common.by import By
import requests
import argparse
import os
import traceback
from selenium.webdriver.firefox.options import Options


def main():
    try:
        parser = argparse.ArgumentParser(description="Python scrip to download forge server client")
        parser.add_argument("url", help="URL of forge to download from")
        parser.add_argument("path", help="Path to save file")
        args = parser.parse_args()

        if args.path == '':
            print('Error: Path cannot be empty')
            quit()
        if args.url == '':
            print('Error: URL cannot be empty')
            quit()

        options = Options()
        options.headless = True

        # Step 1: Initialize WebDriver (Make sure you have the correct browser driver installed)
        driver = webdriver.Firefox(options=options)

        # Step 2: Open the first URL
        driver.get(args.url)

        # Step 3: Find the <a> tag under the div with class 'link-boosted'
        link_boosted = driver.find_element(By.CSS_SELECTOR, 'div.link-boosted a')
        boosted_link = link_boosted.get_attribute('href')

        # Step 4: Navigate to the boosted link
        driver.get(boosted_link)

        # Add a delay to wait for any redirects (increase time if necessary)
        # time.sleep(5)  # Increase if the page takes longer to load

        # Step 5: Find the download link under the div with class 'showSkip'
        print(driver.current_url)
        download_link_element = driver.find_element(By.CSS_SELECTOR, 'a.skip')
        download_link = download_link_element.get_attribute('href')

        # Step 6: Download the file
        file_response = requests.get(download_link)
        file_name = 'server.jar_forge'

        # Save the file
        full_path = os.path.join(args.path, file_name)
        with open(full_path, 'wb') as file:
            file.write(file_response.content)

        # Close the browser
        driver.quit()
        print("Success")
    except Exception as e:
        print(f"Error: {e}")
        traceback.print_exc()

main()

# DorineMae Apps Plugin  

The **DorineMae Apps** plugin enhances Elementor’s functionality with essential admin tools, including one-click cache clearing and automatic expiration of sections or widgets.  

## Features  

✅ **Clear Elementor Cache & Sync Library in 1 Click**  
Easily clear Elementor's cache and sync the library with a single click to improve performance.  

✅ **Auto-Expire Elementor Sections or Widgets**  
Set expiration times for sections/widgets using a custom CSS class, automatically hiding them when the set time is reached.  

## Installation  

### Method 1: Easy Installation (Recommended)  
1. Download the **latest ZIP** of the plugin from [GitHub Releases](https://github.com/dorinemae/dorinemae-apps/releases).  
2. In your WordPress dashboard, go to **Plugins → Add New → Upload Plugin**.  
3. Click **Choose File**, select the downloaded ZIP, then click **Install Now**.  
4. Once installed, click **Activate** to enable the plugin.  

### Method 2: Manual FTP Installation  
1. Download and extract the ZIP file.  
2. Upload the `dorinemae-apps` folder to `/wp-content/plugins/` via FTP.  
3. Go to **Plugins** in WordPress and activate **DorineMae Apps**.  

## Configuration

1. **Access Plugin Settings**  
   After activating the plugin, navigate to **DorineMae Apps** in the WordPress admin menu to access your plugin settings.

2. **Elementor Cache & Sync Feature**  
   - **Enable "Clear Elementor Cache & Sync":**  
     Enable this option to add a **CleanUp Elementor** button to the admin bar.
   - **How It Works:**  
     Clicking the **CleanUp Elementor** button triggers an AJAX process that simulates clearing Elementor’s cache and syncing its library.
   - **Visual Feedback:**  
     - Spinner buttons update their state sequentially:
       - **Default:** `button elementor-button-spinner`
       - **Loading:** `button elementor-button-spinner loading`
       - **Success:** `button elementor-button-spinner success`
     - A centered popup message—“Elementor cache cleared and library synced successfully.”—appears in the middle of the screen.
     - Approximately 1 second after the popup disappears, the page automatically refreshes.

3. **Auto-Expire Elementor Sections/Widgets Feature**  
   - **Auto-Expire Functionality is Always Active:**  
     This feature automatically hides Elementor sections or widgets once the specified expiration timestamp is reached.
   - **Setting Expiry Rules:**  
     In the plugin settings, simply enter the CSS class of the target element and set the corresponding date and time for when it should be hidden. Once you click **Save Changes**, the expiry rule becomes active, and the specified elements will automatically be hidden at the scheduled time.

4. **Save Your Settings**  
   After configuring the options above, click **Save Changes** to update your settings.
 

## Usage  

- **Cache Cleanup:** Click the **CleanUp Elementor** button in the admin bar when enabled.  
- **Auto-Expire:** Elements matching the specified class will automatically disappear at the scheduled time.  

## Contributing  

If you’d like to improve this plugin, feel free to fork the repository and submit a pull request on GitHub:  
🔗 [GitHub Repository](https://github.com/dorinemae/dorinemae-apps)  

## License  

This plugin is open-source and free to use. Contributions are welcome!  

<?php

//
require_once ABSPATH . "wp-admin/includes/plugin-install.php";
require_once ABSPATH . "/wp-admin/includes/plugin.php";

/**
 * common functions used in BBPress API Unit tests
 */
class Bbp_API_test_common {
  /**
   * Install BBPress into test environment
   */

  private $bbpress = "bbpress/bbpress.php";
  private $bbpapi = "bbp-API/bbp-api.php";

  function activateBBPress() {
    $this->active_plugins = get_option("active_plugins");
    if(!in_array($this->bbpress, $this->active_plugins)) {
      $plugin_args = array("slug" => "bbpress");
      $plugin_api = plugins_api("plugin_information", $plugin_args);
      $zip_location = WP_PLUGIN_DIR .  "/" . $plugin_api->slug . ".zip";
      $plugin_status = install_plugin_install_status($plugin_api);

      if($plugin_status['status'] == "install") {
        if(!file_exists(WP_PLUGIN_DIR .  "/" . $plugin_api->slug . ".zip")){
          $plugin_zip_url = curl_init($plugin_api->download_link);
          curl_setopt($plugin_zip_url, CURLOPT_RETURNTRANSFER, true);
          $zipfile = curl_exec($plugin_zip_url);
          curl_close($plugin_zip_url);
          file_put_contents(WP_PLUGIN_DIR .  "/" . $plugin_api->slug . ".zip", $zipfile);
        }
        if(file_exists(WP_PLUGIN_DIR .  "/" . $plugin_api->slug . ".zip")) {
          $zip = new ZipArchive;
          if ($zip->open($zip_location)) {
            $zip->extractTo(WP_PLUGIN_DIR);
            $zip->close();
          }

        }
      }
      activate_plugin($this->bbpress);
    }
  }

  function activateBBPAPI() {
    $this->active_plugins = get_option("active_plugins");
    if(!in_array($this->bbpapi, $this->active_plugins)) {
      activate_plugin($this->bbpapi);
      $this->active_plugins = get_option("active_plugins");
    }
  }
}

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


  /**
   * Create BBPress forum for testing.
   * @return int $forum_id  ID of the created forum.
   *
   */
  function createBBPForum() {
    if(file_exists($this->bbpress)) {
      include_once($this->bbpress);
    }
    $forum_id = bbp_insert_topic(
      array(
        'post_parent'  => 0,
        'post_title'   => "Test Forum",
        'post_content' => "Test Content",
        'post_author' => 1,
      ));
    return $forum_id;

  }

  /**
   * Create BBPress topic for testing.
   * @param  int $forum_id   ID of the forum to create the new topic within.
   * @param  array $topic_data  Array containing title and content of initial
   * post on the new topic.
   * @return int $topic_id  ID of the created topic.
   */
  function createBBPTopic($forum_id, $topic_data) {
    if(file_exists($this->bbpress)) {
      include_once($this->bbpress);
    }
    $topic_id = bbp_insert_topic(
      array(
        'post_parent'  => 0,
        'post_title'   => $topic_data["title"],
        'post_content' => $topic_data["content"],
        'post_author' => 1,
      ),
      array(
        'forum_id'     => $forum_id,
      )
    );
    return $topic_id;
  }

  /**
   * Create reply to topic for testing.
   * @return int ID of the created reply.
   */
  function createBBPReply($forum_id, $topic_id, $reply_data) {
    if(file_exists($this->bbpress)) {
      include_once($this->bbpress);
    }
    $reply_id = bbp_insert_reply(
      array(
        'post_parent'  => 0,
        'post_title'   => $reply_data['title'],
        'post_content' => $reply_data['content'],
        'post_author' => 1,
      ),
      array(
        'forum_id'     => $forum_id,
        'topic_id'     => $topic_id,
      )
    );
    return $reply_id;
  }
}

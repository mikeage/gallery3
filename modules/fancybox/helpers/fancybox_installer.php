<?php defined("SYSPATH") or die("No direct script access.");

class fancybox_installer {
  static function activate() {
    fancybox::check_config();
  }

  static function deactivate() {
    site_status::clear("fancybox_config");
  }
}
?>
<?php defined("SYSPATH") or die("No direct script access.");

class fancybox_Core {

  static function check_config() {
    $hash = module::get_var("fancybox", "settings");
    if (isset($hash)):
      site_status::warning(
        t("FancyBox is not quite ready! Please visit <a href=\"%url\">admin page</a>, verify and save settings before first use.",
          array("url" => html::mark_clean(url::site("admin/fancybox")))),
        "fancybox_config");
    else:
      site_status::clear("fancybox_config");
    endif;    
  }
}
?>

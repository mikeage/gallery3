<?php defined("SYSPATH") or die("No direct script access.");

class fancybox_event_Core {

  static function admin_menu($menu, $theme) {
    $menu
      ->get("settings_menu")
      ->append(Menu::factory("link")
               ->id("fancybox")
               ->label(t("FancyBox"))
               ->url(url::site("admin/fancybox")));
  }
}

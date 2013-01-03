<?php defined("SYSPATH") or die("No direct script access.") ?>

<ul>
  <li>
    <?= t("Version: %version", array("version" => gallery::VERSION)) ?>
  </li>
  <li>
    <?= t("Albums: %count", array("count" => $album_count)) ?>
  </li>
  <li>
    <?= t("Photos: %count", array("count" => $photo_count)) ?>
  </li>
  <li>
    <?= t("Hits: %count", array("count" => $view_count)) ?>
  </li>
</ul>

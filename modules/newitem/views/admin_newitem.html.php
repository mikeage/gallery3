<?php defined("SYSPATH") or die("No direct script access.") ?>
<style type="text/css">
.newspan {
background-color: <?= module::get_var("newitem", "bgcolor") ?>;
color: <?= module::get_var("newitem", "fontcolor") ?>;
padding-right: 5px;
padding-left: 5px;
}
</style>
<div id="g-admin-code-block">
  <h2><?= t("New! & Updated! text overlay administration.") ?></h2>

  <?= $form ?>
</div>

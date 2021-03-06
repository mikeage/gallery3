<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
/**
 * Loop through the children and give each 'new!' icon for each ID so that
 * we can do some crazy css stuff to move the icon about, depending on aspect ratio 
 * and if you have set the left, center or right location.
 * If the item is an album don't move the icon down.
 * I'm sure there is a better way with some JS to do the calculation
 * Let me know if you have a better idea on how to do the calculations
 * and still have the different aspect ratios work out.
 */
$textlocation = module::get_var("newitem", "location");
// left setting
if ($textlocation == 'TLeft') { ?>
  <? $margin = ($child->thumb_width - $child->thumb_height) / 2; ?>
  <style type="text/css">
  #g-thumb-<?= $child->id ?> {
  margin-top: <?= max(0, $margin); ?>px;
  <? if ($margin < 0) : ?>
   <? $margin2 = ($child->thumb_height - $child->thumb_width) / 2; ?>
   margin-left: <?= abs($margin2); ?>px;
  <? endif ?>
  }
  <? if ($child->is_album() && $margin > 0) : ?>
  span.g-newitem-text {
  top: 0px;
  }
  <? endif ?>
  </style>
<? } // end left
// Center setting
if ($textlocation == 'Center') { ?>
  <? $margin = ($child->thumb_width - $child->thumb_height) / 2; ?>
  <style type="text/css">
  #g-thumb-<?= $child->id ?> {
  margin-top: <?= max(0, $margin); ?>px;
  margin-left: 42%;
  }
  <? if ($child->is_album()): ?>
  span.g-newitem-text {
  top: 0px;
  }
  <? endif ?>
  </style>
<? } // end center
// Right setting
if ($textlocation == 'TRight') { ?>
  <? $margin = ($child->thumb_width - $child->thumb_height) / 2; ?>
  <style type="text/css">
  #g-thumb-<?= $child->id ?> {
  margin-top: <?= max(0, $margin); ?>px;
  float: right;
  display: block;
  position: relative;
  <? if ($margin > 0) : ?>
   <? $margin2 = ($child->thumb_height - $child->thumb_width) / 2; ?>
   margin-right: <?= min(abs($margin2), 35); ?>px;
  <? endif ?>
  <? if ($margin < 0) : ?>
   <? $margin3 = ($child->thumb_width - $child->thumb_height) / 2; ?>
   margin-right: <?= min(abs($margin3), 35) + 25; ?>px;
  <? endif ?> 
  }
  <? if ($child->is_album()): ?>
  span.g-newitem-text {
  top: 0px;
  }
  <? endif ?>
  </style>
<? } // end Right ?>
<div class="newcontainer">
<span class="g-newitem-text" id="g-thumb-<?= $child->id ?>"><?= t("&nbsp;&nbsp;New!&nbsp;&nbsp;") ?></span>
</div>

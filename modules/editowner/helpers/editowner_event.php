<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class editowner_event_Core {
  static function item_edit_form($item, $form) {

    // We don't want to allow changes of the root album
    if ($item->id == 1)
      return;

    $record = ORM::factory("item")->where("id", "=", $item->id)->find();
    $ownerrec = ORM::factory("user")->where("id", "=", $record->owner_id)->find();
    $ownerdata = $form->edit_item->group("edit_owner")->label("Owner");
    $allusers = ORM::factory("user")->order_by("full_name")->find_all();

    if ($record->loaded()) {
	foreach($allusers as $row){
	    $all_users[$row->id] = $row->full_name." (".$row->name.")";
	}
      if($item->is_album()){
	$form->edit_item->checkbox("change_all")->label(t("Check to modify owner of all photos within album too"))
				   ->class('g-unique')->checked(false);
      }
      $form->edit_item->dropdown("item_owner")
	->label(t("Owner"))
	->options($all_users)
	->id("g-editowner")
	->selected($ownerrec->id);
    }
  }

  static function item_edit_form_completed($item, $form) {
    // Change the item's captured field to the specified value.
  
    // We don't want to change the root element, so check for that
    if ($item->id == 1) {
      return;
    }
    if($form->edit_item->item_owner->value != $item->owner_id){
      $item->owner_id = $form->edit_item->item_owner->value;
      $item->save();
    }
    if($item->is_album()){
      $change_all = $form->edit_item->change_all->value;
      if($change_all){
	db::build()
	  ->update("items")
	  ->set("owner_id", $form->edit_item->item_owner->value)
	  ->where("parent_id", "=", $item->id)
	  ->execute();
	  $newowner = ORM::factory("user")->where("id", "=", $form->edit_item->item_owner->value)->find();
	  message::success(t("Changed owner of all photos in album to: ").$newowner->full_name);
      }
    }
  }

}


<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class Admin_Newitem_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var(
        "newitem", "location", $form->newitem->location->value);
      module::set_var(
        "newitem", "fontcolor", $form->newitem->fontcolor->value);
      module::set_var(
        "newitem", "bgcolor", $form->newitem->bgcolor->value);
      module::set_var(
        "newitem", "trans", $form->newitem->trans->value);
      module::set_var(
        "newitem", "top", $form->newitem->top->value);
      module::set_var(
        "newitem", "left", $form->newitem->left->value);
	  if (isset($form->newitem->hidden->value)) {
        module::set_var (
	      "newitem", "hidden", $form->newitem->hidden->value);
	  }
      module::set_var(
        "newitem", "days", $form->recent->days->value);
      module::set_var(
        "newitem", "updatedays", $form->updated->updatedays->value);
		
      message::success(t("Your settings have been saved."));
      url::redirect("admin/newitem");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_newitem.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }

  private function _get_form() {
    for ($i = 1; $i <= 10; $i++) {
      $range[$i] = "$i";
	}
	for ($i = 5; $i <= 95; $i+=5) {
      $range2[$i] = "$i";
	}
    $form = new Forge("admin/newitem/handler", "", "post", array("id" => "g-admin-form"));
	
	$group = $form->group("newitem")->label(t("General settings"));
    $group->input("bgcolor")
	    ->label(t("Choose the background color of the <span class='newspan'> text </span>"))
        ->value(module::get_var("newitem", "bgcolor", "yellow"));
    $group->dropdown("trans")->label(t("Choose the visability of the text."))
      	->options($range2)
      	->selected(module::get_var("newitem", "trans", "50"));
    $group->input("fontcolor")->label(t('Text color.'))
      ->label(t("Choose the font color of the <span class='newspan'> text </span>"))
	  ->value(module::get_var("newitem", "fontcolor", "red"));
    $group->dropdown("location")->label(t('Text location.  Text will be rotated 45&#176; for left & right location.)'))
      ->options(array("TLeft" => t("Top left"),
                      "Center" => t("Top center"),
					  "TRight" => t("Top right")))
	  ->selected(module::get_var("newitem", "location", "TLeft"));
    $group->input("top")->label(t('Pixels from the top.  Default is 20.  If center above use 10.<br />
								Choose a larger number if your thumbs are larger the the default.'))
      ->value(module::get_var("newitem", "top", "20"))
      ->rules("length[1,2]");
    $group->input("left")->label(t('Pixels from the left. If center above use 0.'))
      ->value(module::get_var("newitem", "left", "20"))
      ->rules("length[1,2]");
	if (module::is_active("dynamic")) {
    $group->checkbox("hidden")
		  ->label(t("Hide new item link."))
		  ->checked(module::get_var("newitem", "hidden", false) == 1);
	} else {
	    $group->checkbox("newitem_hidden")
		  ->label(t("Hide new item link. Not avalible as the Dynamic module is not active."))
		  ->disabled("disabled")
		  ->checked(module::get_var("newitem", "hidden", false) == 1);
	}
	$group = $form->group("recent")->label(t("New item settings"));
    $group->input("days")->label(t("Days to show: <span class='newspan'>New!</span>"))
      ->value(module::get_var("newitem", "days", "10"))
      ->rules("valid_numeric|length[1,3]");
	  
	$group = $form->group("updated")->label(t("Updated settings"));
	$group->input("updatedays")->label(t("On albums, days to show: <span class='newspan'>Updated!</span>"))
      ->value(module::get_var("newitem", "updatedays", "10"))
      ->rules("valid_numeric|length[1,3]");
	$form->submit("submit")->value(t("Save"));
    return $form;
  }
}
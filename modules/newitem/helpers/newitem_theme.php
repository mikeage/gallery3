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
class newitem_theme_Core {
  static function head($theme) {
    if ($theme->page_subtype() == "album" || $theme->page_subtype() == "tag" || $theme->page_subtype() == "Tag Albums" || $theme->page_subtype() == "dynamic" || $theme->page_subtype() == "search") {
	  $trans 	= module::get_var("newitem", "trans");
	  $bgcolor  	= module::get_var("newitem", "bgcolor");
	  $fontcolor = module::get_var("newitem", "fontcolor");
	  $top   	= module::get_var("newitem", "top");
	  $top   	.= 'px';
	  $left  	= module::get_var("newitem", "left");
	  $left  	.= 'px';
	  $location   	= module::get_var("newitem", "location");
	  if ($location == 'TLeft') { $deg = '-45deg'; $marleft = '0%'; }
	  if ($location == 'Center') { $deg = '0deg'; $marleft = '42%'; }
	  if ($location == 'TRight') { $deg = '45deg'; $marleft = '0%'; }
	  $images_url = url::file("modules/newitem/images");
	  return "\t<style type=\"text/css\"> 
        span.g-newitem-text {
        -webkit-transform: rotate($deg);	
        -moz-transform: rotate($deg);
        -ms-transform: rotate($deg);
        -o-transform: rotate($deg);
        transform: rotate($deg);
        -moz-opacity:.$trans;
        opacity:.$trans;
        filter:alpha(opacity=$trans);
        display:block;
        position:absolute;
        top: $top;
        left: $left;
        margin-left: $marleft;
        color: $fontcolor;
        background-color: $bgcolor;
        padding:3px 5px 3px 5px;
        letter-spacing:1px;
        }
      div.newcontainer {
        width: 100%;
        display: inline;
        position: absolute;
        top:0px;
        left:0px;
      }
      </style>";
	}
  }
  static function thumb_bottom($theme, $child) {
	$time_to_display = module::get_var("newitem", "days") * 86400;
	$difference =  time() - $child->created;
	if ($difference < $time_to_display) {
	  $view = new View("newitem.html");
	  $view->child = $child;
    return $view;
	}
	if ($child->is_album()) {
	// first get the number of items in the album that have been updated in $updatedays
	  $updatedays = module::get_var("newitem", "updatedays");
	  $item = ORM::factory("item", $child->id);
	  $sub_count = $item
	    ->viewable()
		->where("updated", ">", db::expr("UNIX_TIMESTAMP() - 86400 * $updatedays"))
		->descendants_count(array(array("type", "=", "photo")));
	}
	// if there is items in the album that have been updated returnt the updateditem view
	if (isset($sub_count) && $sub_count > 0) {
	  $view = new View("updateitem.html");
	  $view->child = $child;
      return $view;
	}
  }
}

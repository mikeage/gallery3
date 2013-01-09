<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
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
class Tag_name_Controller extends Controller {
  public function __call($function, $args) {
    $tag_name = $function;
    $tag = ORM::factory("tag")->where("name", "=", $tag_name)->find();
	if ($tag->id == 0) {
		/* No matching tag was found. If this was an imported tag, this is probably a bug. If the user typed the URL manually, it might just be wrong */
        throw new Kohana_404_Exception();
	} else {
		/* We don't want to manually construct a URI, since the tags module and the tags_album module should both be taken into account. However, if we use $tag->url(), we get a full path (/path/to/gallery3/tag*), and Kohana's url::redirect always assumes paths are relative to the application root. */
        //$uri = "tag/{$tag->id}/" . urlencode($tag->name);
		//url::redirect($uri);
		header("Location: {$tag->url()}");
	}
  }

}

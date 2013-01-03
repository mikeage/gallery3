<?php defined("SYSPATH") or die("No direct script access.");

class Admin_fancybox_Controller extends Admin_Controller {

  private $newline = false;

  private function save_item_state($statename, $value, $default) {
    $hash = module::get_var("fancybox", "settings");
    if (isset($hash)):
      $settings = unserialize($hash);
      if (($value) and ($value != $default)):
        $settings[$statename] = $value;
      else:
        if (isset($settings[$statename])):
          unset($settings[$statename]);
        endif;
      endif;
    else:
      $settings = array();
    endif;
    module::set_var("fancybox", "settings", serialize($settings));
  }

  private function read_item_state($statename, $default) {
    $hash = module::get_var("fancybox", "settings");
    if (isset($hash)):
      $settings = unserialize($hash);
      if (isset($settings[$statename])):
        return $settings[$statename];
      else:
        return $default;
      endif;
    else:
      return $default;
    endif;
  }

  private function write_line($Handle, $line) {
  	if ($line):
	    if ($this->newline):
	      $prefix = ',';
	    else:
	      $prefix = ' ';
	    endif;

	    $this->newline = true;
	    fwrite($Handle, "    " . $prefix . $line . "\n");
	  endif;
  }

  private function write_setting($Handle, $name, $state, $default, $type) {
    if ($state != $default):
      switch ($type):
        case 1: // bool
          if ($state):
          	$this->write_line($Handle, "'$name' : true");
          else:
            $this->write_line($Handle, "'$name' : false");
          endif;
          break;
        case 2: // int
          $this->write_line($Handle, "'$name' : $state");
          break;
        case 3: // string
          $this->write_line($Handle, "'$name' : '$state'");
          break;
        default:
          $this->write_line($Handle, "'$name' : $state");
      endswitch;
    endif;
  }

  public function index() {
    $view = new Admin_View("admin.html");
    $view->content = new View("admin_fancybox.html");
    $view->content->form = $this->_get_setting_form();
    $view->content->help = $this->get_edit_form_help();
    print $view;
  }

  public function save() {
    access::verify_csrf();

    $form = $this->_get_setting_form();
    if ($form->validate()):

      /* Read Form State ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
      $target         = $form->g_fancybox_appearance->target->value;
      $showCloseBtn   = $form->g_fancybox_appearance->show_close_btn->value;
      $padding        = $form->g_fancybox_appearance->padding->value;

      $overlayShow    = $form->g_fancybox_appearance->overlay_show->value;
      $overlayColor   = $form->g_fancybox_appearance->overlay_color->value;
      $overlayOpacity = $form->g_fancybox_appearance->overlay_opacity->value;
      $titlePosition  = $form->g_fancybox_appearance->title_position->value;

      $transitionIn   = $form->g_fancybox_animation->transitionin->value;
      $transitionOut  = $form->g_fancybox_animation->transitionout->value;
      $speedIn        = $form->g_fancybox_animation->speed_in->value;
      $speedOut       = $form->g_fancybox_animation->speed_out->value;
      $speedChange    = $form->g_fancybox_animation->speed_change->value;

      $autoResize     = $form->g_fancybox_behavior->autoresize->value;
      $centerOnScroll = $form->g_fancybox_behavior->center_on_scroll->value;
      $closeCnClick   = $form->g_fancybox_behavior->close_c_click->value;
      $closeOvClick   = $form->g_fancybox_behavior->close_o_click->value;
      $closeEsc       = $form->g_fancybox_behavior->close_e_click->value;
      $cyclic         = $form->g_fancybox_behavior->cyclic->value;

      /* Save Form State ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
      //module::clear_var("fancybox", "overlayColor");

      $this->save_item_state("target", $target, "a.g-sb-preview;a.g-fullsize-link");
      $this->save_item_state("hideCloseBtn", !$showCloseBtn, FALSE);
      $this->save_item_state("padding", $padding, "10");
      $this->save_item_state("overlayHide", !$overlayShow, FALSE);
      $this->save_item_state("overlayColor",  $overlayColor, "#666");
      $this->save_item_state("overlayOpacity", $overlayOpacity, "0.3");
      $this->save_item_state("titlePosition", $titlePosition, "default");

      $this->save_item_state("transitionIn",  $transitionIn, "default");
      $this->save_item_state("transitionOut", $transitionOut, "default");
      $this->save_item_state("speedIn", $speedIn, "300");
      $this->save_item_state("speedOut", $speedOut, "300");
      $this->save_item_state("changeSpeed", $speedChange, "300");

      $this->save_item_state("noAutoResize", !$autoResize, FALSE);
      $this->save_item_state("CenterOnScroll", $centerOnScroll, FALSE);
      $this->save_item_state("closeCnClick", $closeCnClick, FALSE);
      $this->save_item_state("noCloseOvClick", !$closeOvClick, FALSE);
      $this->save_item_state("noCloseEsc", !$closeEsc, FALSE);
      $this->save_item_state("cyclic", $cyclic, FALSE);

      $File = MODPATH . "fancybox/js/fancybox-init.js";
      $Handle = fopen($File, 'w');
      fwrite($Handle, "// ******* Auto-generated file. Please do not change. ***\n\n");
      fwrite($Handle, "$(document).ready(function() {\n");

      $targets = explode(";", $target);
      foreach($targets as $key => $value):
        $this->newline = false;
        fwrite($Handle, "  $(\"$value\").fancybox({\n");

        $this->write_setting($Handle, "showCloseButton", $showCloseBtn, TRUE, 1);
        $this->write_setting($Handle, "padding", $padding, 10, 2);
        $this->write_setting($Handle, "overlayShow", $overlayShow, TRUE, 1);
        $this->write_setting($Handle, "overlayColor", $overlayColor, "#666", 3);
        $this->write_setting($Handle, "overlayOpacity", $overlayOpacity, "0.3", 2);

        $_title = "";

        if ($titlePosition != "hide"):
          $_title .= "'titleFormat' : function(title, currentArray, currentIndex, currentOpts) { ";
        endif;
        switch ($titlePosition):
          case "over":
            $_title .= "return '<span id=\"fancybox-title-over\">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + ' : ' + title + '</span>'; }";
            $this->write_setting($Handle, "titlePosition", $titlePosition, "default", 3);
            break;
          case "over_dynamic":
            $_title .= "return '<span id=\"fancybox-title-over\">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + ' : ' + title + '</span>'; }\n";
            $this->write_setting($Handle, "titlePosition", "over", "", 3);
            $_title .= ",'onComplete' :	function() { $(\"#fancybox-wrap\").hover(function() { $(\"#fancybox-title\").show(); }, function() { $(\"#fancybox-title\").hide(); });	}";
            break;
          case "hide":
            $_title .= $this->write_setting($Handle, "titleShow", false, TRUE, 1);
            break;
          case "outside":
          case "inside":
          default:
            $_title .= "        return 'Image ' +  (currentIndex + 1) + ' / ' + currentArray.length + ' : ' + title;\n      }";
            break;
        endswitch;
        $this->write_line($Handle, $_title);

        $this->write_setting($Handle, "transitionIn", $transitionIn, "default", 3);
        $this->write_setting($Handle, "transitionOut", $transitionOut, "default", 3);
        $this->write_setting($Handle, "speedIn", $speedIn, "300", 2);
        $this->write_setting($Handle, "speedOut", $speedOut, "300", 2);
        $this->write_setting($Handle, "changeSpeed", $speedChange, "300", 2);

        $this->write_setting($Handle, "autoScale", $autoResize, TRUE, 1);
        $this->write_setting($Handle, "autoDimensions", $autoResize, TRUE, 1);

        $this->write_setting($Handle, "centerOnScroll", $centerOnScroll, FALSE, 1);
        $this->write_setting($Handle, "hideOnContentClick", $closeCnClick, FALSE, 1);
        $this->write_setting($Handle, "hideOnOverlayClick", $closeOvClick, TRUE, 1);
        $this->write_setting($Handle, "enableEscapeButton", $closeEsc, TRUE, 1);
        $this->write_setting($Handle, "cyclic", $cyclic, FALSE, 1);

        fwrite($Handle, "  });\n\n");
      endforeach;

      fwrite($Handle, "});\n");
      fclose($Handle);

      site_status::clear("fancybox_config");
      message::success("Settings have been Saved.");
      url::redirect("admin/fancybox");
    endif;

    $view = new Admin_View("admin.html");
    $view->content = new View("admin_fancybox.html");
    $view->content->form = $form;
    $view->content->help = $this->get_edit_form_help();
    print $view;
  }

  private function _get_setting_form() {
    $form = new Forge("admin/fancybox/save", "", "post", array("id" => "g-admin-fancybox-form"));

    $group = $form->group("g_fancybox_appearance")->label(t("Appearance Settings"));
    $group->input("target")
      ->label(t("Target (jQuery filter)"))
      ->rules("required")
      ->error_messages("required", t("You must enter a class/id/selector"))
      ->value($this->read_item_state("target", "a.g-sb-preview;a.g-fullsize-link"));
    $group->checkbox("show_close_btn")
      ->label(t("Show Close Button (Default: on)"))
      ->checked(!$this->read_item_state("hideCloseBtn", FALSE));
    $group->input("padding")
      ->label(t("Padding size in pixels (Default: 10)"))
      ->rules("required")
      ->error_messages("required", t("You must enter a value"))
      ->value($this->read_item_state("padding", "10"));
    $group->checkbox("overlay_show")
      ->label(t("Add Background Overlay (Default: on)"))
      ->checked(!$this->read_item_state("overlayHide", FALSE));
    $group->input("overlay_color")
      ->label(t("Overlay Color (default: #666)"))
      ->value($this->read_item_state("overlayColor", "#666"));
    $group->dropdown("overlay_opacity")
      ->label(t("Overlay Opacity (default: 0.3)"))
      ->options(array("0" => t("0 (transparent)"), "0.1" => "0.1", "0.2" => "0.2", "0.3" => t("0.3 (default)"), "0.4" => "0.4", "0.5" => "0.5", "0.6" => "0.6", "0.7" => "0.7", "0.8" => "0.8", "0.9" => "0.9", "1" => t("1 (opaque)")))
      ->selected($this->read_item_state("overlayOpacity", "0.3"));
    $group->dropdown("title_position")
      ->label(t("Title Position (default: Outside)"))
      ->options(array("default" => t("Outside (default)"), "inside" => t("Inside"), "over" => t("Overlay"), "over_dynamic" => t("Overlay (Dynamic)"), "hide" => t("Hide")))
      ->selected($this->read_item_state("titlePosition", "default"));

    $group = $form->group("g_fancybox_animation")->label(t("Animation Settings"));

    $group->dropdown("transitionin")
      ->label(t("Transition In (default: Fade)"))
      ->options(array("default" => t("Fade (default)"), "elastic" => t("Elastic"), "none" => t("None")))
      ->selected($this->read_item_state("transitionIn", "default"));
    $group->dropdown("transitionout")
      ->label(t("Transition Out (default: Fade)"))
      ->options(array("default" => t("Fade (default)"), "elastic" => t("Elastic"), "none" => t("None")))
      ->selected($this->read_item_state("transitionOut", "default"));
    $group->input("speed_in")
      ->label(t("Speed In (default: 300)"))
      ->rules("required")
      ->error_messages("required", t("You must enter a value"))
      ->value($this->read_item_state("speedIn", "300"));
    $group->input("speed_out")
      ->label(t("Speed Out (default: 300)"))
      ->rules("required")
      ->error_messages("required", t("You must enter a value"))
      ->value($this->read_item_state("speedOut", "300"));
    $group->input("speed_change")
      ->label(t("Change Speed (default: 300)"))
      ->rules("required")
      ->error_messages("required", t("You must enter a value"))
      ->value($this->read_item_state("changeSpeed", "300"));

    $group = $form->group("g_fancybox_behavior")->label(t("Behavior Settings"));
    $group->checkbox("autoresize")
      ->label(t("Auto Resize to Fit (Default: on)"))
      ->checked(!$this->read_item_state("noAutoResize", FALSE));
    $group->checkbox("center_on_scroll")
      ->label(t("Center on Scroll (Default: off)"))
      ->checked($this->read_item_state("CenterOnScroll", FALSE));
    $group->checkbox("close_c_click")
      ->label(t("Close on Content Click (Default: off)"))
      ->checked($this->read_item_state("closeCnClick", FALSE));
    $group->checkbox("close_o_click")
      ->label(t("Close on Overlay Click (Default: on)"))
      ->checked(!$this->read_item_state("noCloseOvClick", FALSE));
    $group->checkbox("close_e_click")
      ->label(t("Close with Esc (Default: on)"))
      ->checked(!$this->read_item_state("noCloseEsc", FALSE));
    $group->checkbox("cyclic")
      ->label(t("Cyclic Navigation (Default: off)"))
      ->checked($this->read_item_state("cyclic", FALSE));

    $form->submit("")->value(t("Save"));
    return $form;
  }

  protected function get_edit_form_help() {
    $help = '<fieldset>';
    $help .= '<legend>Help</legend><ul>';
    $help .= '<li><h3>Appearance Settings</h3>
      <p><b>Target (jQuery filter)</b> - specify one or more possible target elements which would be handled by Fancybox
      separated with <b>;</b> delimiter. Default: a.g-sb-preview;a.g-fullsize-link
      <br />Use <b>Show Close Button</b> to toggle Close Button
      <br /><b>Padding</b> - Space between FancyBox wrapper and content
      <br /><b>Overlay Opacity</b> - Opacity of overlay.
      </li>';
    $help .= '<li><h3>Animation Settings</h3>
      <p><b>Speed In</b> - Speed in miliseconds of the zooming-in animation
      <br /><b>Speed Out</b> - Speed in miliseconds of the zooming-out animation
      <br /><b>Change Speed</b> - Speed in miliseconds of the animation when navigating thorugh gallery items
      </li>';
    $help .= '<li><h3>Behavior Settings</h3>
      <p>The following settings should be left on default unless you know what you are doing.
      <p><b>Auto Resize to Fit</b> - Scale images to fit in viewport
      <br /><b>Center on Scroll</b> - Keep image in the center of the browser window when scrolling
      <br /><b>Close on Content Click</b> - Close FancyBox by clicking on the image<br />
      <i>(You may want to leave this off if you display iframed or inline content that containts clickable elements - for example: play buttons for movies, links to other pages)</i>
      <br /><b>Close on Overlay Click</b> - Close FancyBox by clicking on the overlay
      <br /><b>Close with Esc</b> - Close FancyBox when "Escape" key is pressed
      </li>';
    $help .= '</ul></fieldset>';
    return $help;
  }
}

?>
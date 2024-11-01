<?php
/*
Plugin Name: WP-Posts to Image
Plugin URI: http://www.birseyler.org/2010/05/wp-posts-to-image-plugin/ 
Description: Let’s you show your blog’s last posts on a forum signature or stg. uses bbcode.
Version: 0.5.9
Author: Utku Demir
Author URI: http://www.birseyler.org/
License: GPL2
*/

/*
WP-Posts to Image:
Copyright (c) 2010 Utku Demir

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

Contact:
Utku Demir
utdemir@gmail.com

*/

//The class for drawing codes
class p2i_textPNG {
	var $font ="fonts/dejavusans.ttf"; //default font. directory relative to script directory.
	var $msg = "no text"; // default text to display.
	var $size = 14; // default font size.
	var $rot = 0; // rotation in degrees.
	var $pad = 0; // padding.
	var $transparent = 1; // transparency set to on.
	var $red = 0; // black text...
	var $grn = 0;
	var $blu = 0;
	var $bg_red = 255; // on white background.
	var $bg_grn = 255;
	var $bg_blu = 255;
	var $fname = "./out.png";

	function draw() 
	{
		$width = 0;
		$height = 0;
	        $offset_x = 0;
		$offset_y = 0;
		$bounds = array();
		$image = "";
	
		// get the font height.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, "W");
		if ($this->rot < 0) 
		{			$font_height = abs($bounds[7]-$bounds[1]);		
		} 
		else if ($this->rot > 0) 
		{
		$font_height = abs($bounds[1]-$bounds[7]);
		} 
		else 
		{
			$font_height = abs($bounds[7]-$bounds[1]);
		}
		// determine bounding box.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, $this->msg);
		if ($this->rot < 0) 
		{
			$width = abs($bounds[4]-$bounds[0]);
			$height = abs($bounds[3]-$bounds[7]);
			$offset_y = $font_height;
			$offset_x = 0;
		} 
		else if ($this->rot > 0) 
		{
			$width = abs($bounds[2]-$bounds[6]);
			$height = abs($bounds[1]-$bounds[5]);
			$offset_y = abs($bounds[7]-$bounds[5])+$font_height;
			$offset_x = abs($bounds[0]-$bounds[6]);
		} 
		else
		{
			$width = abs($bounds[4]-$bounds[6]);
			$height = abs($bounds[7]-$bounds[1]);
			$offset_y = $font_height;;
			$offset_x = 0;
		}
		
		$image = imagecreate($width+($this->pad*2)+1,$height+($this->pad*2)+1);
		$background = ImageColorAllocate($image, $this->bg_red, $this->bg_grn, $this->bg_blu);
		$foreground = ImageColorAllocate($image, $this->red, $this->grn, $this->blu);
	
		if ($this->transparent) ImageColorTransparent($image, $background);
		ImageInterlace($image, false);
	
		// render the image
		ImageTTFText($image, $this->size, $this->rot, $offset_x+$this->pad, $offset_y+$this->pad, $foreground, $this->font, $this->msg);
	
		// output PNG object.
		imagePNG($image,$this->fname);
		#var_dump(is_writable(dirname(__FILE__)));
		#var_dump(is_writable(dirname($this->fname)));
		}
	}

function p2i_draw_image($post_name,$fname){
  $text=new p2i_textPNG;
  $text->msg=$post_name;
  $text->size=get_option('p2i_size');
  $text->fname=dirname(__FILE__).DIRECTORY_SEPARATOR.$fname;
  $text->transparent=get_option("p2i_transparency");
  $text->font=dirname(__FILE__).'/fonts/'.get_option('p2i_font');
  $text->red=get_option('p2i_red');
  $text->grn=get_option('p2i_green');
  $text->blu=get_option('p2i_blue');
  $text->bg_red=get_option('p2i_bgred');
  $text->bg_grn=get_option('p2i_bggreen');
  $text->bg_blu=get_option('p2i_bgblue');
  
  $text->draw();
}

function p2i_menu() {
  add_options_page(__('Posts to Image Options', 'wp-posts-to-image-plugin'), 'Posts to Image', 'manage_options', 'posts_to_image', 'p2i_options');
}

function p2i_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.', 'wp-posts-to-image-plugin') );
  }

  p2i_check_options();

  $x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
  if($_POST['save']=='yes'){
  p2i_set_options($_POST['font'],$_POST['size'],$_POST['r'],$_POST['g'],$_POST['b'],$_POST['transparency'],$_POST['br'],$_POST['bg'],$_POST['bb'],$_POST['count'],$_POST['htaccess']);
  p2i_update_all();
  }
  if($_POST['shortenlinks']=='yes'){
  $link_table="";
  $link_table.="<table>";
  $link_table.="<tr><td></td><td><center><b>".__('Images', 'wp-posts-to-image-plugin')."</b></center></td><td><center><b>".__('Links', 'wp-posts-to-image-plugin')."</b></center></td></tr>";

  $count=get_option("p2i_count");

  for($i=1;$i<=$count;$i++){
  $link_table.="<tr><td><b>$i </b></td><td>".p2i_shorten_url($x.$i.".png")."</td><td>".p2i_shorten_url($x.$i.'.php')."</td>";
  }
  $link_table.="</table>";
  }
?>
<div class="wrap">
<br>
<?php

if(!is_writable(dirname(__file__)))
  echo "<center><big><b>".__('Unable to complete initialization.<br>Please make writable and readable the following directory:', 'wp-posts-to-image-plugin')."</b><br>$x</big></center><br>";
?>

<b> <?php _e("Example:", 'wp-posts-to-image-plugin'); ?> </b><br>
<a href=<?php echo $x."1.php"; ?>><img src=<? echo $x."1.png"; ?>></a><br>
<br>
<form method="post">
    <table>
        <tr>
	<td><b><?php _e("Post Count", 'wp-posts-to-image-plugin'); ?></b></td><td><select name=count><?php
                $count=get_option("p2i_count");
                for($i=1;$i<=10;$i++){
                    echo "<option name=$i";
                    if($i==$count)
                        echo " SELECTED";
                    echo ">$i</option>";}
            ?></select></td>
        </tr>
	<td><b><?php _e('Select font:', 'wp-posts-to-image-plugin'); ?></b><br><small><?php _e('(You can upload your own .ttf\'s to the "fonts" folder)', 'wp-posts-to-image-plugin'); ?></small></td><td>
            <select name=font>
<?php
$handle=opendir(dirname(__FILE__).DIRECTORY_SEPARATOR."fonts");
  
  $font=get_option("p2i_font");
  while (false !== ($file = readdir($handle))) {
        if($file[0]=='.')continue;
        echo "<option name=$file";
        if($file==$font) echo " SELECTED";
        echo ">$file</option>";
        }
closedir($handle);
?>            
            </select>
            </td>
        </tr>
	<tr><td><b><?php _e('Select Font Size', 'wp-posts-to-image-plugin'); ?></b></td><td>
            <select name="size">
            <?php
                $size=get_option("p2i_size");
                for($i=6;$i<=32;$i++){
                    echo "<option name=$i";
                    if($i==$size)
                        echo " SELECTED";
                    echo ">$i</option>";}
            ?></select>
        </td></tr>
	<tr><td><b><?php _e('Font Colors:', 'wp-posts-to-image-plugin'); ?></b><br><small><?php _e('All of the colors must be RGB', 'wp-posts-to-image-plugin'); ?> </small></td></tr>
        <?php 
        $r=get_option("p2i_red");
        $g=get_option("p2i_green");
        $b=get_option("p2i_blue");
        $br=get_option("p2i_bgred");
        $bg=get_option("p2i_bggreen");
        $bb=get_option("p2i_bgblue");
        
        ?>
	<tr><td><?php _e('Red', 'wp-posts-to-image-plugin'); ?><small>(between 0-255)</small></td><td><input type=text name="r" maxlength=3 value=<?php echo $r; ?>></td></tr>
        <tr><td><?php _e('Green', 'wp-posts-to-image-plugin'); ?><small><?php _e('(between 0-255)', 'wp-posts-to-image-plugin'); ?></small></td><td><input type=text name="g" maxlength=3 value=<?php echo $g; ?>></td></tr>
        <tr><td><?php _e('Blue', 'wp-posts-to-image-plugin'); ?><small><?php _e('(between 0-255)', 'wp-posts-to-image-plugin'); ?></small></td><td><input type=text name="b" maxlength=3 value=<?php echo $b; ?>></td></tr></td>
	<tr><td><b><?php _e('Background Colors:', 'wp-posts-to-image-plugin'); ?></b><br><small><?php _e('All of the colors must be RGB', 'wp-posts-to-image-plugin'); ?> </small></td></tr>
        <tr><td><?php _e('Transparency:', 'wp-posts-to-image-plugin'); ?></td><td><input type=checkbox name="transparency" value=1 <?php if(get_option("p2i_transparency")) echo "checked=\"yes\""; ?>></td></tr>
        <tr><td><?php _e('Red', 'wp-posts-to-image-plugin'); ?><small><?php _e('(between 0-255)', 'wp-posts-to-image-plugin'); ?></small></td><td><input type=text name="br" maxlength=3 value=<?php echo $br; ?>></td></tr>
        <tr><td><?php _e('Green', 'wp-posts-to-image-plugin'); ?><small><?php _e('(between 0-255)', 'wp-posts-to-image-plugin'); ?></small></td><td><input type=text name="bg" maxlength=3 value=<?php echo $bg; ?>></td></tr>
	<tr><td><?php _e('Blue', 'wp-posts-to-image-plugin'); ?><small><?php _e('(between 0-255)', 'wp-posts-to-image-plugin'); ?></small></td><td><input type=text name="bb" maxlength=3 value=<?php echo $bb; ?>></td></tr></td>
	<tr><td><b><?php _e('Enable creating .htaccess','wp-posts-to-image-plugin')?></b><br/><small><?php _e('Adds "Expires:" header to images via .htaccess.<br/> Use the option with Apache webserver.') ?></small></td><td><input type=checkbox name="htaccess" value=1 <?php if(get_option("p2i_htaccess")) echo "checked=\"yes\""; ?>></td></tr>
    </table><br/>
<input type=hidden name="save" value=yes>
<input type=submit value="<?php _e('Save and Create Images', 'wp-posts-to-image-plugin'); ?>">
</form>
<form method="post">
	<input type=hidden name="shortenlinks" value=yes>
	<input type=submit value="<?php _e('Shorten Links', 'wp-posts-to-image-plugin'); ?>" value=yes>
</form>
<?php
  
  echo $link_table;

  $link_table="";
  $link_table.="<table>";
  $link_table.="<tr><td></td><td><center><b>".__("Images", 'wp-posts-to-image-plugin')."</b></center></td><td><center><b>".__("Links", 'wp-posts-to-image-plugin')."</b></center></td><td><center><b>".__("Preview", 'wp-posts-to-image-plugin')."</b></center></td></tr>";

  $count=get_option("p2i_count");

  for($i=1;$i<=$count;$i++)
    $link_table.="<tr><td><b>$i </b></td><td>".$x.$i.".png"."</td><td>".$x.$i.".php"."</td><td> <a href=$x"."$i.php><img src=$x"."$i.png></a></td></tr>";
  

  $link_table.="</table>";
  echo $link_table;
  echo '</div>';

}

function p2i_update_all(){
  p2i_create_images();
  p2i_create_redirects();
  if((int)get_option('p2i_htaccess'))
	  { p2i_create_htaccess();}
  else
	  { p2i_delete_htaccess();}
}
function p2i_delete_htaccess(){
	@unlink(dirname(__FILE__)."/.htaccess");
	}

function p2i_create_htaccess(){
	$content="ExpiresActive On\nExpiresByType image/png \"access plus 12 hours\"";
		
	$handle=fopen(dirname(__FILE__)."/.htaccess","w");
	fwrite($handle,$content);
	fclose($handle);
}

function p2i_create_images(){
  global $post;
  $i=1;
  $count=get_option("p2i_count");
  foreach(get_posts("numberposts=$count") as $post){
   p2i_draw_image($post->post_title,(string)$i.".png");
   $i++;
  }
  for(;$i<=11;$i++){
      $name=dirname(__FILE__)."/".(string)$i.".png";
      @unlink($name);}
}

function p2i_create_redirects(){
  global $post;
  //$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
  $i=1;
  $count=get_option("p2i_count");
  foreach(get_posts("numberposts=$count") as $post){
    $handle=fopen(dirname(__FILE__)."/".(string)$i.".php","w");
    fwrite($handle,"<?php\n header( 'HTTP/1.1 301 Moved Permanently' ); \n header( 'Location:".get_permalink(). "' ) ; \n?>");
    fclose($handle);
  $i++;
  }
  for(;$i<=11;$i++){
      $name=dirname(__FILE__)."/".(string)$i.".php";
      @unlink($name);
      }
}

function p2i_shorten_url($url){
    $url = "http://is.gd/api.php?longurl=".$url;
    $request = new WP_Http;
    $result = $request->request( $url );
    $output = $result['body'];

    return $output;
}

function p2i_clean(){
delete_option("p2i_font");
delete_option("p2i_size");
delete_option("p2i_red");
delete_option("p2i_green");
delete_option("p2i_blue");
delete_option("p2i_bgred");
delete_option("p2i_bggreen");
delete_option("p2i_bgblue");
delete_option("p2i_count");
delete_option("p2i_transparency");
delete_option("p2i_htaccess");

for($i=1;$i<=10;$i++){
      $name=dirname(__FILE__)."/".(string)$i;
      @unlink($name.".php");
      @unlink($name.".png");
      }
}

function p2i_set_options($font,$size,$r,$g,$b,$trans,$br,$bg,$bb,$count,$htaccess){
update_option("p2i_font",$font);
update_option("p2i_size",(int)$size);
update_option("p2i_red",(int)$r);
update_option("p2i_green",(int)$g);
update_option("p2i_blue",(int)$b);
update_option("p2i_bgred",(int)$br);
update_option("p2i_bggreen",(int)$bg);
update_option("p2i_bgblue",(int)$bb);
update_option("p2i_count",(int)$count);
update_option("p2i_transparency",$trans);
update_option("p2i_htaccess",$htaccess);
}

function p2i_check_options(){
if(!get_option("p2i_font")) update_option("p2i_font","dejavusans.ttf");
if(!get_option("p2i_size")) update_option("p2i_size",14);
if(!get_option("p2i_red")) update_option("p2i_red",0);
if(!get_option("p2i_green")) update_option("p2i_green",0);
if(!get_option("p2i_blue")) update_option("p2i_blue",0);
if(!get_option("p2i_bgred")) update_option("p2i_bgred",255);
if(!get_option("p2i_bggreen")) update_option("p2i_bggreen",255);
if(!get_option("p2i_bgblue")) update_option("p2i_bgblue",255);
if(!get_option("p2i_count")) update_option("p2i_count",5);    
if(!get_option("p2i_transparency")) update_option("p2i_transparency",1);
if(!get_option("p2i_htaccess")) update_option("p2i_htaccess",1);
}
function p2i_check_files(){

$fname=dirname(__FILE__).DIRECTORY_SEPARATOR."1.php";
if((!file_exists($fname)) or !is_writable(dirname(__file__))){

$options_page_link=get_bloginfo('wpurl')."/wp-admin/options-general.php?page=posts_to_image";
?>
<div id="message" class="error"><p><?php _e("<strong>Error:</strong> Your current WP Posts to Image plugin doesn't have any images and/or can't write the plugin directory - Please reconfigure it.", 'wp-posts-to-image-plugin'); ?></p><p><a href="<?php echo "$options_page_link\" > $options_page_link"; ?></a></p></div>
<?php

}
}

load_plugin_textdomain('wp-posts-to-image-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/locale/' );
register_uninstall_hook(__FILE__, 'p2i_clean');
add_action('admin_menu', 'p2i_menu');
add_action('publish_post', 'p2i_update_all');
add_action('admin_head','p2i_check_files');

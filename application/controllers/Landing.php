<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends Yumbox_Controller {

	public function index()
	{
		// load language
		$this->lang->load("landing");
		
		// featured foods
		$rush_food_id = $this->config->item('featured_rush_id');
		$rush_food = $this->food_model->getFoodAndVendorForFoodId($rush_food_id);
		$data["rush_food_id"] = $rush_food_id;
		$data["rush_food_name"] = $rush_food==NULL?"":$rush_food->food_name;
		$data["rush_food_descr"] = limit_text($rush_food==NULL?"":$rush_food->descr, 20);
		$rush_food_pic = $this->food_model->getFoodPicturesForFoodId($rush_food_id);
		$rush_food_pic = count($rush_food_pic)>0?$rush_food_pic[0]:"";
		$data["rush_food_pic"] = $rush_food_pic==NULL?"":$rush_food_pic->path;
		$data["rush_vendor"] = $rush_food==NULL?"":$rush_food->vendor_name;
		
		$explore_food_id = $this->config->item('featured_explore_id');
		$explore_food = $this->food_model->getFoodAndVendorForFoodId($explore_food_id);
		$data["explore_food_id"] = $explore_food_id;
		$data["explore_food_name"] = $explore_food==NULL?"":$explore_food->food_name;
		$data["explore_food_descr"] = limit_text($explore_food==NULL?"":$explore_food->descr, 20);
		$explore_food_pic = $this->food_model->getFoodPicturesForFoodId($explore_food_id);
		$explore_food_pic = count($explore_food_pic)>0?$explore_food_pic[0]:"";
		$data["explore_food_pic"] = $explore_food_pic==NULL?"":$explore_food_pic->path;
		$data["explore_vendor"] = $explore_food==NULL?"":$explore_food->vendor_name;
		
		// page data
		$data["quick_menu_text"] = $this->lang->line("quick_menu_text");
		$data["full_menu_text"] = $this->lang->line("full_menu_text");
		$data["nav_content"] = $this->navigation(false);
		
		// Load views
		$this->header();
		$this->load->view("landing", $data);
		$this->footer();
	}
}

<?php

include_once plugin_dir_path(__FILE__).'/drawwidget.php';

class draw
{
	
	public function __construct()
	{
		add_action('widgets_init',function(){
			register_widget('draw_Widget');
		});
	}

}
new draw();
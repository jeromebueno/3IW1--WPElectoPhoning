<?php

/**
 * Création du Widget
 */
class draw_Widget extends WP_Widget
{

	public function __construct()
	{
		parent::__construct('user_draw', 'Tirage au sort', array('description'=> 'Tirage au sort d\'un utilisateur'));
	}

	//Formulaire en Back-Office
	function form($instance) {
		$title = isset($instance['title']) ? $instance['title']: 'Mon tirage';
		$date = isset($instance['date']) ? $instance['date']: '';
		$id = isset($instance['id']) ? $instance['id']: '';
		echo '<p>
			<label style = "padding-right:10px; for="'.$this->get_field_name('id').'">Numero du tirage</label>
			<span></br></span>
			<input id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'" type="text" value="'.$id.'"/>
			<span></br></span>
			
			<label style = "padding-right:29px;" for="'.$this->get_field_name('title').'">Titre du tirage</label>
			<span></br></span>
			<input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'"/>
			<span></br></span>
			
			<label style = "padding-right:29px; for="'.$this->get_field_name('date').'"> Date du tirage</label>
			<span></br></span>
			<input id="'.$this->get_field_id('date').'" name="'.$this->get_field_name('date').'" type="date" value="'.$date.'"/>
		</p>';

		//Appele de la fonction pour enregistrer le tirage en base
        global $wpdb;
        $id = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}draw_list WHERE id='$id'");
        if(is_null($id)){
            Mon_Plugin::save_draw(apply_filters('widget_title', $instance['title']),apply_filters('widget_date', $instance['date']), apply_filters('id', $instance['id']));
        } elseif ($id) {
            Mon_Plugin::update_draw(apply_filters('widget_title', $instance['title']),apply_filters('widget_date', $instance['date']), apply_filters('id', $instance['id']));
        }
	}

	//Affichage en Front
	public function widget($args, $instance)
	{
		$titreTirage = apply_filters('widget_title', $instance['title']);
		$dateTirage = apply_filters('widget_date', $instance['date']);
		$id = apply_filters('widget_id', $instance['id']);
		$today = new DateTime();
		$dateFin = new DateTime($dateTirage);
		//calcule le nombre de jours restant avant tirage
		$nbJour = $today->diff($dateFin)->format('%d');


		echo '<div style ="border: 1px outset black;padding:8px;margin:6px;">';

		echo $args['before_widget'];
		echo $args['before_title'];
		echo $titreTirage;
        echo '</br>dans ' . $nbJour . ' jour(s)';
		echo ' le ' . date("d/m/y", strtotime($dateTirage));

		echo $args['after_title'];

		echo '        
            <form style ="margin-bottom:-20px";" action="" method="post">
                  
                    <label for="user_draw_email">Votre email:</label>
                    <span></br></span>
                    <input id="user_draw_email" name="user_draw_email" type="email" />
                    
                    <span></br></span>
                    <label for="user_draw_firstname">Votre prénom:</label>
                    <span></br></span>
                    <input id="user_draw_firstname" name="user_draw_firstname" type="text" />
            
                    <span></br></span>
                    <label for="user_draw_lastname">Votre nom:</label>
                    <span></br></span>
                    <input id="user_draw_lastname" name="user_draw_lastname" type="text"/>
                    
                    <span></br></span>
                    <input type="hidden" name="id" value="'.$id.'" ">
                    <span></br></span>
                    <input type="submit"/>
            </form>
            ';

            //Si le nombre de jours restant est a 0, on effectue le tirage
            if ($nbJour == 0) {
                Mon_Plugin::draw_user($id);
                $winner = Mon_Plugin::return_winner($id);
                echo "<h4>Bravo a ".$winner->lastname." ".$winner->firstname.", grand gagnant de ce tirage</h4>";
            }

		echo '</div>
		';

		echo $args['after_widget'];
	}
}
?>
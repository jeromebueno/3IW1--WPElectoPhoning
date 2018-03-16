<?php
/*
Plugin Name: Tirage au sort
Description: Tirage au sort d'un utilisateur
Version: 0.1
*/
class Mon_Plugin
{

	public function __construct()
	{
		include_once plugin_dir_path(__FILE__).'/draw.php';
		register_activation_hook(__FILE__, array('Mon_Plugin', 'install'));
		register_uninstall_hook(__FILE__, array('Mon_Plugin', 'uninstall'));
		add_action('wp_loaded', array($this, 'save_user'));
	}

	public static function install() {
		global $wpdb;
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}user_draw_list (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname varchar(255),gagnant TINYINT(1) DEFAULT 0,idTirage INT(255));");
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}draw_list (id INT(255) PRIMARY KEY, title VARCHAR(255) NOT NULL, date DATE NOT NULL,jouer TINYINT (1) DEFAULT 0);");
	}

	public static function uninstall() {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}user_draw_list;");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}draw_list;");
	}


	//Enregistre le tirage en base
	public static function save_draw($titreTirage,$dateTirage, $idTirage) {
		global $wpdb;
		$wpdb->insert("{$wpdb->prefix}draw_list", array('title' => $titreTirage,'date' => $dateTirage, 'id' => $idTirage));
	}

	//Enregistre le participant en base
	public function save_user()
    {
        if (isset($_POST['user_draw_email']) && isset($_POST['user_draw_firstname']) && isset($_POST['user_draw_lastname']) &&
            !empty($_POST['user_draw_email']) && !empty($_POST['user_draw_firstname']) && !empty($_POST['user_draw_firstname'])) {

            global $wpdb;
            $email = $_POST['user_draw_email'];
            $firstname = $_POST['user_draw_firstname'];
            $lastname = $_POST['user_draw_lastname'];
            $tirageId = $_POST['id'];

            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}user_draw_list WHERE email='$email' AND idTirage = '$tirageId'");

            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}user_draw_list", array('email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'idTirage' => $tirageId));
            }
        }
    }


    public static function update_draw($titre, $date, $id)
    {
        global $wpdb;
        $wpdb->update("{$wpdb->prefix}draw_list",array('title' => $titre, 'date' => $date),array('id' => $id));
    }

	//Si le tirage n'as pas deja été tirer, tire au hasard un participant et lui attribue un booleen "gagnant" a 1
	public static function draw_user($id){
		global $wpdb;

		$row = $wpdb->get_row("SELECT jouer FROM {$wpdb->prefix}draw_list WHERE id = '$id'");

		if($row->jouer == 0) {
			//remet tout les participant à 0, met le champs gagnant d'un participant à 1 et passe le tirage en "jouer" (id = 1)
            $wpdb->update("{$wpdb->prefix}user_draw_list",array('gagnant' => 0),array('idTirage' => $id));
            $wpdb->get_row("UPDATE {$wpdb->prefix}user_draw_list SET gagnant = 1 WHERE idTirage = '$id' ORDER BY RAND( ) LIMIT 1;");
            $wpdb->update("{$wpdb->prefix}draw_list",array('jouer' => 1),array('id' => $id));
		}
	}

	//retourne le gagnant, soit le participant avec le champ gagnant a 1
	public static function return_winner($id){
		global $wpdb;
		$winner =  $wpdb->get_row("SELECT * FROM {$wpdb->prefix}user_draw_list WHERE gagnant = 1 AND idTirage='$id';");
		return $winner;
	}
}
new Mon_Plugin();

?>
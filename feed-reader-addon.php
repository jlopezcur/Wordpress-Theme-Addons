<?php
/**
 * Widget lector de feeds para loispereiro.blogaliza.org para usar en el tema
 * visual no a través de plugins.
 * 
 * Obtenemos las entradas de cualquier feed (incluso externo).
 * 
 * Utiliza la librería zRSSFeed que debe estar en la raiz del tema visual en
 * /js y además debe estar incluida en la cabecera de la web como ademas de necesitar
 * jQuery cargado también
 * <code>
 * <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.4.min.js"></script>
 * <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.zrssfeed.min.js"></script>
 * </code>
 * @link http://www.zazar.net/developers/zrssfeed/
 * 
 * @author Algueirada
 * @version 1.0
 * @package loispereiro
 * @subpackage libs
 */

/**
 * Esta clase gestiona la obtención, procesado y visualización
 * del feed indicado.
 * @package loispereiro
 * @subpackage libs
 */
class lpFeedReaderWidget {
	
	/**
	 * La url del feed
	 */
	var $_feed_url = "";
	
	/**
	 * Salida del widget
	 */
	var $_out = "";
	
	/**
	 * Constructor de la clase que inicializa las variables,
	 * procesa y prepara el contenido a mostrar.
	 * @param string $feed La URL del feed
	 */
	function lpFeedReaderWidget($feed) {
		$this->_feed_url = $feed;
		$this->_get_content();
	}
	
	/**
	 * Genera el contenido del widget
	 */
	function _get_content() {
		$this->_out = '<div id="feed-'.md5($this->_feed_url).'"></div>
			<script type="text/javascript">
			$(document).ready(function () {
	  			$("#feed-'.md5($this->_feed_url).'").rssfeed("'.$this->_feed_url.'", {
	    			limit: 5,
	    			content: false,
	    			header: false
	  			});
			});
			</script>';
	}
	
	/**
	 * Devuleve el codigo del widget
	 * @return string Código del widget
	 */
	function get_widget() {
		return $this->_out;
	}
	
	/**
	 * Dump function
	 */
	function dump() {
		print_r($this->_feed_url);
	}
}
?>
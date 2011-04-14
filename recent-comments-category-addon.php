<?php
/**
 * Widget de comentarios recientes para loispereiro.blogaliza.org para usar en el tema
 * visual no a través de plugins.
 * 
 * Obtenemos los últimos comentarios de los post de una categoría
 * concreta.
 * 
 * @author Algueirada
 * @version 1.0
 * @package loispereiro
 * @subpackage libs
 */

/**
 * Esta clase gestiona la obtención, procesado y visualización
 * de los comentarios referentes a post de una categroría concreta.
 * @package loispereiro
 * @subpackage libs
 */
class lpLastCommentsCategoryWidget {
	
	/**
	 * La categoría sobre la que se buscan comentarios
	 */
	var $_category = "";
	
	/**
	 * Salida del widget
	 */
	var $_out = "";
	
	/**
	 * Constructor de la clase que inicializa las variables,
	 * procesa y prepara el contenido a mostrar.
	 * @param string $category Categoría
	 */
	function lpLastCommentsCategoryWidget($category) {
		$this->_category = $category;
		$this->_get_comments();
	}
	
	function _get_comments() {
		$show_comments = 10; $i = 0;
		$comments = get_comments("number=50&status=approve");
		
		$this->_out = '<ul>';
		foreach ($comments as $comment) {
			$comm_post_id = $comment->comment_post_ID;
			if (!in_category($this->_category, $comm_post_id)) continue;
			$i++;
			$post = get_post($comm_post_id);
			$this->_out .= '<li>
				<div style="float:left; padding:5px;"><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($comment->comment_author_email))).'?s=25" /></div>
				<h4>'.$comment->comment_author.' en <a href="'.get_permalink($comm_post_id).'">'.$post->post_title.'</a></h4>
				<small>Publicado el '.date('j F, Y \a \l\a\s G:s', $comment->comment_date).'</small>
				'.$comment->comment_content.'
				<div style="clear:both;"></div></li>';
			if ($i >= $show_comments) break;
		}
		$this->_out .= '</ul>
		<div style="height: 10px;"></div>';
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
		print_r($this->_category);
		print_r($this->_out);
		print_r($this->_post);
	}
}
?>
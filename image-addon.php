<?php
/**
 * Widget de imagen para loispereiro.blogaliza.org para usar en el tema
 * visual no a través de plugins.
 * 
 * Obtenemos el último post de una categoría y le extraemos
 * la primera imagen que muestra.
 * 
 * Utiliza la librería phpThumb que debe estar en la raiz del tema visual en
 * /vendors/phpthumb y además debemos habilitar permisoss para la cache en /vendors/phpthumb/cache
 * @link http://phpthumb.sourceforge.net/
 * 
 * @author Algueirada
 * @version 1.0
 * @package loispereiro
 * @subpackage libs
 */

require_once(TEMPLATEPATH . '/vendors/phpthumb/phpthumb.class.php');

/**
 * Esta clase gestiona la obtención, procesado y visualización
 * de la última imagen publicada en una determinada categoría.
 * @package loispereiro
 * @subpackage libs
 */
class lpLastImageWidget {
	
	/**
	 * La categoría sobre la que se busca el último post
	 */
	var $_category = "";
	
	/**
	 * Array de datos del post que son útiles:
	 * - content
	 * - permalink
	 * - title
	 * - date
	 * - time
	 */
	var $_post = array();
	
	/**
	 * Array de datos de la imagen:
	 * - url
	 * - embed
	 * - width
	 * - height
	 */
	var $_image = array();
	
	/**
	 * Constructor de la clase que inicializa las variables,
	 * procesa y prepara el contenido a mostrar.
	 * @param string $category Categoría
	 * @param integer $width Ancho del widget (por defecto 320)
	 * @param integer $height Alto del widget (por defecto 237)
	 */
	function lpLastImageWidget($category, $width = 255, $height = 175) {
		$this->_category = $category;
		$this->_image['width'] = $width;
		$this->_image['height'] = $height;
		$this->_image['embed'] = "";
		$this->_get_last_post();
		$this->_get_image_data();
		$this->_get_image_embed();
	}
	
	/**
	 * Obtiene los datos del último post de la categoría
	 * seleccionada por el constructor.
	 */
	function _get_last_post() {
		global $post;
		$args = array('numberposts' => 1, 'category_name' => $this->_category );
		$myposts = get_posts($args);
		
		foreach($myposts as $post) {
			setup_postdata($post);
			$this->_post['content'] = get_the_content(); 
			$this->_post['permalink'] = get_permalink();
			$this->_post['title'] = get_the_title();
			$this->_post['date'] = get_the_date();
			$this->_post['time'] = get_the_time();
		}
	}
	
	/**
	 * Obtiene los datos de la primera imagen publicada del post
	 */
	function _get_image_data() {
		preg_match('/(img|src)=("|\')[^"\'>]+/i', $this->_post['content'], $output);
		$url = preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3", $output[0]);
		$info = pathinfo($url);
	   	if (isset($info['extension'])) {
	       	if (($info['extension'] == 'jpg') ||
	       	($info['extension'] == 'jpeg') ||
	       	($info['extension'] == 'gif') ||
	       	($info['extension'] == 'png'))
	       	$this->_image['url'] = $url;
		}
	}
	
	/**
	 * Crea el codigo para embeber la imagen utilizando phpThumb
	 */
	function _get_image_embed() {
		$phpThumb = new phpThumb();
		$phpThumb->setSourceFilename($this->_image['url']);
		$output_filename = TEMPLATEPATH . '/vendors/phpthumb/cache/'.md5($this->_image['url']).'.'.$phpThumb->config_output_format;
		$phpThumb->setParameter('w', $this->_image['width']);
		$phpThumb->setParameter('h', $this->_image['height']);
		if ($phpThumb->GenerateThumbnail()) {
			$phpThumb->RenderToFile($output_filename);
		}
		$uri = get_bloginfo('template_url') . '/vendors/phpthumb/cache/'.md5($this->_image['url']).'.'.$phpThumb->config_output_format;
		
		$this->_image['embed'] = '<p style="text-align:center;">
		<a href="'.$this->_post['permalink'].'">
		<img src="'.$uri.'" />
		</a>
		</p>
		<a href="'.$this->_post['permalink'].'">'.$this->_post['title'].'</a>
		<small>Publicado o '.$this->_post['date'].' ás '.$this->_post['time'].'</small>
		<div style="text-align: right;"><a href="http://loispereiro.blogaliza.org/category/fotografias/">m&aacute;is</a></div>
		<div style="height: 10px;"></div>';
	}
	
	/**
	 * Devuleve el codigo para embeber la imagen
	 * @return string Código para embeber la imagen
	 */
	function get_widget() {
		return $this->_image['embed'];
	}
	
	/**
	 * Dump function
	 */
	function dump() {
		print_r($this->_category);
		print_r($this->_image);
		print_r($this->_post);
	}
}
?>
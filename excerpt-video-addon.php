<?php
/**
 * Widget de vídeo para loispereiro.blogaliza.org para usar en el tema
 * visual no a través de plugins.
 * 
 * Obtenemos el último post de una categoría y le extraemos
 * el primer vídeo que muestra.
 * 
 * @author Algueirada
 * @version 1.0
 * @package loispereiro
 * @subpackage libs
 */

/**
 * Esta clase gestiona la obtención, procesado y visualización
 * del último vídeo publicado en una determinada categoría.
 * @package loispereiro
 * @subpackage libs
 */
class lpExcerptVideoWidget {
	
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
	 * Array de datos del vídeo:
	 * - type (youtube, vimeo, qik, bambuser)
	 * - channel (For bambuser only)
	 * - id
	 * - embed
	 * - width
	 * - height
	 */
	var $_video = array();
	
	/**
	 * Constructor de la clase que inicializa las variables,
	 * procesa y prepara el contenido a mostrar.
	 * @param string $category Categoría
	 * @param integer $width Ancho del widget (por defecto 320)
	 * @param integer $height Alto del widget (por defecto 237)
	 */
	function lpExcerptVideoWidget($width = 255, $height = 175) {
		$this->_category = $category;
		$this->_video['width'] = $width;
		$this->_video['height'] = $height;
		$this->_video['embed'] = "";
		$this->_get_last_post();
		$this->_get_video_data();
		$this->_get_video_embed();
	}
	
	/**
	 * Obtiene los datos del último post de la categoría
	 * seleccionada por el constructor.
	 */
	function _get_last_post() {
		global $post;
		
		//setup_postdata($post);
		$this->_post['content'] = get_the_content(); 
		$this->_post['permalink'] = get_permalink();
		$this->_post['title'] = get_the_title();
		$this->_post['date'] = get_the_date();
		$this->_post['time'] = get_the_time();
	}
	
	/**
	 * Obtiene los datos del primer vídeo publicado del post
	 */
	function _get_video_data() {
		// YouTube
		preg_match('#(http://www.youtube.com)?/(v/([-|~_0-9A-Za-z]+)|watch\?v\=([-|~_0-9A-Za-z]+)&?.*?)#i', $this->_post['content'], $output);
		if (isset($output[3]) && !empty($output[3])) {
			$this->_video['type'] = 'youtube';
			$this->_video['id'] = $output[3];
			return;
		}
		if (isset($output[4]) && !empty($output[4])) {
			$this->_video['type'] = 'youtube';
			$this->_video['id'] = $output[4];
			return;
		}
		
		// Vimeo
		preg_match('#(http://player.vimeo.com)?/video/([-|~_0-9A-Za-z]+)#i', $this->_post['content'], $output);
		if (isset($output[2]) && !empty($output[2])) {
			$this->_video['type'] = 'vimeo';
			$this->_video['id'] = $output[2];
			return;
		}
		
		// Vimeo 2
		//http://vimeo.com/moogaloop.swf?clip_id=21708567&server=vimeo.com&show_title=1&show_byline=1&show_portrait=1&color=00ADEF&fullscreen=1&autoplay=0&loop=0
		preg_match('#http://(?:\w+.)?vimeo.com/(?:video/|moogaloop\.swf\?clip_id=)(\w+)#i', $this->_post['content'], $output);
		if (isset($output[1]) && !empty($output[1])) {
			$this->_video['type'] = 'vimeo2';
			$this->_video['id'] = $output[1];
			return;
		}
	
		// Qik
		preg_match('#streamID\=([0-9A-Za-z]+)(&?|&amp;)#i', $this->_post['content'], $output);
		if (isset($output[1]) && !empty($output[1])) {
			$this->_video['type'] = 'qik';
			$this->_video['id'] = $output[1];
			return;
		}
		
		// Bambuser
		preg_match('#(http://static.bambuser.com/r/player.swf\?vid\=)([0-9]+)#i', $this->_post['content'], $output);
		if (isset($output[2]) && !empty($output[2])) {
			$this->_video['type'] = 'bambuser';
			$this->_video['id'] = $output[2];
			return;
		}
	}
	
	/**
	 * Crea el codigo para embeber el vídeo según el portal
	 */
	function _get_video_embed() {
		$this->_video['embed'] = '<p>';
		
		// YouTube
		if ($this->_video['type'] == 'youtube') {
			$this->_video['embed'] .= '<object width="'.$this->_video['width'].'" height="'.$this->_video['height'].'">
			<param name="movie" value="http://www.youtube.com/v/'.$this->_video['id'].'?fs=1&amp;hl=gl_ES"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<param NAME="wmode" VALUE="transparent">
			<embed src="http://www.youtube.com/v/'.$this->_video['id'].'?fs=1&amp;hl=es_ES" wmode="transparent" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'"></embed>
			</object>';
		
		// Vimeo
		} else if ($this->_video['type'] == 'vimeo') {
			$this->_video['embed'] .= '<iframe src="http://player.vimeo.com/video/'.$this->_video['id'].'?title=0&amp;byline=0&amp;portrait=0" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'" frameborder="0"></iframe>';
		
		// Vimeo2
		} else if ($this->_video['type'] == 'vimeo2') {
			$this->_video['embed'] .= '<object width="'.$this->_video['width'].'" height="'.$this->_video['height'].'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$this->_video['id'].'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='.$this->_video['id'].'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'"></embed></object>';
		
		
		// Qik - falta el id
		} else if ($this->_video['type'] == 'qik') {
			$this->_video['embed'] .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'" id="qikPlayer" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="allowFullScreen" value="true" />
			<param name="movie" value="http://qik.com/swfs/qikPlayer5.swf" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#333333" />
			<param NAME="wmode" VALUE="transparent">
			<param name="FlashVars" value="streamID='.$this->_video['id'].'&amp;autoplay=false" />
			<embed src="http://qik.com/swfs/qikPlayer5.swf" quality="high" wmode="transparent" bgcolor="#333333" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'" name="qikPlayer" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="streamID='.$this->_video['id'].'&amp;autoplay=false"></embed>
			</object>';
						
		// Bambuser
		} else if ($this->_video['type'] == 'bambuser') {
			$this->_video['embed'] .= '<object id="bplayer" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'">
			<embed name="bplayer" src="http://static.bambuser.com/r/player.swf?vid='.$this->_video['id'].'" type="application/x-shockwave-flash" width="'.$this->_video['width'].'" height="'.$this->_video['height'].'" allowfullscreen="true" allowscriptaccess="always" wmode="opaque"></embed>
			<param name="movie" value="http://static.bambuser.com/r/player.swf?vid='.$this->_video['id'].'"></param>
			<param name="allowfullscreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<param name="wmode" value="transparent"></param>
			</object>';
		}
		
		$this->_video['embed'] .= '</p>';
	}
	
	/**
	 * Devuleve el codigo para embeber el vídeo
	 * @return string Código para embeber el vídeo
	 */
	function get_widget() {
		return $this->_video['embed'];
	}
	
	/**
	 * Dump function
	 */
	function dump() {
		print_r($this->_category);
		print_r($this->_video);
		print_r($this->_post);
	}
}

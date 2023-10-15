<?php

/**
 * Derechos de autor por Hector Carrillo ( hector_o_c@hotmail.com )
 * Ultima actualizacion: 30/Ene/2015
 *
 * Autorizado en virtud de la Licencia de Apache, Versión 2.0 (la "Licencia");
 * se prohíbe utilizar este archivo excepto en cumplimiento de la Licencia.
 * Podrá obtener una copia de la Licencia en:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * A menos que lo exijan las leyes pertinentes o se haya establecido por escrito,
 * el software distribuido en virtud de la Licencia se distribuye “TAL CUAL”, SIN
 * GARANTÍAS NI CONDICIONES DE NINGÚN TIPO, ya sean expresas o implícitas. Véase
 * la Licencia para consultar el texto específico relativo a los permisos y
 * limitaciones establecidos en la Licencia.
 */

namespace HCStudio;

class Paginator {

	function __construct($quantity, $page, $total, $url='', $var='', $prev='Prev', $next='Next', $class=''){
		$this->Total = $total;
		$this->textPrev = $prev;
		$this->textNext = $next;
		$this->prefUrl = ((empty($url))?'?':$url.'&').((empty($var))?'page=':$var.'=');
		$this->Css = $class;
        $this->page = ($page>0)?$page:0;
        $this->quantity = ($quantity > 0) ? $quantity : 0;
        $this->from = $page * $quantity;
        $this->numPages = ceil($this->Total / $this->quantity);
        $this->prefUrl = preg_replace('[\?\&]', '?',preg_replace('[\&*\&]', '&amp;', $this->prefUrl));    //Quita     ?& = ?
	}

	function showPaginatorBootstrap($getContent = false)
	{
		if($this->numPages > 1)
		{
			$content .= '<div class="col-md-12 text-center">';
			$content .= '<nav>';
			$content .= '<ul class="pagination">';

			if($this->page > 0)
				$content .= '<li>
			      <a class="pagination" data-page="'.$i.'"  href="'.$this->prefUrl.($this->page-1).'" aria-label="Previous">
			        <span aria-hidden="true">&laquo;</span>
			      </a>
			    </li>';

			if($this->numPages > 1)
	            for($i = 1; $i <= $this->numPages; $i++)
					if($i == $this->page)
						$content .= '<li><a class="current pagination" data-page="'.$i.'" href="'.$this->prefUrl.$i.'">'.$i.'</a></li>';
	                else
						$content .= '<li><a class="pagination" data-page="'.$i.'" href="'.$this->prefUrl.$i.'">'.$i.'</a></li>';

			if($this->page < $this->numPages-1)
			    $content .= '<li>
			      <a class="pagination" data-page="'.($this->page+1).'" href="'.$this->prefUrl.($this->page+1).'" aria-label="Next">
			        <span aria-hidden="true">&raquo;</span>
			      </a>
			    </li>';

			$content .= '</ul>';
			$content .= '</nav>';
			$content .= '</div>';
		}

		if($getContent) return $content;
        else echo $content;
	}
	function showPaginator(){
		// Clear prefUrl
        $this->prefUrl=preg_replace('[\&*\&]', '&amp;', $this->prefUrl);    //Quita las && = & repetidas
        $this->prefUrl=preg_replace('[\?\&]', '?',$this->prefUrl);    //Quita     ?& = ?
		// Set total pages
        $this->numPages = ceil($this->Total/$this->quantity);
		// Show container paginate
		echo '<div class="pagination'.((empty($this->Css))?'':' '.$this->Css).'">';
		// Show prev link
		if($this->page > 0)
			print '<a class="paginate-prev" href="'.$this->prefUrl.($this->page-1).'">'.$this->textPrev.'</a>';

		// Show number pages
		if($this->numPages > 1){
            for($i = 0; $i < $this->numPages; $i++){
				// Show current page
				if($i==$this->page){
					echo '<span class="paginate-current">'.$i.'</span>';
                }elseif($i == $this->page+1 || $i == $this->page+2 || $i == $this->page - 1
				|| $i == $this->page - 2 || $i == 0 || $i == $this->numPages-1) {
                    echo '<a class="paginate-link" href="'.$this->prefUrl.$i.'">'.$i.'</a>';
                }
                elseif ($i == $this->page - 3) {
                    echo "<span>...</span>";
                }
                elseif ($i == $this->page + 3) {
                    echo "<span>...</span>";
                }
            }
        }
		// Show next link
		if($this->page < $this->numPages-1)
			echo '<a class="paginate-next" href="'.$this->prefUrl.($this->page+1).'">'.$this->textNext.'</a>';

		echo '</div>';
    }
}

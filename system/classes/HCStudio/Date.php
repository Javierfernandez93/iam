<?php

namespace HCStudio;

class Date {

	private $timestamp;

	function __construct($fecha=null,$orden=null) {
		if(!is_null($fecha)) {
			if($this->isDate($fecha,$orden)) $this->timestamp = $this->toTimestamp($fecha,$orden);
			else $this->timestamp = $fecha;
		}
		if(is_null($fecha)) $this->timestamp = time();
	}
	# seteamos fecha
	public function setDate($fecha,$orden=null) {
		if($this->isDate($fecha,$orden)) $this->timestamp = $this->toTimestamp($fecha,$orden);
		else $this->timestamp = $fecha;
		return $this;
	}
	# obtenemos fecha
	public function getDate() {
		return date('Y-m-d',$this->timestamp);
	}
	# obtenemos tiempo en unix
	public function getTimestamp(){
		return $this->timestamp;
	}
	# obtenemos fecha formateada
	public function humanFormat() {
		return $this->getDayname().", ".$this->getDay(true)." de ".$this->getMonthname()." de ".$this->getYear();
	}
	# obtenemos fecha formateada segun criterios
	public function format($string) {
		$string = str_replace('~d',$this->getDay(true), $string);
		$string = str_replace('~m',$this->getMonth(true), $string);
		$string = str_replace('~a',$this->getYear(true), $string);
		$string = str_replace('^d',$this->getDayname(), $string);
		$string = str_replace('^m',$this->getMonthname(), $string);
		$string = str_replace('|d',$this->getDayname(true), $string);
		$string = str_replace('|m',$this->getMonthname(true), $string);
		$string = str_replace('*d',$this->getDay(), $string);
		$string = str_replace('*m',$this->getMonth(), $string);
		$string = str_replace('*a',$this->getYear(), $string);
		return $string;
	}
	# valida fecha
	private function isDate($date,$orden=null){
		$parse = $this->date_parse($orden, $date);
		if (checkdate(@$parse["month"], @$parse["day"], @$parse["year"])) return true;
		else return false;
	}
	# parseamos fecha mediante formato de fecha
	private function date_parse($format,$date) {
		$dMask = array('H'=>'hour','i'=>'minute','s'=>'second','y'=>'year','m'=>'month','d'=>'day');
		$format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);
		$date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);
		$dt = array();
		foreach ($date as $k => $v) {
			if (isset($dMask[$format[$k]]))  @$dt[$dMask[$format[$k]]] .= $v;
		}
		return $dt;
	}
	# valida timestamp
	private function isTimestamp($timestamp)	{
		return (is_numeric($timestamp) && (int)$timestamp==$timestamp);
	}
	# convierte fecha en timestamp
	private function toTimestamp($date,$orden=null){
		$parse = $this->date_parse($orden, $date);
		return strtotime("{$parse["month"]}/{$parse["day"]}/{$parse["year"]}");
	}
	# obtenemos mes
	private function getMonth($cero=false) {
		if($cero) return date('n',$this->timestamp);
		return date('m',$this->timestamp);
	}
	# obtenemos dia
	private function getDay($cero=false) {
		if($cero) return date('j',$this->timestamp);
		return date('d',$this->timestamp);
	}
	# obtenemos año
	private function getYear($short=false) {
		if($short) return date('y',$this->timestamp);
		return date('Y',$this->timestamp);
	}
	# obtenemos nombre del dia
	public function getDayname($short=false) {
		$dayShort=array('Dom','Lun','Mar','Mié','Jue','Vie','Sáb');
		$day=array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		if($short) return $dayShort[date('w',$this->timestamp)];
		return $day[date('w',$this->timestamp)];
	}
	# obtenemos nombre del mes
	public function getMonthname($short=false) {
		$monthShort=array(1=>'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
		$month=array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		if($short) return $monthShort[$this->getMonth(true)];
		return $month[$this->getMonth(true)];
	}
	# obtenemos arreglo con rango de fechas
	public function dateRange($first,$last,$step='+1 month',$format='d/m/Y') {
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);
		while( $current <= $last ) {
			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}
		return $dates;
	}
}

?>
<?php

namespace JFStudio;

use HCStudio\Connection;

class Layout
{
	# const vars
	const ROOT = "../";

	public static $DISABLE_CACHE = true;
	# public vars
	public $page_name = "";
	public $layout = "";
	public $view = "";
	public $tags = null;
	public $js_path = null;
	public $css_path = null;
	public $css_scripts = null;
	public $js_scripts = null;
	public $path = "";
	public $vars = [];
	public $warnings = [];
	public $_root = null;
	public $layout_root = null;
	public $virtual_view= null;
	public $view_root = null;
	public $content_virtual_view = null;
	public $pretitle = 'IAM - ';
	public $modules = [];
	private static $instance;

	public static function getInstance()
 	{
	    if(self::$instance instanceof self === false) {
	      self::$instance = new self;
	    }

	    return self::$instance;
 	}

	public function init(string $page_name = 'Página',$view = false,$layout = false,$root = "",$layout_root = "../../",$view_root = false,$virtual_view = false)
	{
		$this->layout_root = $layout_root ? $layout_root : $root;
		$this->view_root = $view_root ? $view_root : $root;
		$this->virtual_view = $virtual_view ? $virtual_view : $virtual_view;

		$this->_root = $root;
		
		if(isset($this->css_path,$this->js_path) === false) {
			$this->setScriptPath(Connection::getMainPath().'/src/');
		}

		$this->setPageName($this->pretitle.$page_name);
		$this->getPath();
		$this->setLayout($layout);
		$this->setView($view);
		$this->setDefaultModules();
	}

	public function __destruct() { }

	public function __clone() { }

	private function getPath()
	{
		$this->path = $_SERVER['PHP_SELF'];
		$this->path = explode("/", $this->path);
		$this->path = $this->path[count($this->path)-2];

		return $this->path;
	}

	private function getPageName()
	{
		return $this->page_name;
	}

	public function makeVirtualFile() 
	{
		$file = fopen($this->view, "w") or die("Unable to open file!");
		fwrite($file, $this->getContentVirtualView());
		fclose($file);
	}

	public function getContentVirtualView() 
	{
		return $this->content_virtual_view;
	}

	public function setContentVirtualView($content_virtual_view = null) 
	{
		$this->content_virtual_view = $content_virtual_view;
	}

	private function setPageName(string $page_name = null)
	{
		$this->page_name = $page_name;
	}

	private function setView($view = null)
	{
		if(isset($view) === true)
		{
			if($this->virtual_view === false)
			{
				$this->view = $this->view_root.'view/'.$view.'.view.php';

				if(file_exists($this->view) === true) return true;
				else {
					$this->warnings['ERROR_VIEW'] = [
						'message' => 'view_not_found',
						'path' => $this->view
					];
					$this->view = false;
					$this->showWarnings();
					exit();
				}
			} else {
				$this->view = $view;
			}
		}
	}

	private function showWarnings()
	{
		echo count($this->warnings) . '<n style="color:red"> WARNING ! <n><br>';

		foreach ($this->warnings as $key => $_warning)
		{
			echo '<n style="color:green">';
			echo ' [ ' . $key . ' ] ';
			if(is_array($_warning))
			{
				foreach ($_warning as $value)
					echo $value.'<br>';
			} else echo $_warning;
			echo '</n>';
		}
	}

	private function setLayout(string $layout = null)
	{
		if(isset($layout) === true)
		{
			$this->layout = $this->layout_root.'layout/'.$layout.'.layout.php';

			if(file_exists($this->layout) === true) return true;
			else {
				$this->warnings['ERROR_LAYOUT'] = [
					'message' => 'layout_not_found',
					'path' => $this->layout
				];
				$this->layout = false;
				$this->showWarnings();
				exit();
			}
		}
	}


	public function setTags(array $tags = null)
	{
		$this->tags = $tags;
	}

	public function __invoke(bool $get_content = false,string $layout_content = null,string $view_content = null)
	{
		$this->display($get_content,$layout_content,$view_content);
	}

	public static function compress(string $buffer = null) {

		$search = [
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		];
	
		$replace = [
			'>',
			'<',
			'\\1',
			''
		];
	
		$buffer = preg_replace($search, $replace, $buffer);
	
		return $buffer;
	}

	public function display(bool $get_content = false,string $layout_content = null,string $view_content = null)
	{
		if(isset($get_content,$this->layout) === true)
		{
			$layout_content = isset($layout_content) === true ? $layout_content : $this->getLayoutContent();
			$view_content = isset($view_content) === true ? $view_content : $this->getViewContent();

			$content = $this->runView($layout_content,$view_content);

			if($get_content === true) return $content;

			echo self::compress($content);
		} else {
			return false;
		}
	}

	public function replaceView(string $module = null,string $haystack = null,string $needle = null)
	{
		return str_replace("{{{$module}}}",$haystack,$needle);
	}

	public function replaceTags(array $tags = null,string $content = null)
	{
		foreach($tags as $tag => $value)
		{
			$content = $this->replaceView("{$tag}",$value,$content);
		}

		return $content;
	}

	public function runView($layout_content, $view_content)
	{
		# replacing title page
		$content = $this->replaceView('title',$this->page_name,$layout_content);

		# replacing content
		$content = $this->replaceView('content',$view_content,$content);
		
		# replacing tags
		if(isset($this->tags) === true)
		{
			$content = $this->replaceTags($this->tags,$content);
		}

		# replacing js scripts
		if(isset($this->js_scripts) === true)
			$content = $this->replaceView('js_scripts',$this->js_scripts,$content);
		else $content = $this->replaceView('js_scripts','',$content);

		# replacing css scripts
		if(isset($this->css_scripts) === true)
			$content = $this->replaceView('css_scripts',$this->css_scripts,$content);
		else $content = $this->replaceView('css_scripts','',$content );

		# looking for modules
		if(isset($this->modules) === true)
		{
			foreach ($this->modules as $module)
			{
				if($find_start = strpos($content, $module))
				{
					$module_size = strlen($module);
					$data = substr($content, ($find_start + $module_size) );

					if($find_end = strpos($data, $module))
					{
						$data = substr($data, 0, $find_end);

						# deleting old data from html
						$content = str_replace( $module . $data . $module, "", $content );

						# adding new data content for layout
						$content = str_replace( $module, $data, $content );
					} else $content = str_replace( $module, '', $content );
				}
			}
		}

		return $content;
	}

	public function setDefaultModules()
	{
		$this->setModule('aside');
		$this->setModule('footer');
		$this->setModule('headerController');
		$this->setModule('menu');
		$this->setModule('scripts_async');
		$this->setModule('metadata');
	}

	public function setModule(string $name = null)
	{
		if(isset($name) === true) {
			if(isset($this->modules["{{{$name}}}"]))
			{
				$this->modules[] = "{{{$name}}}";
			}
		}

		return true;
	}

	public function getViewContent()
	{
		if($this->virtual_view == false)
		{
			ob_start();
			extract($this->vars);
			require_once $this->view;
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		return $this->view;
	}

	public function getLayoutContent()
	{
		ob_start();
		extract($this->vars);
		require_once $this->layout;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function setVar($name = null,$value = null)
	{
		if(is_array($name) === true)
		{
			foreach($name as $key => $val)
			{
				$this->vars[$key] = $val;
			}
		} else if(is_string($name) === true && isset($value) === true) {
			$this->vars[$name] = $value;
		}
	}

	public function getHtml(string $layout_content = null,string $view_content = null,bool $trim = true)
	{
		if($trim === true) 
		{
			$content = preg_replace('/\s+/',' ',$this->display(true,$layout_content,$view_content));

			if($this->virtual_view === true) {
				unlink($this->view);
			}
		}

		return $content;
	}

	public function setScript(array $files_names = null)
	{
		if(isset($files_names) === true)
		{
			foreach ($files_names as $key => $value)
			{
				if(self::$DISABLE_CACHE === true)
					$value .= "?ver=".time();

				if(!strpos($value,".css") === false )
					$this->css_scripts[] = $value;
				else if( !strpos($value,".js") === false )
					$this->js_scripts[] = $value;
				else if(!strpos($value,".*") === false ) {
					$this->css_scripts[] = str_replace("*", "css", $value);
					$this->js_scripts[] = str_replace("*", "js", $value);
				}
			}

			if(isset($this->css_scripts) === true)
				$this->css_scripts = $this->setCssScripts();

			if(isset($this->js_scripts) === true)
				$this->js_scripts = $this->setJsScripts();
		}
	}

	public function isJsModule(string $script = null)
	{
		return strpos($script, ".module.") !== false || strpos($script, ".vue.") !== false;
	}

	public function setJsScripts()
	{
		$files = null;

		foreach ($this->js_scripts as $js_file_name) 
		{
			if($this->isJsModule($js_file_name) === true)
			{
				$files .= '<script type="module" src="'.$this->_root.$this->js_path.'js/'.$js_file_name.'"></script>';
			} else {
				$files .= '<script src="'.$this->_root.$this->js_path.'js/'.$js_file_name.'"></script>';
			}
		}

		return $files;
	}

	# function:: adds css scripts to "view".view.php
	# extended by setScript()
	public function setCssScripts()
	{
		$files = null;
		
		foreach ($this->css_scripts as $css_file_name)
		{
			$files .= '<link rel="stylesheet" type="text/css" href="'.$this->_root.$this->css_path.'css/'.$css_file_name.'">';
		}

		return $files;
	}

	public function setScriptPath(string $script_path = null)
	{
		$this->css_path = $script_path;
		$this->js_path = $script_path;
	}

	public function setScriptcss_path(string $css_path = null)
	{
		$this->css_path = $css_path;
	}

	public function setScriptjs_path(string $js_path = null)
	{
		$this->js_path = $js_path;
	}
}
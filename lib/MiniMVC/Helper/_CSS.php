<?php

class BaseHelperCss extends Helper
{
	protected static $files = array();
	protected static $filesToMerge = array();

	public function addFile($path, $media = 'all', $merge = true, $external = false, $key =  '')
	{
		if ($path && $media)
		{
			if (!$external && $merge && !isset(self::$filesToMerge[$key]))
			{
				self::$filesToMerge[$key] = (object)array('path'=>$path, 'media'=>$media);
			}
			elseif (!isset(self::$files[$key]))
			{
				self::$files[$key] = (object)array('path'=>($external) ? $path : BASEDIR.$path, 'media'=>$media);
			}
		}
	}

	public function add($file, $module = true, $media = 'all', $merge = true)
	{
		if ($module === true || $module === 1)
		{
			$module = $this->module;
		}
		$external = false;
		if ($module == 'Template')
		{
			if (Layout::getTemplateName() && file_exists(QDIR.'templates/'.Layout::getTemplateName().'/css/'.$file))
			{
				$key = 'template'.$file;
				$path = 'templates/'.Layout::getTemplateName().'/css/'.$file;
			}
			elseif (Layout::getTemplateName() && file_exists(QDIR.'templates/'.Layout::getTemplateName().'/'.$file))
			{
				$key = 'template'.$file;
				$path = 'templates/'.Layout::getTemplateName().'/'.$file;
			}
		}
		elseif ($module !== false)
		{
			if (Layout::getTemplateName() && file_exists(QDIR.'templates/'.Layout::getTemplateName().'/modules/'.$module.'/css/'.$file))
			{
				$key = 'module'.$module.$file;
				$path = 'templates/'.Layout::getTemplateName().'/modules/'.$module.'/css/'.$file;
			}
			elseif (Layout::getTemplateName() && file_exists(QDIR.'templates/'.Layout::getTemplateName().'/modules/'.$module.'/'.$file))
			{
				$key = 'module'.$module.$file;
				$path = 'templates/'.Layout::getTemplateName().'/modules/'.$module.'/'.$file;
			}
			elseif (file_exists(QDIR.'modules/'.$module.'/css/'.$file))
			{
				$key = 'module'.$module.$file;
				$path = 'modules/'.$module.'/css/'.$file;
			}
			elseif (file_exists(QDIR.'modules/'.$module.'/'.$file))
			{
				$key = 'module'.$module.$file;
				$path = 'modules/'.$module.'/'.$file;
			}
		}

		if (!isset($key))
		{
			if (file_exists(QDIR.'css/'.$file))
			{
				$key = $file;
				$path = 'css/'.$file;
			}
			elseif(!$merge)
			{
				$key = $file;
				$external = true;
				$path = $file;
			}
			else
			{
				return false;
			}
		}

		self::addFile($path, $media, $merge, $external, $key);
		return true;
	}

	public static function get()
	{
		return array_merge(self::$files, self::getMergedFiles());
	}

	public static function display()
	{
		$html = '';
		foreach ($this->get() as $currentCss)
		{
			$html .= '<link rel="stylesheet" type="text/css" href="'.$currentCss->path.'" media="'.$currentCss->media.'" />'."\n";
		}
		return $html;
	}

	public static function getMergedFiles()
	{
		$template = Layout::getTemplateName();
		$app = Apps::getCurrent();
		if (!$template)
		{
			return array();
		}
		$cssCache = Config::get('css_'.$app.'_'.$template);
		$cssMergedCache = Config::get('css_'.$app.'_'.$template.'_merged');

		$currentFiles = self::$filesToMerge;
		$mediaToMerge = array();

		if ($cssCache === false)
		{
			$cssCache = array();
		}
		if ($cssMergedCache === false)
		{
			$cssMergedCache = array();
		}

		$newFiles = array_merge($cssCache, $currentFiles);
		$deletedFiles = false;

		foreach ($cssMergedCache as $currentFile)
		{
			if (!is_file(QDIR.$currentFile->path))
			{
				$deletedFiles = true;
				break;
			}
		}

		if ($deletedFiles || $newFiles != $cssCache)
		{
			$merged = array();
			//get file data
			foreach ($newFiles as $filekey=>$filedata)
			{
				if (substr($filedata->path, -4) == '.css' && is_file(QDIR.$filedata->path) && is_readable(QDIR.$filedata->path))
				{
					$data = file_get_contents(QDIR.$filedata->path);
					$file_data = self::prepareFile($data, $filedata->path);
					if ($file_data)
					{
						foreach ($filedata->media as $currentmedia)
						{
							if (!isset($merged[trim($currentmedia)]))
							{
								$merged[trim($currentmedia)] = '';
							}
							$merged[trim($currentmedia)] .= "\n\n\n\n".'/* ========== MERGED FROM '.$filedata->path.' ========== */'."\n\n\n".$file_data;
						}
					}
				}
			}
			$newtime = time();
			foreach ($merged as $currentmedia=>$content)
			{
				$oldtime = isset($cssMergedCache[$currentmedia]->date) ? $cssMergedCache[$currentmedia]->date : false;
				if ($oldtime && file_exists(QDIR.'cache/css_'.$app.'_'.$template.'_'.trim($currentmedia).'_'.$oldtime.'.css'))
				{
					unlink(QDIR.'cache/css_'.$app.'_'.$template.'_'.trim($currentmedia).'_'.$oldtime.'.css');
				}

				$handle = fopen(QDIR.'cache/css_'.$app.'_'.$template.'_'.trim($currentmedia).'_'.$newtime.'.css', 'wb');
				fwrite($handle, $content);
				fclose($handle);

				$cssMergedCache[$currentmedia] = (object)array('path'=>BASEDIR.'cache/css_'.$app.'_'.$template.'_'.trim($currentmedia).'_'.$newtime.'.css', 'media'=>$currentmedia, 'date'=>$newtime);
			}

			Config::set('css_'.$app.'_'.$template, $newFiles);
			Config::set('css_'.$app.'_'.$template.'_merged', $cssMergedCache);
		}
		return $cssMergedCache;
	}

	protected static function prepareFile($data, $path)
	{
		$csspath = explode('/', $path);
		array_pop($csspath);
		$csspath = implode('/', $csspath).'/';
		if ($csspath == '/')
		{
			$csspath = '';
		}
		$regex = '#url[\s]*\(([^\)]*)\)#iU';
		preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);

		$search = array();
		$replace = array();
		foreach ($matches[1] as $key=>$url)
		{
			$url = trim(str_replace(array('"', "'"), '', $url));

			//don't touch absolute URIs
			if (substr($url, 0, 1) == '/' || substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')
			{
				continue;
			}
			else
			{
				$search[] = $matches[0][$key];
				$newpath_temp = explode('/', BASEDIR.$csspath.$url);
				$newpath = array();
				foreach ($newpath_temp as $path_index=>$path)
				{
					$newpath[$path_index] = $path;
					if ($path == '.')
					{
						array_pop($newpath);
					}
					if ($path == '..')
					{
						array_pop($newpath);
						array_pop($newpath);
					}
				}
				$newpath = implode('/', $newpath);
				$replace[] = 'url("'.DOMAIN.$newpath.'")';
			}
		}
		$data = str_replace($search, $replace, $data);

		//search for overloads
		$regex = '#url\("('.DOMAIN.BASEDIR.'.*)"\)#iU';
		preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);

		$search = array();
		$replace = array();
		foreach ($matches[1] as $key=>$url)
		{
			$start = mb_strlen(DOMAIN.BASEDIR, 'UTF-8');
			$staticUrl = explode('/',substr($url, $start));
			if (isset($staticUrl[0]) && $staticUrl[0] == 'modules' && isset($staticUrl[1]) && isset($staticUrl[2]))
			{
				$file = array();
				for ($i=2; $i<count($staticUrl); $i++)
				{
					$file[] = $staticUrl[$i];
				}
				$file = implode('/', $file);
				$module = $staticUrl[1];
			}
			elseif(isset($staticUrl[1]) && $staticUrl[1] == 'modules' && isset($staticUrl[0]) && $staticUrl[0] == 'custom' && isset($staticUrl[2]))
			{
				$file = array();
				for ($i=3; $i<count($staticUrl); $i++)
				{
					$file[] = $staticUrl[$i];
				}
				$file = implode('/', $file);
				$module = $staticUrl[2];
			}
			elseif(isset($staticUrl[0]) && $staticUrl[0] == 'templates' && isset($staticUrl[1]))
			{
				$file = array();
				for ($i=2; $i<count($staticUrl); $i++)
				{
					$file[] = $staticUrl[$i];
				}
				$file = implode('/', $file);
				$module = false;
			}
			else
			{
				$file = false;
				$module = false;
			}

			if ($file)
			{
				$search[] = $url;
				$replace[] = Helpers::get('static', $module)->get($file);
			}

		}
		return str_replace($search, $replace, $data);
	}
}

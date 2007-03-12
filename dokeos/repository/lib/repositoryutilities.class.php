<?php
/**
 * $Id$
 * @package repository
 */

require_once dirname(__FILE__).'/condition/andcondition.class.php';
require_once dirname(__FILE__).'/condition/orcondition.class.php';
require_once dirname(__FILE__).'/condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/repositorydatamanager.class.php';

/**
 * This class provides some common methods that are used throughout the
 * repository and sometimes outside it.
 *
 * @author Tim De Pauw
 */
class RepositoryUtilities
{
	const TOOLBAR_DISPLAY_ICON = 1;
	const TOOLBAR_DISPLAY_LABEL = 2;
	const TOOLBAR_DISPLAY_ICON_AND_LABEL = 3;

	private static $us_camel_map = array ();
	private static $camel_us_map = array ();

	/**
	 * Splits a Google-style search query. For example, the query
	 * /"dokeos repository" utilities/ would be parsed into
	 * array('dokeos repository', 'utilities').
	 * @param $pattern The query.
	 * @return array The query's parts.
	 */
	static function split_query($pattern)
	{
		$matches = array();
		preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
		$parts = array ();
		for ($i = 1; $i <= 2; $i ++)
		{
			foreach ($matches[$i] as $m)
			{
				if (!is_null($m) && strlen($m) > 0)
					$parts[] = $m;
			}
		}
		return (count($parts) ? $parts : null);
	}

	/**
	 * Transforms a search string (given by an end user in a search form) to a
	 * Condition, which can be used to retrieve learning objects from the
	 * repository.
	 * @param string $query The query as given by the end user.
	 * @param mixed $properties The learning object properties which should be
	 *                          taken into account for the condition. For
	 *                          example, array('title','type') will yield a
	 *                          Condition which can be used to search for
	 *                          learning objects on the properties 'title' or
	 *                          'type'. By default the properties are 'title'
	 *                          and 'description'. If the condition should
	 *                          apply to a single property, you can pass a
	 *                          string instead of an array.
	 * @return Condition The condition.
	 */
	static function query_to_condition($query, $properties = array (LearningObject :: PROPERTY_TITLE, LearningObject :: PROPERTY_DESCRIPTION))
	{
		if (!is_array($properties))
		{
			$properties = array ($properties);
		}
		$queries = self :: split_query($query);
		if (is_null($queries))
		{
			return null;
		}
		$cond = array ();
		foreach ($queries as $q)
		{
			$q = '*'.$q.'*';
			$pattern_conditions = array ();
			foreach ($properties as $index => $property)
			{
				$pattern_conditions[] = new PatternMatchCondition($property, $q);
			}
			if (count($pattern_conditions) > 1)
			{
				$cond[] = new OrCondition($pattern_conditions);
			}
			else
			{
				$cond[] = $pattern_conditions[0];
			}
		}
		$result = new AndCondition($cond);
		return $result;
	}

	/**
	 * Converts a date/time value retrieved from a FormValidator datepicker
	 * element to the corresponding UNIX itmestamp.
	 * @param string $string The date/time value.
	 * @return int The UNIX timestamp.
	 */
	static function time_from_datepicker($string)
	{
		list ($date, $time) = split(' ', $string);
		list ($year, $month, $day) = split('-', $date);
		list ($hours, $minutes, $seconds) = split(':', $time);
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}

	/**
	 * Orders the given learning objects by their title. Note that the
	 * ordering happens in-place; there is no return value.
	 * @param array $objects The learning objects to order.
	 */
	static function order_learning_objects_by_title(& $objects)
	{
		usort($objects, array (get_class(), 'by_title'));
	}
	
	static function order_learning_objects_by_id_desc(& $objects)
	{
		usort($objects, array (get_class(), 'by_id_desc'));
	}

	/**
	 * Prepares the given learning objects for use as a value for the
	 * element_finder QuickForm element.
	 * @param array $objects The learning objects.
	 * @return array The value.
	 */
	static function learning_objects_for_element_finder(& $objects)
	{
		$return = array ();
		foreach ($objects as $object)
		{
			$id = $object->get_id();
			$return[$id] = self :: learning_object_for_element_finder($object);
		}
		return $return;
	}

	/**
	 * Prepares the given learning object for use as a value for the
	 * element_finder QuickForm element's value array.
	 * @param LearningObject $object The learning object.
	 * @return array The value.
	 */
	static function learning_object_for_element_finder($object)
	{
		$type = $object->get_type();
		// TODO: i18n
		$date = date('r', $object->get_modification_date());
		$return = array ();
		$return['class'] = 'type type_'.$type;
		$return['title'] = $object->get_title();
		$return['description'] = get_lang(LearningObject :: type_to_class($type).'TypeName').' ('.$date.')';
		return $return;
	}

	/**
	 * Converts the given under_score string to CamelCase notation.
	 * @param string $string The string in under_score notation.
	 * @return string The string in CamelCase notation.
	 */
	static function underscores_to_camelcase($string)
	{
		if (!isset (self :: $us_camel_map[$string]))
		{
			self :: $us_camel_map[$string] = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $string));
		}
		return self :: $us_camel_map[$string];
	}

	/**
	 * Converts the given CamelCase string to under_score notation.
	 * @param string $string The string in CamelCase notation.
	 * @return string The string in under_score notation.
	 */
	static function camelcase_to_underscores($string)
	{
		if (!isset (self :: $camel_us_map[$string]))
		{
			self :: $camel_us_map[$string] = preg_replace(array ('/^([A-Z])/e', '/([A-Z])/e'), array ('strtolower(\1)', '"_".strtolower(\1)'), $string);
		}
		return self :: $camel_us_map[$string];
	}

	/**
	 * Builds a HTML representation of a toolbar, i.e. a list of clickable
	 * icons. The icon data is passed as an array with the following structure:
	 *
	 *   array(
	 *     array(
	 *       'img'     => '/path/to/icon.gif', # preferably absolute
	 *       'label'   => 'The Label', # no HTML
	 *       'href'    => 'http://the.url.to.point.to/', # null for no link
	 *       'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON,
	 *                      # ... or another constant
	 *       'confirm' => true  # requests confirmation upon clicking
	 *     ),
	 *     # ... more arrays, one per icon
	 *   )
	 *
	 * For the purpose of semantics, the toolbar will be an unordered
	 * list (ul) element. You can pass extra element class names, which allows
	 * you to poke at that element a little, but not at individual icons. If
	 * you wish to style only the label in your stylesheet, you can, as it is
	 * enclosed in a span element. To overcome technical limitations, the icon
	 * gets the class name "labeled" if a label is present. Future versions
	 * may allow positioning the label on either side.
	 * @param array $toolbar_data An array of toolbar elements. See above.
	 * @param mixed $class_names An additional class name. All toolbars have
	 *                           the class name "toolbar", but you may add
	 *                           as much as you like by passing a string or
	 *                           an array of strings here.
	 * @param string $css If you must, you can pass extra CSS for the list
	 *                    element's "style" attribute, but please don't.
	 * @return string The HTML.
	 */
	function build_toolbar($toolbar_data, $class_names = array (), $css = null)
	{
		if (!is_array($class_names))
		{
			$class_names = array ($class_names);
		}
		$class_names[] = 'toolbar';
		$html = array ();
				$html[] = '<div class="publication_attributes">';
		$html[] = '<ul class="'.implode(' ', $class_names).'"'. (isset ($css) ? ' style="'.$css.'"' : '').'>';
		foreach ($toolbar_data as $index => $elmt)
		{
			$label = (isset ($elmt['label']) ? htmlentities($elmt['label']) : null);
			if (!array_key_exists('display', $elmt))
			{
				$elmt['display'] = self :: TOOLBAR_DISPLAY_ICON;
			}
			$display_label = ($elmt['display'] & self :: TOOLBAR_DISPLAY_LABEL) == self :: TOOLBAR_DISPLAY_LABEL && !empty ($label);
			$button = '';
			if (($elmt['display'] & self :: TOOLBAR_DISPLAY_ICON) == self :: TOOLBAR_DISPLAY_ICON && isset ($elmt['img']))
			{
				$button .= '<img src="'.htmlentities($elmt['img']).'" alt="'.$label.'" title="'.$label.'"'. ($display_label ? ' class="labeled"' : '').'/>';
			}
			if ($display_label)
			{
				$button .= '<span>'.$label.'</span>';
			}
			if (isset ($elmt['href']))
			{
				$button = '<a href="'.htmlentities($elmt['href']).'" title="'.$label.'"'. ($elmt['confirm'] ? ' onclick="return confirm(\''.addslashes(htmlentities(get_lang('ConfirmYourChoice'))).'\');"' : '').'>'.$button.'</a>';
			}
			$classes = array();
			if ($index == 0)
			{
				$classes[] = 'first';
			}

			if ($index == count($toolbar_data) - 1)
			{
				$classes[] = 'last';
			}
			$html[] = '<li'.(count($classes) ? ' class="'.implode(' ', $classes).'"' : '').'>'.$button.'</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';
		// Don't separate by linefeeds. It creates additional whitespace.
		return implode($html);
	}
	/**
	 * Compares learning objects by title.
	 * @param LearningObject $learning_object_1
	 * @param LearningObject $learning_object_2
	 * @return int
	 */
	private static function by_title($learning_object_1, $learning_object_2)
	{
		return strcasecmp($learning_object_1->get_title(), $learning_object_2->get_title());
	}
	
	private static function by_id_desc($learning_object_1, $learning_object_2)
	{
		return ($learning_object_1->get_id() < $learning_object_2->get_id() ? 1 : -1); 
	}
	
	/**
	 * Checks if a file is an HTML document.
	 */
	// TODO: SCARA - MOVED / FROM: document_form_class / TO: RepositoryUtilities or some other relevant class.
	function is_html_document($path)
	{
		return (preg_match('/\.x?html?$/', $path) === 1);
	}
	
	function build_uses($publication_attr)
	{
		$rdm = RepositoryDataManager :: get_instance();
		
		$html 	= array ();
		$html[] = '<div class="publications">';
		$html[] = '<div class="publications_title">'.htmlentities(get_lang('ThisObjectIsPublished')).'</div>';
		$html[] = '<ul class="publications_list">';
		foreach ($publication_attr as $info)
		{
			$publisher = $this->get_user_info($info->get_publisher_user_id());
			$object = $rdm->retrieve_learning_object($info->get_publication_object_id());
			$html[] = '<li><img src="'.api_get_path(WEB_CODE_PATH).'/img/next.png" alt="option"/>';
			// TODO: i18n
			// TODO: SCARA - Find cleaner solution to display Learning Object title + url
			$html[] = '<a href="'.$info->get_url(). '">' . $info->get_application().': '.$info->get_location().'</a> > <a href="'. $object->get_view_url() .'">'. $object->get_title() .'</a> ('.$publisher['firstName'].' '.$publisher['lastName'].', '.date('r', $info->get_publication_date()).')';
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';
		
		return implode($html);
	}
	
	function build_block_hider($type, $id = null, $message = null)
	{
		$html = array();
		
		if ($type == 'script')
		{
			$html[]   = '<script language="JavaScript">';
			$html[]  .= 'function showElement(item)';
			$html[]  .= '{';
			$html[]  .= '	if (document.getElementById(item).style.display == \'block\')';
			$html[]  .= '  {';
			$html[]  .= '		document.getElementById(item).style.display = \'none\';';
			$html[]  .= '		document.getElementById(\'plus\').style.display = \'inline\';';
			$html[]  .= '		document.getElementById(\'minus\').style.display = \'none\';';
			$html[]  .= '  }';
			$html[]  .= '	else';
			$html[]  .= '  {';
			$html[]  .= '		document.getElementById(item).style.display = \'block\';';
			$html[]  .= '		document.getElementById(\'plus\').style.display = \'none\';';
			$html[]  .= '		document.getElementById(\'minus\').style.display = \'inline\';';
			$html[]  .= '		document.getElementById(item).value = \'Version comments here ...\';';
			$html[]  .= '	}';
			$html[]  .= '}';
			$html[]  .= '</script>';
		}
		elseif($type == 'begin')
		{
			$show_message = 'Show' . $message;
			$hide_message = 'Hide' . $message;
			
			$html[]    = '<div id="plus"><a href="javascript:showElement(\''. $id .'\')">'. get_lang('Show' . $message) .'</a></div>';
			$html[]    = '<div id="minus" style="display: none;"><a href="javascript:showElement(\''. $id .'\')">'. get_lang('Hide' . $message) .'</a></div>';
			$html[]   .= '<div id="'. $id .'" style="display: none;">';
		}
		elseif($type == 'end')
		{
			$html[]   = '</div>';
		}
		
		return implode($html);
	}

	function arr_diff(&$a1,&$a2)
	{
		$max=70;
		$c1=count($a1);
		$c2=count($a2);
		$v[1]=0;
		for ($D=0; $D<=$max; $D++)
		{
			for ($k=-$D; $k<=$D; $k=$k+2)
			{
				if (($k==-$D) || ($k!=$D && $v[$k-1]<$v[$k+1]))
					$x=$v[$k+1];
				else
					$x=$v[$k-1]+1;
					$y=$x-$k;
				while (($x<$c1)&&($y<$c2)&&($a1[$x]==$a2[$y]))
				{
					$x++;
					$y++;
				}
				$v[$k]=$x;
				if (($x>=$c1)&&($y>=$c2))
				{
					$vbck[$D]=$v;
					return self :: diff_rek($a1,$a2,$D,$c1-$c2,$vbck);
				};
			}
			$vbck[$D]=$v;
		};
		return -1;
	}

	function diff_to_html($oldString, $newString)
	{
		//$a1 = explode("\r\n", $oldString);
		$a1 = explode("\r\n", $oldString);
		$a2 = explode("\r\n", $newString);
		$result = self :: arr_diff($a1, $a2);
		print_r();
		foreach ($result[0] as $num => $foo)
		{
			$source = $result[1][$num];
			$element = $result[0][$num];

			switch ($source)
			{
				case "1":
					$pre = "<font color=red>";
					$post = "</font>";
					break;
				case "2":
					$pre = "<font color=green>";
					$post = "</font>";
					break;
				case "b":
					$pre = "";
					$post = "";
					break;
			}

			//READABLE OUTPUT:
			$return .= $pre . $element . $post . " ";
		}
		return $return;
	} 	

	function diff_rek(&$a1,&$a2,$D,$k,&$vbck)
	{
		$x=$vbck[$D][$k]; $y=$x-$k;
		if ($D==0)
		{
			if ($x==0)
				return array(array(),array());
			else
				return array(array_slice($a1,0,$x),array_fill(0,$x,"b"));
		}
		$x2=$vbck[$D-1][$k+1];
		$y2=$vbck[$D-1][$k-1]-($k-1);
		$xdif=$x-$x2; $ydif=$y-$y2;
		$l=min($x-$x2,$y-$y2);
		$x=$x-$l;
		$y=$y-$l;
		if ($x==$x2)
		{
			$res= self :: diff_rek($a1,$a2,$D-1,$k+1,$vbck);
			array_push($res[0],$a2[$y-1]);
			array_push($res[1],"2");
			if ($l>0)
			{
				$res[0]=array_merge($res[0],array_slice($a2,$y,$l));
				$res[1]=array_merge($res[1],array_fill(0,$l,"b"));
			}
		}
		else
		{
			$res= self :: diff_rek($a1,$a2,$D-1,$k-1,$vbck);
			array_push($res[0],$a1[$x-1]);
			array_push($res[1],"1");
			if ($l>0)
			{
				$res[0]=array_merge($res[0],array_slice($a1,$x,$l));
				$res[1]=array_merge($res[1],array_fill(0,$l,"b"));
			}
		}
		return $res;
	}

}
?>
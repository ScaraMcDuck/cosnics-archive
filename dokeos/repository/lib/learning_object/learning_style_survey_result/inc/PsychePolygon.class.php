<?php

/**
 * Creates one of those fancy polygons that every survey needs. Some
 * additional documentation is in order. Good luck.
 * 
 * @author Tim De Pauw
 */
class PsychePolygon {
	/**
	 * An image in the PNG format.
	 */
	const IMAGE_TYPE_PNG = 1;

	/**
	 * An image in the GIF format. 
	 */
	const IMAGE_TYPE_GIF = 2;

	/**
	 * An image in the JPEG format.
	 */
	const IMAGE_TYPE_JPEG = 3;

	private static $MIME_TYPES = array(
		self::IMAGE_TYPE_PNG => 'image/png',
		self::IMAGE_TYPE_GIF => 'image/gif',
		self::IMAGE_TYPE_JPEG => 'image/jpeg'
	);

	private static $DEFAULT_OPTIONS = array(
		'image_width' => 800,
		'image_height' => 600,
		'circle_radius' => 250,
		'max_text_label_width' => 200,
		'font_filename' => 'DejaVuSans.ttf',
		'font_path' => null,
		'font_size' => 10,
		'text_leading' => 5,
		'text_padding' => 10,
		'colors' => array(
			'background' => array(
				'r' => 255, 'g' => 255, 'b' => 255),
			'circle_stroke' => array(
				'r' => 0, 'g' => 111, 'b' => 255, 'a' => 63),
			'circle_fill' => array(
				'r' => 0, 'g' => 111, 'b' => 255, 'a' => 125),
			'text' => array(
				'r' => 0, 'g' => 0, 'b' => 0),
			'text_label_stroke' => array(
				'r' => 127, 'g' => 191, 'b' => 255, 'a' => 31),
			'text_label_fill' => array(
				'r' => 127, 'g' => 191, 'b' => 255, 'a' => 63),
			'polygon_stroke' => array(
				'r' => 0, 'g' => 223, 'b' => 0),
			'polygon_fill' => array(
				'r' => 0, 'g' => 223, 'b' => 0, 'a' => 95)
		),
		'antialias' => true
	);

	private $titles;

	private $data;

	private $options;

	private $polygon;
	
	private $labels;

	private $strings;

	private $center;
	
	private $image_data;

	/**
	 * Constructor.
	 * @param array $titles An array of labels for the polygon's vertices.
	 * @param array $data An array of values for the polygon. Indices need to
	 *                    correspond to those of $titles.
	 * @param int $min_value The minimum value to consider.
	 * @param int $max_value The maximum value to consider.
	 * @param array $options An associative array of options. Available options
	 *                       can be found in $DEFAULT_OPTIONS (see source code).
	 */
	function __construct ($titles, $data, $min_value = 0, $max_value = 100,
	$options = array()) {
		if (!extension_loaded('gd')) {
			die(get_class() . ' requires that GD be enabled');
		}
		$this->titles = $titles;
		$this->data = $data;
		$this->min_value = $min_value;
		$this->max_value = $max_value;
		$this->options = array_merge_recursive(self::$DEFAULT_OPTIONS, $options);
		$font_path = ($this->options['font_path'] === null
			? dirname(__FILE__)
			: $this->options['font_path']);
		$this->font_file = $font_path . DIRECTORY_SEPARATOR
			. $this->options['font_filename'];
		$this->revalidate();
	}

	/**
	 * Creates the polygon image.
	 * @param int $type The type of image to generate.
	 * @return array An associative array containing the image as well as its
	 *               dimensions and MIME type.
	 */
	function create_image($type = self::IMAGE_TYPE_PNG) {
		$this->create_image_object();
		$this->allocate_colors();
		$this->fill_background();
		$this->draw_circle();
		$this->draw_polygon();
		$this->draw_labels();
		$this->generate_image($type);
		return array(
			'data' => $this->image_data,
			'mime_type' => self::$MIME_TYPES[$type],
			'width' => $this->options['image_width'],
			'height' => $this->options['image_height']
		);
	}

	private function revalidate() {
		$this->polygon = array();
		$this->labels = array();
		$this->strings = array();
		$this->center = array(
			'x' => $this->options['image_width'] / 2,
			'y' => $this->options['image_height'] / 2
		);
		$angle = pi() / 2;
		$angle_delta = 2 * pi() / count($this->titles);
		$max_text_width = $this->options['max_text_label_width'];
		foreach ($this->titles as $idx => $title) {
			$value = $this->data[$idx];
			if (preg_match_all('/(\S+)/', $title, $matches)) {
				$words = $matches[0];
				$str = $words[0];
				$size = $this->measure_string($str);
				$lines = array();
				$total_width = 0;
				for ($i = 1; $i < count($words); $i++) {
					$new_str = $str . ' ' . $words[$i];
					$new_size = $this->measure_string($new_str);
					if ($new_size['width'] > $max_text_width) {
						$lines[] = array('text' => $str, 'size' => $size);
						if ($size['width'] > $total_width) {
							$total_width = $size['width'];
						}
						$str = $words[$i];
						$size = $this->measure_string($str);
					}
					else {
						$str = $new_str;
						$size = $new_size;
					}
				}
				$lines[] = array('text' => $str, 'size' => $size);
				if ($size['width'] > $total_width) {
					$total_width = $size['width'];
				}
				$total_height = count($lines) * $this->options['font_size']
					+ (count($lines) - 1) * $this->options['text_leading'];
				$pos = array(
					'x' => $this->center['x']
						- cos($angle) * $this->options['circle_radius']
						- $total_width / 2,
					'y' => $this->center['y']
						- sin($angle) * $this->options['circle_radius']
						- $total_height / 2
				);
				$this->labels[] = array(
					'x' => $pos['x'] - $this->options['text_padding'],
					'y' => $pos['y'] - $this->options['text_padding'],
					'width' => $total_width
						+ 2 * $this->options['text_padding'],
					'height' => $total_height
						+ 2 * $this->options['text_padding']
				);
				$y = $pos['y'] + $this->options['font_size'];
				foreach ($lines as $line) {
					$x = $pos['x']
						+ ($total_width - $line['size']['width']) / 2;
					$this->strings[] = array(
						'x' => $x,
						'y' => $y,
						'text' => $line['text']
					);
					$y += $this->options['font_size']
						+ $this->options['text_leading'];
				}
			}
			$ratio = ($value - $this->min_value)
				/ ($this->max_value - $this->min_value);
			$this->polygon[] = $this->center['x']
				- cos($angle) * $this->options['circle_radius'] * $ratio;
			$this->polygon[] = $this->center['y']
				- sin($angle) * $this->options['circle_radius'] * $ratio;
			$angle += $angle_delta;
		}
	}
	
	private function create_image_object() {
		$this->image = imagecreatetruecolor(
			$this->options['image_width'], $this->options['image_height']);
		if ($this->options['antialias'] && function_exists('imageantialias')) {
			imageantialias($this->image, true);
		}
	}

	private function allocate_colors() {
		$this->color_map = array();
		foreach ($this->options['colors'] as $type => $color) {
			$this->color_map[$type] = (array_key_exists('a', $color)
				? imagecolorallocatealpha($this->image,
					$color['r'], $color['g'], $color['b'], $color['a'])
				: imagecolorallocate($this->image,
					$color['r'], $color['g'], $color['b']));
		}
	}

	private function fill_background() {
		imagefilledrectangle($this->image,
			0, 0, $this->options['image_width'], $this->options['image_height'],
			$this->color_map['background']);
	}

	private function draw_circle() {
		$diameter = $this->options['circle_radius'] * 2;
		imagefilledellipse($this->image,
			$this->center['x'], $this->center['y'],
			$diameter, $diameter,
			$this->color_map['circle_fill']);
		imageellipse($this->image,
			$this->center['x'], $this->center['y'],
			$diameter, $diameter,
			$this->color_map['circle_stroke']);
	}

	private function draw_polygon() {
		if (count($this->polygon)) {
			imagefilledpolygon($this->image,
				$this->polygon, count($this->titles),
				$this->color_map['polygon_fill']);
			imagepolygon($this->image,
				$this->polygon, count($this->titles),
				$this->color_map['polygon_stroke']);
		}
	}

	private function draw_labels() {
		$this->draw_label_backgrounds();
		$this->draw_label_texts();
	}

	private function draw_label_backgrounds() {
		foreach ($this->labels as $label) {
			imagefilledrectangle(
				$this->image,
				$label['x'],
				$label['y'],
				$label['x'] + $label['width'],
				$label['y'] + $label['height'],
				$this->color_map['text_label_fill']
			);
			imagerectangle(
				$this->image,
				$label['x'],
				$label['y'],
				$label['x'] + $label['width'],
				$label['y'] + $label['height'],
				$this->color_map['text_label_stroke']
			);
		}
	}

	private function draw_label_texts() {
		foreach ($this->strings as $string) {
			imagettftext(
				$this->image,
				$this->options['font_size'],
				0,
				$string['x'],
				$string['y'],
				$this->color_map['text'], 
				$this->font_file,
				$string['text']
			);
		}
	}

	private function generate_image($type) {
		$func = null;
		switch ($type) {
			case self::IMAGE_TYPE_PNG: $func = 'imagepng'; break;
			case self::IMAGE_TYPE_GIF: $func = 'imagegif'; break;
			case self::IMAGE_TYPE_JPEG: $func = 'imagejpeg'; break;
			default: die('Invalid image type');
		}
		ob_start();
		call_user_func($func, $this->image);
		$this->image_data = ob_get_contents();
		ob_end_clean();
	}

	private function measure_string ($str) {
		$bbox = imagettfbbox(
			$this->options['font_size'], 0, $this->font_file, $str);
		return array(
			'width' => $bbox[4] - $bbox[6],
			'height' => $bbox[1] - $bbox[7]
		);
	}
}

?>
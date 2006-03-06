<?php

define(HEADER, "<?php\nrequire_once dirname(__FILE__) . '/../../learningobject.class.php';\n\n");
define(FOOTER, "}\n?" . ">");

$path = dirname(__FILE__) . '/../lib/learning_object';
if ($handle = opendir($path)) {
	while (false !== ($file = readdir($handle))) {
		$p = $path . '/' . $file;
		if (strpos($file, '.') === false && is_dir($p)) {
			$classFile = $p . '/' . $file . '.class.php';
			$propertyFile = $p . '/' . $file . '.properties';
			if (is_file($propertyFile) && !is_file($classFile)) {
				$properties = file($propertyFile);
				if ($fh = fopen($p . '/' . $file . '.class.php', 'w')) {
					fwrite($fh, HEADER);
					$cls = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $file));
					fwrite($fh, 'class ' . $cls . ' extends LearningObject '."\n".'{' . "\n");
					foreach ($properties as $prop) {
						$prop = rtrim($prop);
						fwrite($fh, "\tfunction get_$prop () \n\t{\n"
							. "\t\treturn \$this->get_additional_property('$prop');\n"
							. "\t}\n"
							. "\tfunction set_$prop (\$$prop) \n\t{\n"
							. "\t\treturn \$this->set_additional_property('$prop', \$$prop);\n"
							. "\t}\n");
					}
					fwrite($fh, FOOTER);
					fclose($fh);
					echo 'Created "' . $file . '" class' . "\n";
				}
			}
		}
	}
	closedir($handle);
}
?>
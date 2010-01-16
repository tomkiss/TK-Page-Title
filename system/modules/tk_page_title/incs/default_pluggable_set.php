<?

// Additional class for the Font Image Generator 2 tools 
class DefaultFontImagePluggableSet
	extends GMIPluggableSet
{
	
	function DefaultFontImagePluggableSet() {
		parent::GMIPluggableSet();
	}
	
	function getExpression() {
		
		$vars = $this->getVariables();
		
		if (isset($vars['exec'])) {
			return $vars['exec'];
		}
		
		// text drawing
		if ($vars['text'] !== null && $vars['font'] !== null && $vars['size'] !== null) {
			$font = "font {font:{font},{size},{leading}};";
		
			if ($vars['width'] !== null && $vars['height'] !== null) {
				$string = "string {text}, 0,0,{width},{height},{align},{valign};";
				$autoResize = "autoresize width;";
			}
			else if ($vars['width'] !== null) {
				$string = "string {text},0,0,{width},{align};";
				$autoResize = "autoresize both;";
			}
			else {
				$string = "string {text},0,0,{align};";
				$autoResize = "autoresize both;";
			}
		}
		else {
			$font = "";
			$string = "";
		}
		
		// background image drawing
		if ($vars['bgimage'] !== null) {
			$pattern = "pattern {image:{bgimage}};";
		}
		else {
			$pattern = "";
		}
		
		// foreground color
		if (preg_match('/^\s*\{\s*\w+\:.*\}\s*$/', $vars['color'])) {
			$foreground = "color {color};";
		}
		else {
			$foreground = "color {color:{color},{alpha}};";
		}
		
		// background color
		if (preg_match('/^\s*\{\s*\w+\:.*\}\s*$/', $vars['bgcolor'])) {
			$background = "color {bgcolor};";
		}
		else {
			$background = "color {color:{bgcolor}};";
		}
		
		// image type selection
		switch ($vars['type']) {
			case "jpeg":
				$type = "type {type},{quality};";
				break;
		
		
			case "png":
				if ($vars['palette'] === null) {
					$type = "type {type};";
					
					if ($vars['blank']) {
						$type .= "$background blank;";
					}
				}
				else {
					if ($vars['bgtrans'] == 'false') {
						$type = "type {type},{palette};";
					}
					else {
						$type = "type {type},{palette},{color:{bgcolor}};";
					}
				}
				break;
			
			case "gif":
			default:
				if ($vars['palette'] === null) {
					$vars['palette'] = 255;
				}
				if ($vars['bgtrans'] == 'false') {
					$type = "type {type},{palette};";
				}
				else {
					$type = "type {type},{palette},{color:{bgcolor}};";
				}
		}
		
		// file output
		if ($vars['file'] !== null) {
			$file = "file {file};";
		}
		else {
			$file = "";
		}
				
		return "size {width},{height}; $autoResize $type $file padding {padding}; $background fill; $pattern $foreground antialias {antialias}; $font $string";
	}
	
	function getVariables() {
		return array_merge($this->defaultVariables, $_GET);
	}
	
}

?>
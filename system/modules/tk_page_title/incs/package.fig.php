<?php

/**
* 
* PHP Font Image Generator
*
* Copyright (C) 2006 Matsuda Shota
* http://sgssweb.com/
* admin@sgssweb.com
*
* ------------------------------------------------------------------------
*
* 2006-4-20		First release.
*
*/
/*
	
	Object Constructor:
		{image: <string>}
		{font: <string>, <number>}
		{font: <string>, <number>, <number>}
		{color: <number>}
		{color: <number>, <number> }
		{color: <number>, <number>, <number>}
		{color: <number>, <number>, <number>, <number>}
		{rectangle: <number>, <number>, <number>, <number>}
		{insets: }
		{insets: <number>}
		{insets: <number>, <number>}
		{insets: <number>, <number>, <number>, <number>}
		{point: }
		{point: <number>, <number>}
		{dimension: }
		{dimension: <number>, <number>}
	
	Canvas Setting:
		autoresize <constant>[none | width | height | both]
		padding <insets>
		padding <number>
		padding <number>, <number>
		padding <number>, <number>, <number>, <number>
		size <number>, <number>
		type <constant>[gif | jpeg | png]
		type <constant>[gif | jpeg | png], <number>
		type <constant>[gif | jpeg | png], <color>
		type <constant>[gif | jpeg | png], <number>, <color>
		file <string>
	
	Graphics:
		antialias <number>
		color <color>
		color <number>
		color <number>, <number>
		color <number>, <number>, <number>
		color <number>, <number>, <number>, <number>
		font <font>
		font <string>, <number>
		font <string>, <number>, <number>
		translate <string>, <number>
		fill
		blank
		gradient <color> <constant>[top | left | bottom | right]
		line <number>, <number>, <number>, <number>
		pattern <image>
		pattern <string>
		pattern <image>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		pattern <string>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		rect <rectangle>
		rect <number>, <number>, <number>, <number>
		fillrect <rectangle>
		fillrect <number>, <number>, <number>, <number>
		patternrect <image>, <rectangle>
		patternrect <string>, <rectangle>
		patternrect <image>, <rectangle>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		patternrect <string>, <rectangle>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		patternrect <image>, <number>, <number>, <number>, <number>
		patternrect <string>, <number>, <number>, <number>, <number>
		patternrect <image>, <number>, <number>, <number>, <number>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		patternrect <string>, <number>, <number>, <number>, <number>, <constant>[repeat | repeat-x | repeat-y | no-repeat]
		image <image>, <number>, <number>
		image <string>, <number>, <number>
		image <image>, <number>, <number>, <number>, <number>
		image <string>, <number>, <number>, <number>, <number>
		image <image>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>
		image <string>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>, <number>
		string <string>
		string <string>, <number>, <number>
		string <string>, <number>, <number>, <number>
		string <string>, <number>, <number>, <number>, <number>
		string <string>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
		string <string>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust],
			   <constant>[top | middle | bottom]
		string <string>, <number>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
		string <string>, <number>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust],
			   <constant>[top | middle | bottom]
		string <string>, <number>, <number>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
		string <string>, <number>, <number>, <number>, <number>,
			   <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust],
			   <constant>[top | middle | bottom]
	
*/




require_once 'package.graphics.php';
require_once 'package.gmi.php';




class FontImageGenerator
	extends GMIExecution
{
	var $canvas; // Canvas
	var $g; // Graphics
	
	function FontImageGenerator() {
		parent::GMIExecution();
		
		$this->canvas = new Canvas();
		$this->g =& $this->canvas->getGraphics();
	}
	
	
	// void execute()
	function execute() {
		parent::execute();
		
		if ($this->isDebugMode) {
			$this->debug();
		}
		else {
			$this->canvas->complete();
			$this->canvas->output();
		}
	}
	
	
	// void command(string name, array args)
	function command($name, &$args) {
	
		switch (strToLower($name)) {
			case "autoresize":
				// autoresize <constant>[none | width | height | both]
				if ($this->validateArguments(array(array('none', 'width', 'height', 'both')), $args)) {
					$this->canvas->setAutoResizeMode($args[0]->getValue());
					break;
				}
				break;
				
			case "padding":
				// padding <insets>
				if ($this->validateArguments(array('insets'), $args)) {
					$this->canvas->setPadding($args[0]->getValue());
					break;
				}
				
				// padding <number>
				// padding <number> <number>
				// padding <number> <number> <number> <number>
				else {
					$insets = $this->construct("insets", $args);
					
					if ($insets != null) {
						$this->canvas->setPadding($insets);
						break;
					}
				}
				break;
			
			case "size":
				// size <dimension>
				if ($this->validateArguments(array('dimension'), $args)) {
					$this->canvas->setSize($args[0]->getValue());
					break;
				}
				// size <number> <number>
				if ($this->validateArguments(array('number', 'number'), $args)) {
					$this->canvas->setSize($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				break;
			
			case "antialias":
				// antialias <number>
				if ($this->validateArguments(array('number'), $args)) {
					$this->g->setTextAntialias($args[0]->getValue());
					break;
				}
				break;
				
			case "color":
				// color <color>
				if ($this->validateArguments(array('color'), $args)) {
					$this->g->setColor($args[0]->getValue());
					break;
				}
				// color <number>
				// color <number> <number>
				// color <number> <number> <number>
				// color <number> <number> <number> <number>
				else {
					$color = $this->construct("color", $args);
					
					if ($color != null) {
						$this->g->setColor($color);
						break;
					}
				}
				break;
				
			case "font":
				// font <font>
				if ($this->validateArguments(array('font'), $args)) {
					$this->g->setFont($args[0]->getValue());
					break;
				}
				// font <string> <number>
				// font <string> <number> <number>
				$font = $this->construct("font", $args);
				
				if ($font != null) {
					$this->g->setFont($font);
					break;
				}
				break;
				
			case "translate":
				// translate <number> <number>
				if ($this->validateArguments(array('number', 'number'), $args)) {
					$this->g->translate($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				break;
				
			case "fill":
				// fill
				$this->g->fill();
				break;
				
			case "blank":
				// blank
				$this->g->blank();
				break;
				
			case "gradient":
				// gradient <color>
				if ($this->validateArguments(array('color'), $args)) {
					$this->g->pattern($args[0]->getValue());
					break;
				}
				// gradient <color> <constant>[top | left | bottom | right]
				if ($this->validateArguments(array('color', array('top', 'left', 'bottom', 'right')), $args)) {
					$this->g->gradient($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				break;
				
			case "line":
				// line <number> <number> <number> <number>
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					$this->g->drawLine($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
					break;
				}
				break;
				
			case "pattern":
				// pattern <image>
				if ($this->validateArguments(array('image'), $args)) {
					$this->g->pattern($args[0]->getValue());
					break;
				}
				// pattern <image> <constant>[repeat | repeat-x | repeat-y | no-repeat]
				if ($this->validateArguments(array('image', array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
					$this->g->pattern($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				
				$image = $this->construct("image", $this->extractArguments(0, 0, $args));
				
				if ($image != null) {
					// pattern <string>
					if ($this->validateArguments(array(null), $args)) {
						$this->g->pattern($image);
						break;
					}
					// pattern <string> <constant>[repeat | repeat-x | repeat-y | no-repeat]
					if ($this->validateArguments(array(null, array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
						$this->g->pattern($image, $args[1]->getValue());
						break;
					}
				}
				break;
				
			case "rect":
				// rect <rectangle>
				if ($this->validateArguments(array('rectangle'), $args)) {
					$this->g->drawRect($args[0]->getValue());
					break;
				}
				// rect <number> <number> <number> <number>
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					$this->g->drawRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
					break;
				}
				break;
				
			case "fillrect":
				// fillrect <rectangle>
				if ($this->validateArguments(array('rectangle'), $args)) {
					$this->g->fillRect($args[0]->getValue());
					break;
				}
				// fillrect <number> <number> <number> <number>
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					$this->g->fillRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
					break;
				}
				break;
				
			case "gradientrect":
				// gradientrect <color> <rectangle>
				if ($this->validateArguments(array('color', 'rectangle'), $args)) {
					$this->g->gradientRect($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				// gradientrect <color> <number> <number> <number> <number>
				if ($this->validateArguments(array('color', 'number', 'number', 'number', 'number'), $args)) {
					$this->g->gradientRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
					break;
				}
				// gradientrect <color> <rectangle> <constant>[top | left | bottom | right]
				if ($this->validateArguments(array('color', 'rectangle', array('top', 'left', 'bottom', 'right')), $args)) {
					$this->g->gradientRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
					break;
				}
				// gradientrect <color> <number> <number> <number> <number> <constant>[top | left | bottom | right]
				if ($this->validateArguments(array('color', 'number', 'number', 'number', 'number', array('top', 'left', 'bottom', 'right')), $args)) {
					$this->g->gradientRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue());
					break;
				}
				break;
				
			case "patternrect":
				// patternrect <image> <rectangle>
				if ($this->validateArguments(array('image', 'rectangle'), $args)) {
					$this->g->patternRect($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				// patternrect <image> <number> <number> <number> <number>
				if ($this->validateArguments(array('image', 'number', 'number', 'number', 'number'), $args)) {
					$this->g->patternRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
					break;
				}
				// patternrect <image> <rectangle> <constant>[repeat | repeat-x | repeat-y | no-repeat]
				if ($this->validateArguments(array('image', 'rectangle', array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
					$this->g->patternRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
					break;
				}
				// patternrect <image> <number> <number> <number> <number> <constant>[repeat | repeat-x | repeat-y | no-repeat]
				if ($this->validateArguments(array('image', 'number', 'number', 'number', 'number', array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
					$this->g->patternRect($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue());
					break;
				}
				
				$image = $this->construct("image", $this->extractArguments(0, 0, $args));
				
				if ($image != null) {
					// patternrect <string> <rectangle>
					if ($this->validateArguments(array(null, 'rectangle'), $args)) {
						$this->g->patternRect($image, $args[1]->getValue());
						break;
					}
					// patternrect <string> <number> <number> <number> <number>
					if ($this->validateArguments(array(null, 'number', 'number', 'number', 'number'), $args)) {
						$this->g->patternRect($image, $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
						break;
					}
					// patternrect <string> <rectangle> <constant>[repeat | repeat-x | repeat-y | no-repeat]
					if ($this->validateArguments(array(null, 'rectangle', array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
						$this->g->patternRect($image, $args[1]->getValue(), $args[2]->getValue());
						break;
					}
					// patternrect <string> <number> <number> <number> <number> <constant>[repeat | repeat-x | repeat-y | no-repeat]
					if ($this->validateArguments(array(null, 'number', 'number', 'number', 'number', array('repeat','repeat-x','repeat-y','no-repeat')), $args)) {
						$this->g->patternRect($image, $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue());
						break;
					}
				}
				break;
				
			case "image":
				// image <image> <number> <number>
				if ($this->validateArguments(array('image', 'number', 'number'), $args)) {
					$this->g->drawImage($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
					break;
				}
				// image <image> <number> <number> <number> <number>
				if ($this->validateArguments(array('image', 'number', 'number', 'number', 'number'), $args)) {
					$this->g->drawImage($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
					break;
				}
				// image <image> <number> <number> <number> <number> <number> <number> <number> <number>
				if ($this->validateArguments(array('image', 'number', 'number', 'number', 'number', 'number', 'number', 'number', 'number'), $args)) {
					$this->g->drawImage($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue(), $args[6]->getValue(), $args[7]->getValue(), $args[8]->getValue());
					break;
				}
				
				$image = $this->construct("image", $this->extractArguments(0, 0, $args));
				
				if ($image != null) {
					// image <string> <number> <number>
					if ($this->validateArguments(array(null, 'number', 'number'), $args)) {
						$this->g->drawImage($image, $args[1]->getValue(), $args[2]->getValue());
						break;
					}// image <image> <number> <number> <number> <number>
					if ($this->validateArguments(array(null, 'number', 'number', 'number', 'number'), $args)) {
						$this->g->drawImage($image, $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
						break;
					}
					// image <image> <number> <number> <number> <number> <number> <number> <number> <number>
					if ($this->validateArguments(array(null, 'number', 'number', 'number', 'number', 'number', 'number', 'number', 'number'), $args)) {
						$this->g->drawImage($image, $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue(), $args[6]->getValue(), $args[7]->getValue(), $args[8]->getValue());
						break;
					}
				}
				break;
				
			case "string":
				// string <string>
				if ($this->validateArguments(array('string'), $args)) {
					$this->g->drawString($args[0]->getValue());
					break;
				}
				// string <string> <number> <number>
				if ($this->validateArguments(array('string', 'number', 'number'), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
					break;
				}
				// string <string> <number> <number>
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				if ($this->validateArguments(array('string', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
					break;
				}
				// string <string> <number> <number>
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				//		  <constant>[top | middle | bottom]
				if ($this->validateArguments(array('string', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust'), array('top','middle','bottom')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), null, null, $args[3]->getValue(), $args[4]->getValue());
					break;
				}
				// string <string> <number> <number> <number>
				if ($this->validateArguments(array('string', 'number', 'number', 'number'), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), null);
					break;
				}
				// string <string> <number> <number> <number> 
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				if ($this->validateArguments(array('string', 'number', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), null, $args[4]->getValue(), 'top');
					break;
				}
				// string <string> <number> <number> <number> 
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				//		  <constant>[top | middle | bottom]
				if ($this->validateArguments(array('string', 'number', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust'), array('top','middle','bottom')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), null, $args[4]->getValue(), $args[5]->getValue());
					break;
				}
				// string <string> <number> <number> <number> <number>
				if ($this->validateArguments(array('string', 'number', 'number', 'number', 'number'), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue());
					break;
				}
				// string <string> <number> <number> <number> <number> 
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				if ($this->validateArguments(array('string', 'number', 'number', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue(), 'top');
					break;
				}
				// string <string> <number> <number> <number> <number> 
				//		  <constant>[left | center | right | left-adjust | center-adjust | right-adjust | adjust]
				//		  <constant>[top | middle | bottom]
				if ($this->validateArguments(array('string', 'number', 'number', 'number', 'number', array('left','center','right','left-adjust','center-adjust','right-adjust','adjust'), array('top','middle','bottom')), $args)) {
					$this->g->drawString($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue(), $args[4]->getValue(), $args[5]->getValue(), $args[6]->getValue());
					break;
				}
				break;
				
			case "type":
				// type <constant>[jpeg | gif | png]
				if ($this->validateArguments(array(array('jpeg','gif','png')), $args)) {
					$this->canvas->setOutputType($args[0]->getValue());
					break;
				}
				// type <constant>[jpeg | gif | png] <number>
				if ($this->validateArguments(array(array('jpeg','gif','png'), 'number'), $args)) {
					$this->canvas->setOutputType($args[0]->getValue(), $args[1]->getValue());
					break;
				}
				// type <constant>[jpeg | gif | png] <color>
				if ($this->validateArguments(array(array('jpeg','gif','png'), 'color'), $args)) {
					$this->canvas->setOutputType($args[0]->getValue(), null, $args[1]->getValue());
					break;
				}
				// type <constant>[jpeg | gif | png] <number> <color>
				if ($this->validateArguments(array(array('jpeg','gif','png'), 'number', 'color'), $args)) {
					$this->canvas->setOutputType($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
					break;
				}
				break;
				
			case "file":
				// file <string>
				if ($this->validateArguments(array('string'), $args)) {
					$this->canvas->setOutputFile($args[0]->getValue());
					break;
				}
				break;
		}
		
		parent::command($name, $args);
	}
	
	
	
	// mixed construct(string class, array args)
	function construct($class, &$args) {
		
		switch (strToLower($class)) {
			case "image":
				// {image: <string>}
				if ($this->validateArguments(array('string'), $args)) {
					return $this->canvas->getImage($args[0]->getValue());
				}
				break;
			
			case "font":
				// {font: <string> <number>}
				if ($this->validateArguments(array('string', 'number'), $args)) {
					return new Font($args[0]->getValue(), $args[1]->getValue());
				}
				// {font: <string> <number> <number>}
				if ($this->validateArguments(array('string', 'number', 'number'), $args)) {
					$value =& new Font($args[0]->getValue(), $args[1]->getValue());
					$value->metrics->setLineHeight($args[2]->getValue());
					return $value;
				}
				break;
			
			case "color":
				// {color: <number>}
				if ($this->validateArguments(array('number'), $args)) {
					return new Color($args[0]->getValue());
				}
				// {color: <number> <number>}
				if ($this->validateArguments(array('number', 'number'), $args)) {
					return new Color($args[0]->getValue(), $args[1]->getValue());
				}
				// {color: <number> <number> <number>}
				if ($this->validateArguments(array('number', 'number', 'number'), $args)) {
					return new Color($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue());
				}
				// {color: <number> <number> <number> <number>}
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					return new Color($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
				}
				break;
			
			case "rectangle":
				// {rectangle: <number> <number> <number> <number>}
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					return new Rectangle($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
				}
				break;
			
			case "insets":
				// {insets: }
				if ($this->validateArguments(array(), $args)) {
					return new Insets();
				}
				// {insets: <number>}
				if ($this->validateArguments(array('number'), $args)) {
					return new Insets($args[0]->getValue(), $args[0]->getValue(), $args[0]->getValue(), $args[0]->getValue());
				}
				// {insets: <number> <number>}
				if ($this->validateArguments(array('number', 'number'), $args)) {
					return new Insets($args[0]->getValue(), $args[1]->getValue(), $args[0]->getValue(), $args[1]->getValue());
				}
				// {insets: <number> <number> <number> <number>}
				if ($this->validateArguments(array('number', 'number', 'number', 'number'), $args)) {
					return new Insets($args[0]->getValue(), $args[1]->getValue(), $args[2]->getValue(), $args[3]->getValue());
				}
				break;
				
			case "dimension":
				// {dimension: }
				if ($this->validateArguments(array(), $args)) {
					return new Dimension();
				}
				// {dimension: <number> <number>}
				if ($this->validateArguments(array('number', 'number'), $args)) {
					return new Dimension($args[0]->getValue(), $args[1]->getValue());
				}
				break;
				
			case "point":
				// {point: }
				if ($this->validateArguments(array(), $args)) {
					return new Point();
				}
				// {point: <number> <number>}
				if ($this->validateArguments(array('number', 'number'), $args)) {
					return new Point($args[0]->getValue(), $args[1]->getValue());
				}
				break;
		}
		
		return parent::construct($class, $args);
	}
	
	
	// GMIValue createValue(string exp)
	function createValue($exp) {
		$value = null;
		
		if (preg_match('/^\{\s*WIDTH\s*\}$/', $exp)) {
			$value = new GMINumber($this, $this->canvas->getWidth());
		}
		else if (preg_match('/^\{\s*HEIGHT\s*\}$/', $exp)) {
			$value = new GMINumber($this, $this->canvas->getHeight());
		}
		
		return ($value !== null)? $value
								: parent::createValue($exp);
	}
	
	
	// GMIValue getProperty(GMIVariable variable, string property)
	function getProperty(&$variable, $property) {
		$type = $variable->getType();
		$value = $variable->getValue();
		
		switch ($type) {
			case "image":
				switch($property) {
					case "width":
						return $this->createValue($value->getWidth());
					case "height":
						return $this->createValue($value->getHeight());
				}
				break;
				
			case "font":
				switch($property) {
					case "name":
						return $this->createValue($value->getFontName());
					case "size":
						return $this->createValue($value->getSize());
					case "leading":
						unset($metrics);
						$metrics =& $value->getMetrics();
						return $this->createValue($metrics->getLeading());
				}
				break;
				
			case "color":
				switch($property) {
					case "r":
						return $this->createValue($value->getRed());
					case "g":
						return $this->createValue($value->getGreen());
					case "b":
						return $this->createValue($value->getGreen());
					case "a":
						return $this->createValue($value->getAlpha());
				}
				break;
			
			case "rectangle":
				switch($property) {
					case "x":
						return $this->createValue($value->getX());
					case "y":
						return $this->createValue($value->getY());
					case "width":
						return $this->createValue($value->getWidth());
					case "height":
						return $this->createValue($value->getHeight());
				}
				break;
			
			case "insets":
				switch($property) {
					case "t":
						return $this->createValue($value->top);
					case "l":
						return $this->createValue($value->left);
					case "b":
						return $this->createValue($value->bottom);
					case "r":
						return $this->createValue($value->right);
				}
				break;
			
			case "dimension":
				switch($property) {
					case "width":
						return $this->createValue($value->getWidth());
					case "height":
						return $this->createValue($value->getHeight());
				}
				break;
			
			case "point":
				switch($property) {
					case "x":
						return $this->createValue($value->getX());
					case "y":
						return $this->createValue($value->getY());
				}
				break;
		}
		
		return parent::getProperty($variable, $property);
	}
}



?>

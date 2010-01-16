<?

// Required files
require_once 'class.imagemask.php'; // Image Mask class Copyright Andrew Collington, 2004 - http://php.amnuts.com/
require_once 'package.fig.php'; // Font tools class Copyright Matsuda Shota 2006  - http://sgssweb.com/
require_once 'default_pluggable_set.php';

class Generate_Tk_Page_Title 
{

	var $text;
	var $gradientfile;
	var $fontfile;
	var $storelocation;
	var $gradients_storelocation;
	var $mask_storelocation;
	var $page_titles_storelocation;
	var $width;
	var $height;
	var $fsize;
	var $color;
	var $align;
	var $antialias;
	var $debug;
	var $leading;
	var $filename_string;
	var $nocache;
	var $filename_ref;
	var $filename;
	var $mask_filename;
	var $gradient_filename; 
	var $divide;
	
	function Generate_Tk_Page_Title($string, $gradientfile, $fontfile, $storelocation, $width, $fsize, $color="000000", $leading, $align="left", $antialias=4, $debug=false, $dontcache = false) 
	{
		
		// Define basic settings
		$this->text = $string;
		$this->gradient_file = $gradientfile;
		$this->fontfile = $fontfile;
		$this->storelocation = $storelocation;
		$this->width = $width;
		$this->height = 0;
		$this->fsize = $fsize;
		$this->color = $color;
		$this->align = $align;
		$this->antialias = $antialias;
		$this->debug = $debug;
		$this->dontcache = $dontcache;
		$this->leading = (isset($leading) ? $leading : $this->fsize+2);
		$this->divide = false;
		
		// File properties
		$this->text = $this->format_text($string);
		$this->filename_string = $this->format_filename_string($string);
		$this->nocache = $this->format_nocache();
		$this->filename_ref = $this->format_filename_ref();
		$this->filename = $this->format_filename();
		$this->mask_filename = $this->format_mask_filename();
		$this->gradient_filename = $this->format_gradient_filename(); 
		
		// If the file does not exist, create it
		if (!file_exists($this->filename) && file_exists($this->fontfile)) 
		{
			
			$this->generate_mask_image();
			$this->generate_gradient_image();
			$this->save_image();
			$this->cleanup_tmp_files();
			
			
		}
		
	}
	
	function return_values() 
	{
		if (file_exists($this->filename)) 
		{
			$this->height = $this->get_image_height();
			return array($this->height, $this->format_root_url($this->filename));
		}
	}
	
	function format_root_url($string) 
	{
		$string = str_replace($_SERVER['DOCUMENT_ROOT'], '', $string); 
		
		if (substr($string, 0, 1) != "/")
		{
			$string = "/".$string;
		}	
		
		return $string;
		
	}
	
	function format_filename_string($string) 
	{
		$string = strtolower($string);
		return $string;
	
	}
	
	function format_nocache() 
	{
		return (($this->debug || $this->dontcache) ? rand(0, 10000) : '');
	
	}
	
	function format_filename_ref() 
	{
		return md5($this->filename_string.$this->gradient_file.$this->color.$this->fontfile.$this->width.$this->fsize.$this->leading.$this->align.$this->antialias.$this->nocache);
	
	}
	
	function format_filename() 
	{
		return $this->storelocation.$this->filename_ref.'.png';
	
	}
	
	function format_mask_filename() 
	{
		return $this->storelocation.$this->filename_ref.'_mask.png';
	
	}
	
	function format_gradient_filename() 
	{
		return $this->storelocation.$this->filename_ref.'_gradient.png';
	
	}
	
	function generate_gradient_image() 
	{	
	
		$imgradient = imagecreatetruecolor($this->maskwidth, $this->maskheight);
		
		if ($this->gradient_file != "" && file_exists($this->gradient_file) && !is_dir($this->gradient_file))
		{
			list($gradientwidth, $gradientheight) = getimagesize($this->gradient_file);
			$gradientimg = imagecreatefrompng($this->gradient_file);
			for ($i=0; $i<$this->number_of_lines; $i++) 
			{
				$dist = ($this->maskheight/$this->number_of_lines)*$i;
				imagecopyresized($imgradient, $gradientimg, 0, $dist, 0, 0, $this->maskwidth, ($this->maskheight/$this->number_of_lines), $gradientwidth, $gradientheight); 
			}
		} 
		else 
		{
			$rgb = $this->hex2rgb($this->color);
			$fillcol = imagecolorallocate($imgradient, $rgb[0], $rgb[1], $rgb[2]);
			imagefill($imgradient, 0, 0, $fillcol);
		}
		
		imagepng($imgradient, $this->gradient_filename);
		
	}
	
	function generate_mask_image() 
	{
		$pluggableSet = new DefaultFontImagePluggableSet();
		$pluggableSet->defaultVariables = array(
			"text" => $this->text,
			"size" => $this->fsize,
			"font" => $this->fontfile,
			"color" => "0x000000",
			"alpha" => "100",
			"leading" => $this->leading,
			"padding" => 0,
			"width" => $this->width,
			"height" => null,
			"align" => $this->align,
			"valign" => "middle",
			"bgcolor" => "0xffffff",
			"bgtrans" => "false",
			"blank" => false,
			"bgimage" => null,
			"antialias" => $this->antialias,
			"type" => "png",
			"palette" => null,
			"quality" => 100,
			"file" => $this->mask_filename
		);
		$fig = new FontImageGenerator();
		$fig->setPluggableSet($pluggableSet);
		$fig->execute();
		$this->number_of_lines = $fig->canvas->getLineCount();
		$this->maskwidth = $fig->canvas->graphics->clipBounds->width;
		$this->maskheight = $fig->canvas->graphics->clipBounds->height;
		if ($this->divide) 
		{
			$this->maskheight = $this->maskheight/2;
			$this->number_of_lines -= 1;
		}
	}
	
	function format_text($text) 
	{
		$text = html_entity_decode($text, ENT_QUOTES,'UTF-8');
		preg_match_all("/\S+/", $text, $matches);
		if (count($matches[0]) == 1) 
		{
			$text = $text."\n.";
			$this->divide = true;
			//$this->leading = $this->fsize+2;	
		}
		return $text;	
	} 
	
	function cleanup_tmp_files() 
	{
		if (file_exists($this->gradient_filename)) 
		{
			unlink($this->gradient_filename);
		}
		if (file_exists($this->mask_filename)) 
			{
			unlink($this->mask_filename);
		}
		
	}
	
	function save_image() 
	{
		if (file_exists($this->gradient_filename) && file_exists($this->mask_filename)) {
			$masker = new imageMask();
			$masker->setDebugging($this->debug);
			$masker->maskOption();
			if ($masker->loadImage($this->gradient_filename)) 
			{
				if ($masker->applyMask($this->mask_filename)) 
				{
					$masker->saveImage($this->filename); 
					return true;
				}
			}
		}
	}
	
	function debug_output() 
	{
		
		$output = "";
		
		$output .= '<table border="1" cellpadding="2">';
		
		$output .= '<tr><td><small>Font file:</small></td><td><small>'.$this->fontfile.'</small></td></tr>';
		
		$output .= '<tr><td><small>Gradient file:</small></td><td><small>'.$this->gradient_file.'</small></td></tr>';
		
		$output .= '<tr><td><small>Store location:</small></td><td><small>'.$this->storelocation.'</small></td></tr>';
		
		$output .= '<tr><td><small>Output file:</small></td><td><small><a href="'.$this->format_root_url($this->filename).'">'.$this->format_root_url($this->filename).'</a></small></td></tr>';
		
		$output .= '<tr><td><small>Font size</small></td><td><small>'.$this->fsize.'</small></td></tr>';
		
		$output .= '<tr><td><small>Leading</small></td><td><small>'.$this->leading.'</small></td></tr>';
		
		$output .= '<tr><td><small>Antialias:</small></td><td><small>'.$this->antialias.'</small></td></tr>';
		
		$output .= '<tr><td><small>Color:</small></td><td><small>'.$this->color.'</small></td></tr>';
		
		$output .= '<tr><td><small>Align:</small></td><td><small>'.$this->align.'</small></td></tr>';
		
		$output .= '</table>';
		
		return $output;

	}
	
	function get_image_height() 
	{
		
		$imgdata = getimagesize($this->filename);
		return $imgdata[1];
		
	}
	
	function hex2rgb($color) 
	{
		if(($perarow != 0) && ($counter != 0) && ($counter % $perarow == 0))
		
        $color = str_replace('#','',$color);
        $s = strlen($color) / 3;
		$sc = (($s != 0) ? 2/$s : 0); 
        $rgb[] = hexdec(str_repeat(substr($color,0,$s),$sc));
        $rgb[] = hexdec(str_repeat(substr($color,$s,$s),$sc));
        $rgb[] = hexdec(str_repeat(substr($color,2*$s,$s),$sc));
        return $rgb;
    }
	
	function utf2ascii($string) 
	{
		$iso88591  = "\\xE0\\xE1\\xE2\\xE3\\xE4\\xE5\\xE6\\xE7";
		$iso88591 .= "\\xE8\\xE9\\xEA\\xEB\\xEC\\xED\\xEE\\xEF";
		$iso88591 .= "\\xF0\\xF1\\xF2\\xF3\\xF4\\xF5\\xF6\\xF7";
		$iso88591 .= "\\xF8\\xF9\\xFA\\xFB\\xFC\\xFD\\xFE\\xFF";
		$ascii = "aaaaaaaceeeeiiiidnooooooouuuuyyy";
		return strtr(mb_strtolower(utf8_decode($string), 'ISO-8859-1'),$iso88591,$ascii);
	}
}


?>
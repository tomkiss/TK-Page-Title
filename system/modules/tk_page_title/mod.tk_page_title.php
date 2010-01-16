<?php

/*

Copyright Tom Kiss 2008
www.tomkiss.net
TK Page Title

*/

require_once 'incs/class.generate_tk_page_title.php'; // TK Page Title Class

class TK_Page_Title {

    var $return_data	= ''; 

    // -------------------------------------
    //  Constructor
    // -------------------------------------

    function TK_Page_Title()
    {
	
		global $TMPL, $DB;
		
		$output = "";
		
		$text = $TMPL->tagdata;
		$text = $this->replace_global_variables($text);
		$alt_text = $this->parse_alt_text($text);
		
		// Load Module settings
		$query = $DB->query("SELECT * FROM exp_tk_page_title_settings WHERE tkps_id = '1'");
		if ($query->num_rows > 0)
		{	
			foreach($query->row as $name => $pref)
			{
				${$name} = $pref;
			}
			
			// Get and set HTML settings
			$this->linker = (!$TMPL->fetch_param('link') ? '' : $TMPL->fetch_param('link'));
			$this->target = (!$TMPL->fetch_param('target') ? '' : ' target="'.$TMPL->fetch_param('target').'"');
			$this->type = (!$TMPL->fetch_param('type') ? 'main' : strtolower($TMPL->fetch_param('type')));
			$this->id = (!$TMPL->fetch_param('id') ? '' : ' id="'.$TMPL->fetch_param('id').'"');
			$this->name = (!$TMPL->fetch_param('name') ? '' : ' name="'.$TMPL->fetch_param('name').'"');
			$this->classy = (!$TMPL->fetch_param('class') ? ' class="pagetitle"' : ' class="pagetitle '.$TMPL->fetch_param('class').'"');
			$this->onclick = (!$TMPL->fetch_param('onclick') ? '' : ' onclick="'.$TMPL->fetch_param('onclick').'"'); 
			$this->style = (!$TMPL->fetch_param('style') ? '' : '; '.$TMPL->fetch_param('style')); 
			$this->align = ($TMPL->fetch_param('align') && ($TMPL->fetch_param('align') == "right" || $TMPL->fetch_param('align') == "center" || $TMPL->fetch_param('align') == "left-adjust" || $TMPL->fetch_param('align') == "center-adjust" || $TMPL->fetch_param('align') == "right-adjust" || $TMPL->fetch_param('align') == "adjust") ? $TMPL->fetch_param('align') : 'left'); 
			$this->cssonly = (!$TMPL->fetch_param('cssonly') ? false : true);
			
			// Text settings
			$this->antialias = (($TMPL->fetch_param('antialias') == "false") ? 1 : 4);
			$this->casey = (!$TMPL->fetch_param('case') ? false : $TMPL->fetch_param('case'));
			if ($this->casey == "upper") {
				$text = mb_strtoupper($text, "utf-8");
			} else if ($this->casey == "title") {
				$text = $this->titleCase($text);
			}
			
			// Debug settings (if set to true, prevents images from caching)
			$this->debug = (!$TMPL->fetch_param('debug') ? false : true); 
			$this->nocache = (!$TMPL->fetch_param('nocache') ? false : true); 
			
			// Get the preferences for this tk page title
			$query = $DB->query("SELECT * FROM exp_tk_page_title WHERE tk_page_title_name = '".$this->type."'");
			if ($query->num_rows == 0) 
			{
				$query = $DB->query("SELECT * FROM exp_tk_page_title WHERE tk_page_title_name = 'Default'");
			} 
			foreach($query->row as $name => $pref) 
			{
				$this->{$name} = $pref;
			}
			
			// Overwrite defaults
			$this->fontfile = $tkps_font_location.$this->tk_page_title_fontfile;
			$this->gradientfile = (!($TMPL->fetch_param('gradient')) ? $tkps_gradient_location.$this->tk_page_title_gradientfile : $tkps_gradient_location.html_entity_decode($TMPL->fetch_param('gradient')) );
			$this->tag = (!$TMPL->fetch_param('tag') ? $this->tk_page_title_htmltag : strtolower($TMPL->fetch_param('tag')));
			$this->width = (!$TMPL->fetch_param('width') ? $this->tk_page_title_width : strtolower($TMPL->fetch_param('width')));
			$this->fsize = (!$TMPL->fetch_param('fsize') ? $this->tk_page_title_fontsize : strtolower($TMPL->fetch_param('fsize')));
			$this->color = (!$TMPL->fetch_param('color') ? $this->tk_page_title_fontcol : strtolower($TMPL->fetch_param('color')));
			$this->leading = (!$TMPL->fetch_param('leading') ? $this->tk_page_title_leading : strtolower($TMPL->fetch_param('leading')));
			
			// Double check requirements
			if (is_writable($tkps_pagetitle_location) && file_exists($this->fontfile) && (isset($this->color) || (file_exists($tkps_gradient_location.$TMPL->fetch_param('gradient'))))) 
			{
				
				// Generate image
				$tk_page_title_image = new Generate_Tk_Page_Title($text, $this->gradientfile, $this->fontfile, $tkps_pagetitle_location, $this->width, $this->fsize, $this->color, $this->leading, $this->align, $this->antialias, $this->debug, $this->nocache);
				$tk_page_title_image_props = $tk_page_title_image->return_values();
				
				// Generate HTML
				$imghtml = ' style="display:block; background-repeat: no-repeat; background-image: url('.$tk_page_title_image_props[1].'); height: '.$tk_page_title_image_props[0].'px; width: '.$this->width.'px'.$this->style.'"';
				
				if ($this->cssonly) 
				{
					$output = $imghtml;
				} 
				else 
				{
					
					// Format link
					if (!empty($this->linker)) 
					{
						$linky = array('<a href="'.$this->linker.'" '.$this->target.$imghtml.$this->onclick.$this->name.' >', '</a>');
						$this->name = "";
						$imghtml = "";
					} 
					else 
					{
						$linky = array('', '');
					}					
					// Create HTML, based on type
					$output = '<'.$this->tag.$imghtml.$this->id.$this->name.$this->classy.' title="'.$alt_text.'">'.$linky[0].'<span style="display: none">'.$alt_text.'</span>'.$linky[1].'</'.$this->tag.'>';	
					
				}
				
				if ($this->debug) 
				{
					
					echo $tk_page_title_image->debug_output();
					
				}
				
				$this->return_data = $output;
				
			}
		}
	}
	
	// Convert to title case
	function titleCase($title) 
	{ 
	
		// List of words that we do not want in title case
		$smallwordsarray = array( 'of','a','the','and','an','or','nor','but','is','if','then','else','when', 'at','from','by','on','off','for','in','out','over','to','into','with' ); 
		$words = explode(' ', $title); 
		foreach ($words as $key => $word) { 
			if ($key == 0 or !in_array($word, $smallwordsarray)) $words[$key] = ucwords($word); 
		} 
		$newtitle = implode(' ', $words);
		
		return $newtitle; 
		
	}

	// Global function that parses the text every time
	function parse_alt_text($string) 
	{
		
		if (empty($string)) 
		{
			$string = 'No input text given';
		} 
		else
		{
		  	$string = html_entity_decode($string, ENT_QUOTES,'UTF-8');
			$string = str_ireplace(array("‘", "’", "–"), array("'", "'", "-"), $string);
		}
		$string = htmlentities($string, ENT_QUOTES,'UTF-8');
		
	   	return $string;
		
	}
	
	// Replace Global Variables
	function replace_global_variables($text) 
	{
		
		global $FNS;
		$replacer = new Global_variable_replacer();
		$text = $FNS->var_swap($text, $replacer->globals_lookup);
		
		return $text;	
		
	}
    
}


// Global Variable Replacer
class Global_variable_replacer
{
        var $globals_lookup = array();

        function Global_variable_replacer($site_id='')
        {
                $this->initGlobalsLookup($site_id);
        }
		
        function initGlobalsLookup($site_id='')
        {
                global $DB, $PREFS;

                $site_id = (empty($site_id) ? $PREFS->core_ini['site_id'] : $site_id);

                $sql = "SELECT * FROM exp_global_variables";            
                if (!empty($site_id))
                {
                        $sql .= " WHERE site_id='".$site_id."'";
                }
                        $sql .= " ORDER BY variable_name ASC";
                
                $query = $DB->query($sql);
                
                if($query->num_rows > 0)
                {
                        foreach($query->result as $row) 
                {
                                $key = $row['variable_name'];
                                $value = $row['variable_data'];
                                $this->globals_lookup[$key] = $value;
                        }
                }
        }

} // end class


?>
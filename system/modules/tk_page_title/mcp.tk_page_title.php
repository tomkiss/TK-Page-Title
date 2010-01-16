<?php

/*

Copyright Tom Kiss 2008
www.tomkiss.net
TK Page Title - Control Panel

*/

require_once('incs/class.generate_tk_page_title.php');

class Tk_page_title_CP { 


    var $version        = '1.1.2'; 

    // -------------------------
    //  Constructor
    // -------------------------
    
    function Tk_page_title_CP( $switch = TRUE )
    {
        global $IN, $DB;
		 
		/** -------------------------------
		/**  Is the module installed?
		/** -------------------------------*/
        
        $query = $DB->query("SELECT COUNT(*) AS count FROM exp_modules WHERE module_name = 'Tk_page_title'");
        
        if ($query->row['count'] == 0)
        {
        	return;
        }
		
		/**	----------------------------------------
		/**	 Get current version
		/**	----------------------------------------*/
		
		$query	= $DB->query("SELECT module_version FROM exp_modules WHERE module_name = 'Tk_page_title' LIMIT 1");
		
		if ($query->num_rows > 0)
		{
			$this->current = $query->row['module_version'];
		}
		
        
        if ($switch)
        {
            switch($IN->GBL('P'))
            {
			     
                 case 'delete'          :	$this->delete_tk_page_title();
                     break;
                 case 'delete_confirm'          :	$this->delete_confirm_tk_page_title();
                     break;
                 case 'modify'          :	$this->modify_tk_page_title();
                     break;
                 case 'modify_settings'          :	$this->modify_tk_page_title_settings();
                     break;
                 case 'add'          :	$this->modify_tk_page_title("new");
                     break;
                 case 'save'          :	$this->save_tk_page_title();
                     break;
                 case 'save_settings' 	:	$this->save_tk_page_title_settings();
                     break;
                 case 'upgrade'          :	$this->upgrade_tk_page_title();
                     break;
                 case 'clear'          :	$this->clear_tk_page_title();
                     break;
                 default                :	$this->tk_page_title_home();
                     break;

            }
        }
    }
    // END

	
	
	// ----------------------------------------
    //  Delete
    // ----------------------------------------

    function delete_tk_page_title()
    {
	    
		global $IN, $DSP, $LANG, $SESS, $DB;        
        
        if ( ! $IN->GBL('delete', 'POST'))
        {
            return $this->tk_page_title_home();
        }

        $ids = array();
                
        foreach ($_POST as $key => $val)
        {        
            if (strstr($key, 'delete') AND ! is_array($val))
            {
                $ids[] = "tk_page_title_id = '".$DB->escape_str($val)."'";
            }        
        }
        
        $IDS = implode(" OR ", $ids);
        
        $DB->query("DELETE FROM exp_tk_page_title WHERE ".$IDS);
    
        $message = (count($ids) == 1) ? $LANG->line('tk_page_title_deleted') : $LANG->line('tk_page_titles_deleted');


        return $this->tk_page_title_home($message);
	}

	
	
	// ----------------------------------------
    //  Clear cache
    // ----------------------------------------

    function clear_tk_page_title()
    {
	    
		global $IN, $DSP, $LANG, $SESS, $DB;        
		
		$message = "";
		
		// Get all page title types
		
		$query = $DB->query("SELECT tkps_pagetitle_location FROM exp_tk_page_title_settings WHERE tkps_id = '1'");
		
		if ($query->num_rows == 0)
		{
			return $this->tk_page_title_home();
		}
		
		foreach($query->row as $name => $pref)
		{
			${$name} = $pref;
		}
	
		$delcount = 0;
	
		if (is_dir($tkps_pagetitle_location)) {
			if ($handle = opendir($tkps_pagetitle_location)) {
				while (false !== ($file = readdir($handle))) { 
					if (substr($file, -3) == "png") {  
						unlink($tkps_pagetitle_location.$file);
						$delcount++;
					}
				}
				closedir($handle); 
			} else {
				$message = 	$LANG->line('tk_page_title_opendirfail');
			}
		} else {
			$message = 	$LANG->line('tk_page_title_nodir');
		}
				
		if ($message == "") {
			$message = ($delcount != 1 ? $delcount.$LANG->line('tk_page_title_delfiles') : $delcount.$LANG->line('tk_page_title_delfile'));
		}
		// Display result of attempt to clear cache
        return $this->tk_page_title_home($message);
	}


	/** -------------------------------------------
    /**  Save settings
    /** -------------------------------------------*/

    function save_tk_page_title_settings()
    {
    	global $IN, $DSP, $LANG, $DB, $OUT;
		
		$required	= array('tkps_font_location', 'tkps_gradient_location', 'tkps_pagetitle_location');
    	
    	$data	= array();
    	$errors	= array();
		
		// Check through POST data
    	foreach($required as $var) {
    		if ( ! isset($_POST[$var]) OR $_POST[$var] == '') {
    			return $OUT->show_user_error('submission', $LANG->line('tk_page_title_missing_fields'));
    		}
    		$data[$var] = $_POST[$var];
    	}
		
		$data['tkps_font_location'] = $_POST['tkps_font_location'];
		$data['tkps_gradient_location'] = $_POST['tkps_gradient_location'];
		$data['tkps_pagetitle_location'] = $_POST['tkps_pagetitle_location'];
		
		// Error checking
    	if (isset($data['tkps_font_location']) && $data['tkps_font_location'] != '') {
			if (!is_dir($data['tkps_font_location'])) {
				$errors[] = $LANG->line('tk_page_title_gradient_not_directory');
			}
			if (!file_exists($data['tkps_gradient_location'])) {
				$errors[] = $LANG->line('tk_page_title_gradient_location_not_found');
			}
		}
    	if (isset($data['tkps_gradient_location']) && $data['tkps_gradient_location'] != '') {
			if (!is_dir($data['tkps_gradient_location'])) {
				$errors[] = $LANG->line('tk_page_title_font_not_directory');
			}
			if (!file_exists($data['tkps_gradient_location'])) {
				$errors[] = $LANG->line('tk_page_title_font_location_not_found');
			}
		}
    	if (isset($data['tkps_pagetitle_location']) && $data['tkps_pagetitle_location'] != '') {
			if (!is_dir($data['tkps_pagetitle_location'])) {
				$errors[] = $LANG->line('tk_page_title_pagetitle_not_directory');
			}
			if (!file_exists($data['tkps_pagetitle_location'])) {
				$errors[] = $LANG->line('tk_page_title_pagetitle_location_not_found');
			}
		}
		
		// Output any errors
		if (sizeof($errors) > 0) {
			return $OUT->show_user_error('submission', $errors);
		}
	
		// Post new data
		$DB->query($DB->update_string('exp_tk_page_title_settings', $data, "tkps_id = '".$DB->escape_str($_POST['tkps_id'])."'"));
		$message = $LANG->line('tk_page_title_settings_saved');
		
		// Redirect on success
		return $this->tk_page_title_home($message);
    
	}	

	/** -------------------------------------------
    /**  Save 
    /** -------------------------------------------*/

    function save_tk_page_title()
    {
    	global $IN, $DSP, $LANG, $DB, $OUT;
		
		$required	= array('tk_page_title_name', 'tk_page_title_width', 'tk_page_title_fontsize', 'tk_page_title_align', 'tk_page_title_leading', 'tk_page_title_htmltag', 'tk_page_title_fontfile', 'tk_page_title_fontcol');
    	
    	$data	= array();
    	$errors	= array();
		
		$query = $DB->query("SELECT * FROM exp_tk_page_title_settings WHERE tkps_id = '1'");
		
		if ($query->num_rows == 0)
		{
			return $this->tk_page_title_home();
		}
		
		foreach($query->row as $name => $pref)
		{
			${$name} = $pref;
		}
		
		// Check through POST data
    	foreach($required as $var) {
    		if ( ! isset($_POST[$var]) OR $_POST[$var] == '') {
    			return $OUT->show_user_error('submission', $LANG->line('tk_page_title_missing_fields'));
    		}
    		$data[$var] = $_POST[$var];
    	}
		$data['tk_page_title_gradientfile'] = $_POST['tk_page_title_gradientfile'];
		
		// Error checking
    	if (isset($data['tk_page_title_gradientfile']) && $data['tk_page_title_gradientfile'] != 'none') {
			if (is_dir($tkps_gradient_location.$data['tk_page_title_gradientfile'])) {
				$errors[] = $LANG->line('tk_page_title_gradient_not_found');
			}
			if (!file_exists($tkps_gradient_location.$data['tk_page_title_gradientfile'])) {
				$errors[] = $LANG->line('tk_page_title_gradient_not_found');
			}
		}
		if (is_dir($tkps_font_location.$data['tk_page_title_fontfile'])) {
			$errors[] = $LANG->line('tk_page_title_fontfile_not_found');
		}
		if (!file_exists($tkps_font_location.$data['tk_page_title_fontfile'])) {
			$errors[] = $LANG->line('tk_page_title_fontfile_not_found');
		}
		
		// Output any errors
		if (sizeof($errors) > 0) {
			return $OUT->show_user_error('submission', $errors);
		}
	
		// Post new data
		if ($_POST['tk_page_title_id'] == 'new') {
			$data['tk_page_title_id'] = '';    		
			$DB->query($DB->insert_string('exp_tk_page_title', $data));
			$message = $LANG->line('tk_page_title_saved');
		} else {    		
			$DB->query($DB->update_string('exp_tk_page_title', $data, "tk_page_title_id = '".$DB->escape_str($_POST['tk_page_title_id'])."'"));
			$message = $LANG->line('tk_page_title_saved');
		}
		
		// Redirect on success
		return $this->tk_page_title_home($message);
    
	}	
	
	// ----------------------------------------
    //  Delete confirm
    // ----------------------------------------

    function delete_confirm_tk_page_title()
    { 
        global $IN, $DSP, $LANG;
        
        if ( ! $IN->GBL('toggle', 'POST'))
        {
            return $this->tk_page_title_home();
        }
		
        $DSP->title = $LANG->line('tk_page_title_name');
        $DSP->crumb = $DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title', $LANG->line('tk_page_title_name'));
		$DSP->crumb .= $DSP->crumb_item($LANG->line('delete'));

        $DSP->body	.=	$DSP->form_open(array('action' => 'C=modules'.AMP.'M=tk_page_title'.AMP.'P=delete'));
        
        $i = 0;
        
        foreach ($_POST as $key => $val)
        {        
            if (strstr($key, 'toggle') AND ! is_array($val))
            {
                $DSP->body	.=	$DSP->input_hidden('delete[]', $val);
                
                $i++;
            }        
        }
        
		$DSP->body .= $DSP->heading($DSP->qspan('alert', $LANG->line('tk_page_title_delete_confirm')));
		$DSP->body .= $DSP->div('box');
		$DSP->body .= $DSP->qdiv('defaultBold', $LANG->line('tk_page_title_delete_question'));
		$DSP->body .= $DSP->qdiv('alert', BR.$LANG->line('action_can_not_be_undone'));
		$DSP->body .= $DSP->qdiv('', BR.$DSP->input_submit($LANG->line('delete')));
		$DSP->body .= $DSP->qdiv('alert',$DSP->div_c());
		$DSP->body .= $DSP->div_c();
		$DSP->body .= $DSP->form_close();
    
	}
	
	
	// ----------------------------------------
    //  Modify settings
    // ----------------------------------------

    function modify_tk_page_title_settings()
    {
	     
		 global $IN, $DSP, $LANG, $DB, $SESS, $PREFS;           
        
        $tkps_id = 1;
		
        /** ----------------------------
        /**  Form Values
        /** ----------------------------*/
                
        $DSP->title  = $LANG->line('tk_page_title_settings');
        $DSP->crumb  = $DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title', $LANG->line('tk_page_title_module_name'));
		$DSP->crumb  .= $DSP->crumb_item($LANG->line('tk_page_title_settings'));
	
        $DSP->body .=	$DSP->form_open(
        								array(
        										'action' => 'C=modules'.AMP.'M=tk_page_title'.AMP.'P=save_settings', 
        										'name'	=> 'configuration',
        										'id' 	=> 'configuration'
        									),
										array('tkps_id' => $tkps_id)
        								);
			
		$query = $DB->query("SELECT * FROM exp_tk_page_title_settings WHERE tkps_id = '".$DB->escape_str($tkps_id)."'");
		
		if ($query->num_rows == 0)
		{
			return $this->tk_page_title_home();
		}
		
		foreach($query->row as $name => $pref)
		{
			${$name} = $pref;
		}	
				
    	/** ---------------------------
    	/**  Begin Creating Form
    	/** ---------------------------*/
    	
    	$LANG->fetch_language_file('publish');
    	
				
		$r =	$DSP->table('tableBorder', '0', '', '100%');
		$r .=	$DSP->tr();
		$r .=   $DSP->td('tableHeadingAlt', '', '2');
		$r .=   $LANG->line('tk_page_title_settings');
		$r .=   $DSP->td_c();
		$r .=	$DSP->tr_c();
		
		$i = 0;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tkps_font_location
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_settings_fl', 'tk_page_title_settings_fl');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_text('tkps_font_location', $tkps_font_location, '20', '120', 'input', '90%');
		
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';

		// tkps_gradient_location
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_settings_gr', 'tk_page_title_settings_gr');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_text('tkps_gradient_location', $tkps_gradient_location, '20', '120', 'input', '90%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';

		// tkps_pagetitle_location
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_settings_pt', 'tk_page_title_settings_pt');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_text('tkps_pagetitle_location', $tkps_pagetitle_location, '20', '120', 'input', '90%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// update
		$r .= $DSP->td($style, '50%', '');
		$r .= "";
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->qdiv('itemWrapperTop', $DSP->input_submit($LANG->line('update')));
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$r .= $DSP->table_c();
        
		$DSP->body .= $r.$DSP->form_close();   
			
	}
	
	
	// ----------------------------------------
    //  Modify
    // ----------------------------------------

    function modify_tk_page_title($id = '')
    {
	        global $IN, $DSP, $LANG, $DB, $SESS, $PREFS;           
        
        $id = ( ! $IN->GBL('id', 'GET')) ? $id : $IN->GBL('id');
        
		// Extra head content
		$DSP->extra_header .= '
		
	<link rel="stylesheet" href="'.$PREFS->ini('theme_folder_url', 1).'tk_page_title/lib/colorpicker/css/colorpicker.css" type="text/css" />

	<script type="text/javascript" src="'.$PREFS->ini('theme_folder_url', 1).'tk_page_title/lib/colorpicker/js/colorpicker.js"></script>
	
	<script> 
		$(document).ready(function() {
			$(\'#tk_page_title_fontcol\').ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val(hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			})
			.bind(\'keyup\', function(){
				$(this).ColorPickerSetColor(this.value);
			});
		});
	</script>
		
		';
		
		
        if ($id == '')
        {
            return $this->tk_page_title_home();
        }
        
        /** ----------------------------
        /**  Form Values
        /** ----------------------------*/
        
		$tk_page_title_id = '';
		$tk_page_title_name = '';
		$tk_page_title_width = '';
		$tk_page_title_fontfile = '';
		$tk_page_title_gradientfile = '';
		$tk_page_title_fontsize = '24';
		$tk_page_title_fontcol = '000000';
		$tk_page_title_htmltag = 'h1';
		$tk_page_title_leading = '26';
		$tk_page_title_align = 'left';
        
        if ($id != 'new')
        {
        	$query = $DB->query("SELECT * FROM exp_tk_page_title WHERE tk_page_title_id = '".$DB->escape_str($id)."'");
        	
        	if ($query->num_rows == 0)
        	{
           		return $this->tk_page_title_home();
        	}
        	
        	foreach($query->row as $name => $pref)
        	{
        		${$name} = $pref;
			}	
        }
		
		$query2 = $DB->query("SELECT * FROM exp_tk_page_title_settings WHERE tkps_id = '1'");
		
		if ($query2->num_rows == 0)
		{
			return $this->tk_page_title_home();
		}
		
		foreach($query2->row as $name => $pref)
		{
			${$name} = $pref;
		}
		
                
        $DSP->title  = $LANG->line('tk_page_title_name');
        $DSP->crumb  = $DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title', $LANG->line('tk_page_title_module_name'));
		$DSP->crumb  .= $DSP->crumb_item(($id == 'new') ? $LANG->line('tk_page_title_new') : $LANG->line('tk_page_title_edit'));
		
		$DSP->body .= ($id == 'new') ? $DSP->qdiv('tableHeading', $LANG->line('tk_page_title_new')) : $DSP->qdiv('tableHeading', $LANG->line('tk_page_title_edit'));
		
        $DSP->body .=	$DSP->form_open(
        								array(
        										'action' => 'C=modules'.AMP.'M=tk_page_title'.AMP.'P=save', 
        										'name'	=> 'configuration',
        										'id' 	=> 'configuration'
        									),
        								array('tk_page_title_id' => $id)
        								);
            	
    	/** ---------------------------
    	/**  Begin Creating Form
    	/** ---------------------------*/
    	
    	$LANG->fetch_language_file('publish');
    	
				
		$r =	$DSP->table('tableBorder', '0', '', '100%');
		$r .=	$DSP->tr();
		$r .=   $DSP->td('tableHeadingAlt', '', '2');
		$r .=   $LANG->line('tk_page_title_options');
		$r .=   $DSP->td_c();
		$r .=	$DSP->tr_c();
		
		$i = 0;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tk_page_title_name
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_name', 'tk_page_title_name');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		if ($tk_page_title_name == "Default") {
    		$r .= $DSP->input_hidden('tk_page_title_name', $tk_page_title_name).$tk_page_title_name;
		} else {
    		$r .= $DSP->input_text('tk_page_title_name', $tk_page_title_name, '20', '120', 'input', '50%');
		}
		
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';

		// tk_page_title_width
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_width_long', 'tk_page_title_width_long');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->input_text('tk_page_title_width', $tk_page_title_width, '20', '20', 'input', '20%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tk_page_title_fontsize
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_fontsize_long', 'tk_page_title_fontsize_long');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->input_text('tk_page_title_fontsize', $tk_page_title_fontsize, '20', '20', 'input', '20%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		// tk_page_title_leading
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_leading', 'tk_page_title_leading');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->input_text('tk_page_title_leading', $tk_page_title_leading, '20', '20', 'input', '20%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tk_page_title_align
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_align', 'tk_page_title_align');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_select_header("tk_page_title_align", 0, 1, "25%");
		$align_types = array("left", "center", "right", "left-adjust", "center-adjust", "right-adjust", "adjust");
		for ($i=0; $i<sizeof($align_types); $i++) 
		{
			if ($tk_page_title_align == $align_types[$i]) 
			{
				$r .= $DSP->input_select_option($align_types[$i], $align_types[$i], '1');
			} 
			else 
			{ 
				$r .= $DSP->input_select_option($align_types[$i], $align_types[$i]);
			}
		}
		$r .= $DSP->input_select_footer();
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tk_page_title_htmltag
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_htmltag', 'tk_page_title_htmltag');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->input_text('tk_page_title_htmltag', $tk_page_title_htmltag, '20', '120', 'input', '20%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		
		// tk_page_title_fontcol
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_fontcol', 'tk_page_title_fontcol');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= '<span style="float: left; display: block;padding: 3px 6px 0 0">#</span>'.$DSP->input_text('tk_page_title_fontcol', $tk_page_title_fontcol, '20', '6', 'input', '12%');
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		$r .= $DSP->tr_c();
		
		// tk_page_title_fontfile
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_fontfile_long', 'tk_page_title_fontfile_long');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_select_header("tk_page_title_fontfile", 0, 1, "50%");
		$font_files = $this->list_files($tkps_font_location);
		for ($i=0; $i<sizeof($font_files); $i++) 
		{
			if ($tk_page_title_fontfile == $font_files[$i]) 
			{
				$r .= $DSP->input_select_option($font_files[$i], $font_files[$i], '1');
			} 
			else 
			{ 
				$r .= $DSP->input_select_option($font_files[$i], $font_files[$i]);
			}
		}
		$r .= $DSP->input_select_footer();
    	$r .= $DSP->td_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		$r .= $DSP->tr_c();
		
		// tk_page_title_gradientfile
		$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->div('defaultBold');
		$r .= $LANG->line('tk_page_title_gradientfile_long', 'tk_page_title_gradientfile_long');
		$r .= $DSP->div_c();
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
		$r .= $DSP->input_select_header("tk_page_title_gradientfile", 0, 1, "50%");
		$r .= $DSP->input_select_option("none", $LANG->line("none"), ($tk_page_title_gradientfile == "none" ? '1' : '0'));
		$gradient_files = $this->list_files($tkps_gradient_location);
		for ($i=0; $i<sizeof($gradient_files); $i++) 
		{
			if ($tk_page_title_gradientfile == $gradient_files[$i]) 
			{
				$r .= $DSP->input_select_option($gradient_files[$i], $gradient_files[$i], '1');
			} 
			else 
			{ 
				$r .= $DSP->input_select_option($gradient_files[$i], $gradient_files[$i]);
			}
		}
		$r .= $DSP->input_select_footer();
    	$r .= $DSP->td_c();
		
		$i++;
		$style = ($i % 2) ? 'tableCellOne' : 'tableCellTwo';
		$r .= $DSP->tr_c();
		
		// update
		$r .= $DSP->td($style, '50%', '');
		$r .= "";
    	$r .= $DSP->td_c();
    	
    	$r .= $DSP->td($style, '50%', '');
    	$r .= $DSP->qdiv('itemWrapperTop', $DSP->input_submit(($id == 'new') ? $LANG->line('submit') : $LANG->line('update')));
    	$r .= $DSP->td_c();
		$r .= $DSP->tr_c();
		
		$r .= $DSP->table_c();
        
		$DSP->body .= $r.$DSP->form_close();       
	}
	
	
	// ----------------------------------------
    //  Module Homepage
    // ----------------------------------------

    function tk_page_title_home($msg = '')
    {
	
		global $DSP, $LANG, $PREFS, $FNS, $DB;
                        
        $DSP->title  = $LANG->line('tk_page_title_module_name');
        $DSP->crumb  = $LANG->line('tk_page_title_module_name');
        
		$DSP->right_crumb($LANG->line('tk_page_title_create_new'), BASE.AMP.'C=modules'.AMP.'M=tk_page_title'.AMP.'P=add');
        
		$DSP->body .= $this->upgrade_check();
		
        $DSP->body .= $DSP->qdiv('tableHeading', $LANG->line('tk_page_title_types')); 
        
        if ($msg != '')
        {
			$DSP->body .= $DSP->qdiv('successBox', $DSP->qdiv('success', $msg));
        }
		
        $query = $DB->query("SELECT tk_page_title_name, tk_page_title_id, tk_page_title_align, tk_page_title_width, tk_page_title_fontsize, tk_page_title_fontfile, tk_page_title_gradientfile, tk_page_title_fontcol, tk_page_title_htmltag , tk_page_title_leading FROM exp_tk_page_title");
        
		
		$query2 = $DB->query("SELECT * FROM exp_tk_page_title_settings WHERE tkps_id = '1'");
		
		if ($query2->num_rows == 0)
		{
			return $this->tk_page_title_home();
		}
		
		foreach($query2->row as $name => $pref)
		{
			${$name} = $pref;
		}
		
        $DSP->body	.=	$DSP->toggle();
                
        $DSP->body	.=	$DSP->form_open(array('action' => 'C=modules'.AMP.'M=tk_page_title'.AMP.'P=delete_confirm', 'name' => 'target', 'id' => 'target'));
    
	
        $DSP->body	.=	$DSP->table('tableBorder', '0', '0', '100%').
						$DSP->tr().
						$DSP->table_qcell('tableHeadingAlt', 
											array(
													$LANG->line('tk_page_title_name'),
													$LANG->line('tk_page_title_width'),
													$LANG->line('tk_page_title_fontsize'),
													$LANG->line('tk_page_title_leading'),
													$LANG->line('tk_page_title_align'),
													$LANG->line('tk_page_title_htmltag'),
													$LANG->line('tk_page_title_fontcol'),
													$LANG->line('tk_page_title_locations'),
													''.NBS.NBS
												 )
											).
						$DSP->tr_c();
		
		$i = 0;

		foreach ($query->result as $row)
		{				
			$style = ($i++ % 2) ? 'tableCellOne' : 'tableCellTwo';
                      
            $DSP->body .= $DSP->tr();
            
            	$namelink = $DSP->qdiv('defaultBold',
											$DSP->anchor(BASE.AMP.'C=modules'.
											AMP.'M=tk_page_title'.
											AMP.'P=modify'.
											AMP.'id='.$row['tk_page_title_id'],
											$row['tk_page_title_name']));
						
				$DSP->body .= $DSP->table_qcell($style, 
									array(
											$namelink,
											$row['tk_page_title_width'],
											$row['tk_page_title_fontsize'],
											$row['tk_page_title_leading'],
											$row['tk_page_title_align'],
											$row['tk_page_title_htmltag'],
											'<span style="float: left; padding-right: 6px">#'.$row['tk_page_title_fontcol'].'</span><span title="#'.$row['tk_page_title_fontcol'].'" style="float: left; width: 12px; height: 12px; background-color: #'.$row['tk_page_title_fontcol'].'; border: 1px solid black; display: block; font-size:0px; ">&nbsp;</span>',
											'<span style="font-size: smaller">'.$LANG->line('tk_page_title_fontfile').': '.$row['tk_page_title_fontfile'].'</span><br/>
											<span style="font-size: smaller">'.$LANG->line('tk_page_title_gradientfile').': '.$row['tk_page_title_gradientfile'].'</span>',
											$DSP->input_checkbox('toggle[]', $row['tk_page_title_id'], '', ($row['tk_page_title_name'] == "Default" ? 'disabled' : ''))
										 )
									).
				$DSP->tr_c();
				
				$examplestring = $row['tk_page_title_name'];
				
				$example_img = new Generate_Tk_Page_Title($examplestring, $tkps_gradient_location.$row['tk_page_title_gradientfile'], $tkps_font_location.$row['tk_page_title_fontfile'], $tkps_pagetitle_location, $row['tk_page_title_width'], $row['tk_page_title_fontsize'], $row['tk_page_title_fontcol'], $row['tk_page_title_leading'], $row['tk_page_title_align']);
				$example_img_props = $example_img->return_values();
				
				$DSP->body .= $DSP->table_row(array(
									array(
											'text'		=> $DSP->div('defaultLight').
'<p>{exp:tk_page_title type="'.$row['tk_page_title_name'].'"}'.$examplestring.'{/exp:tk_page_title}</p><br/>
<img src="'.$example_img_props[1].'" />'.$DSP->div_c(),
											'class'		=> $style,
											'colspan'	=> '9'
										)
									)
								);
					
				require_once 'mod.tk_page_title.php';
				
				$TMPL = array();
				$TMPL["tagdata"] = array("");
				
				$DSP->body .= $DSP->tr_c();
		}
		
		
		
		
		$style = ($i++ % 2) ? 'tableCellOne' : 'tableCellTwo';
		$DSP->body .= $DSP->table_row(array(
									array(
											'text'		=> $DSP->anchor('http://www.tomkiss.net/ee/add-on/tk_page_title', $LANG->line('tk_page_title_documentation'))." | ".$DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title'.AMP.'P=modify_settings', $LANG->line('tk_page_title_settings'))." | ".$DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title'.AMP.'P=clear', $LANG->line('tk_page_title_clear')),
											'class'		=> $style,
											'colspan'	=> '8'
										),

									array(
											'text'		=> $DSP->qdiv('itemWrapper', $DSP->input_submit($LANG->line('delete'))),
											'class'		=> $style,
											'colspan'	=> '1'
										)
									)
								);
		
        $DSP->body	.=	$DSP->table_close(); 
        $DSP->body	.=	$DSP->form_close();  
		
		/*
        $DSP->body	.=	$DSP->table_c(); 
    	
		$DSP->body	.=	$DSP->qdiv('itemWrapperTop', $DSP->input_submit($LANG->line('delete')));             
        
        $DSP->body	.=	$DSP->form_close();     
		*/
		
	}
	
	function check_dir($dir)
	{
		
		global $FNS;
		
		if (substr($dir, 0, -1) != "/")
		{
		} else {
			$dir .= "/";
		}	
		
		$dir = $FNS->remove_double_slashes($dir);
		
		return $dir;
	}
	
	function list_files($dir, $type = false)
	{
		
		$file_array = array();
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) { 
				
					if (is_file($dir.$file) && substr($file, 0, 1) != ".")
					{
						if ($type != false && substr($file, -3) == $type) 
						{  
							$file_array[] = $file;
						} else {
							$file_array[] =  $file;
						}
					}
				}
				closedir($handle); 
			}
		}
		
		return $file_array;
	}


	function upgrade_check()
	{
		global $DB, $DSP, $IN, $LANG;
		
		
		/**	----------------------------------------
		/**	 Get current version
		/**	----------------------------------------*/
		
		$query	= $DB->query("SELECT module_version FROM exp_modules WHERE module_name = 'Tk_page_title' LIMIT 1");
		
		if ($query->num_rows > 0)
		{
			$this->current = $query->row['module_version'];
		}
		
		
		if ( $this->current < $this->version)
		{
			$link = $DSP->anchor(BASE.AMP.'C=modules'.AMP.'M=tk_page_title'.AMP.'P=upgrade', $LANG->line('tk_page_title_upgrade'));
			
			$msg = $DSP->qdiv('itemWrapperTop', $DSP->qdiv('box', $LANG->line('tk_page_title_upgrade_msg').BR.BR.$DSP->qspan('bold', $link)));
		
			return $msg;
		}
	
		return FALSE;
	}
	
	
		
	// ----------------------------------------
    //  upgrade
    // ----------------------------------------

    function upgrade_tk_page_title()
    {
		
		global $DB, $LANG, $PREFS, $FNS;

		// Check we're on a different version
		if ( $this->current == '' OR $this->current >= $this->version )
		{
			return $this->tk_page_title_home();
		}
    	
		/*
		// Begin upgrade scripts
    	$sql = array();
		
		//
        if ($this->current < '1.1.3')
		{
			
		}
        
        foreach ($sql as $query)
        {
            $DB->query($query);
        }
		*/
		
		// Complete upgrade
		$DB->query( $DB->update_string('exp_modules', array('module_version' => $this->version), array('module_name' => 'Tk_page_title')));
	
		return $this->tk_page_title_home($LANG->line('tk_page_title_upgrade_complete'));
	
	}
	
	
	
    // ----------------------------------------
    //  Module installer
    // ----------------------------------------

    function tk_page_title_module_install()
    {
        global $DB, $PREFS;    
		
		$doc_root = $this->check_dir($PREFS->ini("theme_folder_path"));
        
        $sql[] = "INSERT INTO exp_modules (module_id, 
                                           module_name, 
                                           module_version, 
                                           has_cp_backend) 
                                           VALUES 
                                           ('', 
                                           'Tk_page_title', 
                                           '$this->version', 
                                           'y')";
                       
        $sql[] = "CREATE TABLE IF NOT EXISTS `exp_tk_page_title` (
                 `tk_page_title_id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                 `tk_page_title_name` TEXT NOT NULL ,
                 `tk_page_title_width` INT(6) ,
                 `tk_page_title_fontsize` INT(6) ,
                 `tk_page_title_fontfile` TEXT NOT NULL ,
                 `tk_page_title_gradientfile` TEXT NOT NULL ,
                 `tk_page_title_fontcol` TEXT NOT NULL ,
                 `tk_page_title_htmltag` TEXT NOT NULL ,
                 `tk_page_title_leading` TEXT NOT NULL ,
                 `tk_page_title_align` TEXT NOT NULL ,
                 PRIMARY KEY (`tk_page_title_id`));";     
    
 		$sql[] = "INSERT INTO exp_tk_page_title 
			(tk_page_title_id, 
			tk_page_title_name, 
			tk_page_title_width, 
			tk_page_title_fontsize, 
			tk_page_title_fontfile, 
			tk_page_title_gradientfile, 
			tk_page_title_fontcol, 
			tk_page_title_htmltag, 
			tk_page_title_leading, 
			tk_page_title_align) 
		VALUES 
			('', 
			'Default', 
			600, 
			24, 
			'verdana.ttf', 
			'default.png', 
			'000000', 
			'h1',
			26,
			'left')";
		
        $sql[] = "CREATE TABLE IF NOT EXISTS `exp_tk_page_title_settings` (
                 `tkps_id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                 `tkps_font_location` TEXT NOT NULL ,
                 `tkps_gradient_location` TEXT NOT NULL ,
                 `tkps_pagetitle_location` TEXT NOT NULL ,
                 PRIMARY KEY (`tkps_id`));";
		
		$sql[] = "INSERT INTO exp_tk_page_title_settings 
		(tkps_id,
		 tkps_font_location,
		 tkps_gradient_location,
		 tkps_pagetitle_location)
		VALUES
		('',
		 '".$doc_root."tk_page_title/fonts/',
		 '".$doc_root."tk_page_title/gradients/',
		 '".$doc_root."tk_page_title/pagetitles/')
		";
		
		
        foreach ($sql as $query)
        {
            $DB->query($query);
        }
        
        return true;
    }
    // END
	
	
	// ----------------------------------------
    //  Module de-installer
    // ----------------------------------------

    function tk_page_title_module_deinstall()
    {
        global $DB;    

        $query = $DB->query("SELECT module_id
                             FROM exp_modules 
                             WHERE module_name = 'Tk_page_title'"); 
                
        $sql[] = "DELETE FROM exp_module_member_groups 
                  WHERE module_id = '".$query->row['module_id']."'";      
                  
        $sql[] = "DELETE FROM exp_modules 
                  WHERE module_name = 'Tk_page_title'";
                  
        $sql[] = "DELETE FROM exp_actions 
                  WHERE class = 'Tk_page_title'";
                  
        $sql[] = "DELETE FROM exp_actions 
                  WHERE class = 'Tk_page_title_CP'";
                  
        $sql[] = "DROP TABLE IF EXISTS exp_tk_page_title";
                  
        $sql[] = "DROP TABLE IF EXISTS exp_tk_page_title_settings";

        foreach ($sql as $query)
        {
            $DB->query($query);
        }

        return true;
    }
    // END



} 
// END CLASS 

?>
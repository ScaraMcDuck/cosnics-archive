<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../admin_manager.class.php';
require_once dirname(__FILE__) . '/../admin_manager_component.class.php';
require_once dirname(__FILE__) . '/../../admin_rights.class.php';
/**
 * Admin component
 */
class AdminManagerLogViewerComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => null)), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('LogsViewer')));
        $trail->add_help('administration');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = $this->build_form();
       
        $this->display_header($trail);
        echo $form->toHtml() . '<br />';
        
        if($form->validate())
        {
        	$type = $form->exportValue('type');
        	$dokeos_type = $form->exportValue('dokeos_type');
        	$server_type = $form->exportValue('server_type');
        	$lines = $form->exportValue('lines');
        }
        else 
        {
        	$type = 'dokeos';
        	
        	$dir = Path :: get(SYS_FILE_PATH) . 'logs/';
			$content = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES, false);
        	
        	$dokeos_type = $content[0];
        	$lines = '10';
        }

        $this->display_logfile_table($type, $dokeos_type, $server_type, $lines);
        
        $this->display_footer();
    }
    
    function build_form()
    {
    	$form = new FormValidator('logviewer', 'post', $this->get_url());
		$renderer =& $form->defaultRenderer();
		$renderer->setElementTemplate(' {element} ');

		$types = array('dokeos' => Translation :: get('DokeosLogs'), 'server' => Translation :: get('ServerLogs'));
		$lines = array('10' => '10 ' . Translation :: get('lines'), '20' => '20 ' . Translation :: get('lines'), 
					   '50' => '50 ' . Translation :: get('lines'), 'all' => Translation :: get('AllLines'));
		
		$dir = Path :: get(SYS_FILE_PATH) . 'logs/';
		$content = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES, false);
		foreach($content as $file)
		{
			if(substr($file, 0, 1) == '.') continue;
			
			$files[$file] = $file;	
		}
		
		$server_types = array('php' => Translation :: get('PHPErrorLog'), 'httpd' => Translation :: get('HTTPDErrorLog'));
		
		$form->addElement('select', 'type', '', $types, array('id' => 'type'));
		$form->addElement('select', 'dokeos_type', '', $files, array('id' => 'dokeos_type'));
		
		$form->addElement('select', 'server_type', '', $server_types, array('id' => 'server_type'));
		$form->addElement('select', 'lines', '', $lines);
		
		$form->addElement('submit', 'submit', Translation :: get('Ok'), array('class' => 'positive finish'));
		$form->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/log_viewer.js'));
		
		return $form;
    }
    
    function display_logfile_table($type, $dokeos_type, $server_type, $count)
    {
    	if($type == 'dokeos')
    	{
    		$file = Path :: get(SYS_FILE_PATH) . 'logs/' . $dokeos_type;
    	}
    	else 
    	{
    		$file = PlatformSetting :: get($server_type . '_location');
    	}
    	
    	if(file_exists($file) && !is_dir($file))
    	{
    		$table = new HTML_Table(array('style' => 'background-color: lightblue; width: 100%;', 'cellspacing' => 0));
    		$this->read_file($file, $table, $count);
	    	echo $table->toHtml();
    	}
    	else 
    	{
    		echo '<div class="warning-message">' . Translation :: get('NoLogfilesFound') . '</div>';
    	}
    }
    
    function read_file($file, &$table, $count)
    {
    	$fh = fopen($file, 'r');
    	$string = file_get_contents($file);
    	$lines = explode("\n", $string);
    	
    	if($count == 'all' || count($lines) < $count)
    		$count = count($lines) - 1;
    	
    	$row = 0;
    	foreach($lines as $line)
    	{
			if($row >= $count)
				break;
				
    		if($line == '')
				continue;
				
    		$border = ($row < $count - 1) ? 'border-bottom: 1px solid black;' : '';
    		//$color = ($row % 2 == 0) ? 'background-color: yellow;' : '';
    		
    		if(stripos($line, 'error') !== false)
    			$color = 'background-color: red;';
    		elseif(stripos($line, 'warning') !== false)
    			$color = 'background-color: pink;';
    		else 
    			$color = null;
    		
    		$table->setCellContents($row, 0, $line);
    		$table->setCellAttributes($row, 0, array('style' => "$border $color padding: 5px;"));
    		$row++;
    	}
    	
    	fclose($fh);
    }
}
?>
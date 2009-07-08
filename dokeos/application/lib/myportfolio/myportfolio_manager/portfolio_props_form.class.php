<?php
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_plugin_path().'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to
 *
 */
class PortfolioPropertiesForm extends FormValidator
{
   /**#@+
    * Constant defining a form parameter
 	*/
	private $tree;
	private $item;
	private $parent;

	/**
	 * The publication that will be changed (when using this form to edit a
	 * publication)
	 */
	private $form_user;

	/**
	 * Creates a new learning object publication form.
	 * @param LearningObject The learning object that will be published
	 * @param string $tool The tool in which the object will be published
	 * @param boolean $email_option Add option in form to send the learning
	 * object by email to the receivers
	 */
    function PortfolioPropertiesForm($parent, $form_user, $action, $item)
    {
		parent :: __construct('publish', 'post', $action);
		//$this->learning_object = $learning_object;
		$this->form_user = $form_user;
		$this->item = $item;
		$this->parent = $parent;
		$this->build_form();
		$this->setDefaults();
        
    }

	/**
	 * Sets the default values of the form.
	 *
	 * By default the publication is for everybody who has access to the tool
	 * and the publication will be available forever.
	 */
    function setDefaults()
    {
    	$defaults = array();
		parent :: setDefaults($defaults);
    }
	/**
	 * Builds the form by adding the necessary form elements.
	 */
    function build_form()
    {
//    	$vis_options = array(0 => Translation :: get('Pf_public'),1 => Translation :: get('Pf_platform'),2 => Translation :: get('Pf_private'));
    	$vis_options = array(0 => Translation :: get('Pf_platform'),1 => Translation :: get('Pf_private'));
    	$this->addElement('select', 'visibility', Translation :: get('Pf_Visibility'), $vis_options);
    	$ptm = PFTreeManager :: get_instance();
    	$root= $ptm->get_root_element($this->form_user);
    	//echo $root;
    	$this->tree = array();
	$this->tree[-1]= "-Do not move this page-";
    	$this->build_treelist($root);
    	//print_r($this->tree);
    	if($root != $this->item) $this->addElement('select', 'move', Translation :: get('Pf_movepage'), $this->tree);
		$this->addElement('submit', 'delete', Translation :: get('Pf_deletepage'));
		$this->addElement('submit', 'submit', Translation :: get('Ok'));
		
		
    }

	function build_treelist($page, $recursion = 0)
	{
		if($page != $this->item)
		{
			$ptm = PFTreeManager :: get_instance();
			$children = $ptm->get_children($page);
			for ($i=0; $i<$recursion; $i++)
			{
				$name.="&nbsp;&nbsp;";
			}
			$portpub = $this->parent->retrieve_portfolio_publication_from_item($page);
			$pubobj = $portpub->get_publication_object();
			$name .= $pubobj->get_title();
			//$name .= "bliep";
			//echo $name;
			//$this->tree[] = array($page => $name);
			$this->tree[$page] = $name;
			//print_r($this->tree);
			foreach ($children as $child)
			{
				$this->build_treelist($child, $recursion + 1);
			}
		}
	}

	function commit_changes()
	{
		$pdm = PortfolioDataManager :: get_instance();
		$values = $this->exportValues();
		$pdm->change_visibility($this->form_user->get_id(), $values['visibility']);
		if($values['move'] != -1) $pdm->set_parent($this->item, $values['move']);
		//print_r($this->exportValues());		
	}

}
?>

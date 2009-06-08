<?php
// $Id: FormValidator.class.php 20404 2009-05-08 09:32:08Z Scara84 $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/

/**
 * Objects of this class can be used to create/manipulate/validate user input.
 */
class WizardPageValidator extends FormValidator
{
/**
    * Contains the mapping of actions to corresponding HTML_QuickForm_Action objects
    * @var array
    */
    var $_actions = array();

   /**
    * Contains a reference to a Controller object containing this page
    * @var      HTML_QuickForm_Controller
    * @access   public
    */
    var $controller = null;

   /**
    * Should be set to true on first call to buildForm()
    * @var bool
    */
    var $_formBuilt = false;

   /**
    * Class constructor
    *
    * @access public
    */
    function WizardPageValidator($formName, $method = 'post', $target = '', $attributes = null)
    {
        $this->FormValidator($formName, $method, '', $target, $attributes);
    }


   /**
    * Registers a handler for a specific action.
    *
    * @access public
    * @param  string                name of the action
    * @param  HTML_QuickForm_Action the handler for the action
    */
    function addAction($actionName, &$action)
    {
        $this->_actions[$actionName] =& $action;
    }


   /**
    * Handles an action.
    *
    * If an Action object was not registered here, controller's handle()
    * method will be called.
    *
    * @access public
    * @param  string Name of the action
    * @throws PEAR_Error
    */
    function handle($actionName)
    {
        if (isset($this->_actions[$actionName])) {
            return $this->_actions[$actionName]->perform($this, $actionName);
        } else {
            return $this->controller->handle($this, $actionName);
        }
    }


   /**
    * Returns a name for a submit button that will invoke a specific action.
    *
    * @access public
    * @param  string  Name of the action
    * @return string  "name" attribute for a submit button
    */
    function getButtonName($actionName)
    {
        return '_qf_' . $this->getAttribute('id') . '_' . $actionName;
    }


   /**
    * Loads the submit values from the array.
    *
    * The method is NOT intended for general usage.
    *
    * @param array  'submit' values
    * @access public
    */
    function loadValues($values)
    {
        $this->_flagSubmitted = true;
        $this->_submitValues = $values;
        foreach (array_keys($this->_elements) as $key) {
            $this->_elements[$key]->onQuickFormEvent('updateValue', null, $this);
        }
    }


   /**
    * Builds a form.
    *
    * You should override this method when you subclass HTML_QuickForm_Page,
    * it should contain all the necessary addElement(), applyFilter(), addRule()
    * and possibly setDefaults() and setConstants() calls. The method will be
    * called on demand, so please be sure to set $_formBuilt property to true to
    * assure that the method works only once.
    *
    * @access public
    * @abstract
    */
    function buildForm()
    {
        $this->_formBuilt = true;
    }


   /**
    * Checks whether the form was already built.
    *
    * @access public
    * @return bool
    */
    function isFormBuilt()
    {
        return $this->_formBuilt;
    }


   /**
    * Sets the default action invoked on page-form submit
    *
    * This is necessary as the user may just press Enter instead of
    * clicking one of the named submit buttons and then no action name will
    * be passed to the script.
    *
    * @access public
    * @param  string    default action name
    */
    function setDefaultAction($actionName)
    {
        if ($this->elementExists('_qf_default')) {
            $element =& $this->getElement('_qf_default');
            $element->setValue($this->getAttribute('id') . ':' . $actionName);
        } else {
            $this->addElement('hidden', '_qf_default', $this->getAttribute('id') . ':' . $actionName);
        }
    }


   /**
    * Returns 'safe' elements' values
    *
    * @param   mixed   Array/string of element names, whose values we want. If not set then return all elements.
    * @param   bool    Whether to remove internal (_qf_...) values from the resultant array
    */
    function exportValues($elementList = null, $filterInternal = false)
    {
        $values = parent::exportValues($elementList);
        if ($filterInternal) {
            foreach (array_keys($values) as $key) {
                if (0 === strpos($key, '_qf_')) {
                    unset($values[$key]);
                }
            }
        }
        return $values;
    }
}

?>

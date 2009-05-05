<?php
/**
 *
 * @author Michael Kyndt
 */
class ReportingTemplateViewer {

    private $parent;
    public function ReportingTemplateViewer($parent)
    {
        $this->parent = $parent;
    }

    /**
     * by registration id
     * @param <type> $reporting_template_registration_id
     */
    public function show_reporting_template($reporting_template_registration_id,$params)
    {
        $rpdm = ReportingDataManager :: get_instance();
        if(!$reporting_template_registration = $rpdm->retrieve_reporting_template_registration($reporting_template_registration_id))
        {
            $this->parent->display_header($trail);
			Display :: error_message(Translation :: get("NotFound"));
			$this->parent->display_footer();
			exit;
        }

        $this->show_reporting_template_by_name($reporting_template_registration->get_classname(), $params);
    }

    /**
     * by class name
     * @param <type> $reporting_template_name
     */
    public function show_reporting_template_by_name($classname,$params)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_CLASSNAME, $classname);
        $rpdm = ReportingDataManager :: get_instance();
        $templates = $rpdm->retrieve_reporting_template_registrations($condition);

        $reporting_template_registration = $templates->next_result();

        $trail = $params['trail'];

        //registration doesn't exist
        if(!isset($reporting_template_registration))
        {
            $this->parent->display_header($trail);
			Display :: error_message(Translation :: get("NotFound"));
			$this->parent->display_footer();
			exit;
        }

        //is platform template
        if ($reporting_template_registration->isPlatformTemplate() && !$this->get_user()->is_platform_admin())
		{
			$this->parent->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->parent->display_footer();
			exit;
		}

        $application = $reporting_template_registration->get_application();
        $base_path = (Application :: is_application($application) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));
        $file = $base_path .$application. '/reporting/templates/'.DokeosUtilities :: camelcase_to_underscores($reporting_template_registration->get_classname()).'.class.php';;
        require_once($file);

        $template = new $classname($this->parent,$reporting_template_registration->get_id(),$params,$trail);

        //$template->set_reporting_blocks_function_parameters($params);

		$this->parent->display_header($trail);

        if(isset($_GET['s']))
		{
            $template->show_reporting_block($_GET['s']);
		}
        echo $template->to_html();

		$this->parent->display_footer();
    }
}
?>

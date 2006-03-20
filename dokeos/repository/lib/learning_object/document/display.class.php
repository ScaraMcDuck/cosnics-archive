<?php
/**
 * @package learningobject.document
 */
class DocumentDisplay extends LearningObjectDisplay
{
	public function DocumentDisplay(&$object)
	{
		parent :: LearningObjectDisplay($object);
	}
	public function get_full_html()
	{
		$object = $this->get_learning_object();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$object->get_type().'.gif" alt="'.$object->get_type().'"/></div>';
		$html[] = '<div class="title">'.$object->get_title().'</div>';
		$html[] = '<div class="description">'.$object->get_description().'</div>';
		//$html[] = '<div class="filesize">'.$object->get_filesize().' kb'.'</div>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>
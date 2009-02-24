<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once 'HTML/Table.php';

class ForumToolBrowserComponent extends ForumToolComponent
{
	private $action_bar;
	private $introduction_text;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','forum'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		$this->introduction_text = $publications->next_result();
		$this->action_bar = $this->get_action_bar();
	
		$table = $this->get_table_html();
		
		$this->display_header(new BreadcrumbTrail());
		
		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			echo $this->display_introduction_text($this->introduction_text);
		}
		
		echo $this->action_bar->as_html();
		echo $table->toHtml();
		
		$this->display_footer();
	}
	
	function get_table_html()
	{
		$table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
		
		$this->create_table_header($table);
		$row = 2;
		$this->create_table_forums($table, $row, 0);
		$this->create_table_categories($table, $row);
		
		return $table;
	}
	
	function create_table_header($table)
	{
		$table->setCellContents(0, 0, '');
		$table->setCellAttributes(0, 0, array('colspan' => 5, 'class' => 'category'));
		
		$table->setHeaderContents(1, 0, Translation :: get('Forum'));
		$table->setCellAttributes(1, 0, array('colspan' => 2));
		$table->setHeaderContents(1, 2, Translation :: get('Topics'));
		$table->setCellAttributes(1, 2, array('width' => 50));
		$table->setHeaderContents(1, 3, Translation :: get('Posts'));
		$table->setCellAttributes(1, 3, array('width' => 50));
		$table->setHeaderContents(1, 4, Translation :: get('LastPost'));
		//$table->setCellAttributes(1, 4, array('width' => 130));
	}
	
	function create_table_categories($table, &$row)
	{
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
		$condition = new AndCondition($conditions);
		
		$categories = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($condition, $offset, $count, $order_property, $order_direction);
		
		while($category = $categories->next_result())
		{
			$table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a>');
			$table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
			$table->setCellContents($row, 2, '');
			$table->setCellAttributes($row, 2, array('colspan' => 3, 'class' => 'category_right'));
			$row++;	
			$this->create_table_forums($table, $row, $category->get_id());
		}
		
	}
	
	function create_table_forums($table, &$row, $parent)
	{
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'forum');
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$course_groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}
		$cond = new EqualityCondition('type','forum');
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), $parent, $user_id, $course_groups, $condition, false, array (Forum :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC), 0, -1, null, $cond);
		
		while($publication = $publications->next_result())
		{
			$forum = $publication->get_learning_object();
			$title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())) . '">' . $forum->get_title() . '</a><br />' . strip_tags($forum->get_description());
			
			$table->setCellContents($row, 0, '<img height="25" width="46" title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'forum/forum_read.gif" />');
			$table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
			$table->setCellContents($row, 1, $title);
			$table->setCellAttributes($row, 1, array('width' => '100%', 'class' => 'row1'));
			$table->setCellContents($row, 2, '');
			$table->setCellAttributes($row, 2, array('class' => 'row2'));
			$table->setCellContents($row, 3, '');
			$table->setCellAttributes($row, 3, array('class' => 'row2'));
			$table->setCellContents($row, 4, '');
			$table->setCellAttributes($row, 4, array('class' => 'row2'));
			$row++;
		}
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('general'));
		
		return $action_bar;
	}
}
?>
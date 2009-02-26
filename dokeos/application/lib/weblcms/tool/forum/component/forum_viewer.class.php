<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class ForumToolViewerComponent extends ForumToolComponent
{
	private $action_bar;
	private $forum;
	private $current_forum;
	private $forums;
	private $topics;
	private $pid;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$this->pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$this->forum = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->pid)->get_learning_object();
		$this->current_forum = $this->forum;
		$this->retrieve_children($this->forum);
		
		$this->action_bar = $this->get_action_bar();
		$topics_table = $this->get_topics_table_html();
		$forum_table =  $this->get_forums_table_html();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $pid)), $this->forum->get_title()));
		
		$this->display_header($trail);
		echo $this->action_bar->as_html();

		echo '<br />';
		echo $topics_table->toHtml();
		echo '<br /><br />';

		echo $forum_table->toHtml();
		$this->display_footer();
	}
	
	function retrieve_children($current_forum)
	{
		$rdm = RepositoryDataManager :: get_instance();
		
		$children = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $current_forum->get_id()), array('add_date'), array(SORT_ASC) );
		while($child = $children->next_result())
		{
			$lo = $rdm->retrieve_learning_object($child->get_ref());
			$child->set_ref($lo);
			if($lo->get_type() == 'forum_topic')
			{	
				$this->topics[] = $child;
			}
			else
			{
				$this->forums[] = $child;
			}
		}
	}
	
	function get_topics_table_html()
	{
		$table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
		
		$this->create_topics_table_header($table);
		$row = 2;
		$this->create_topics_table_content($table, $row);
		$this->create_topics_table_footer($table, $row);
		
		return $table;
	}
	
	function create_topics_table_header($table)
	{
		$table->setCellContents(0, 0, '<b>' . Translation :: get('Topics') . '</b>');
		$table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));
		
		$table->setHeaderContents(1, 0, Translation :: get('Topics'));
		$table->setCellAttributes(1, 0, array('colspan' => 2));
		$table->setHeaderContents(1, 2, Translation :: get('Author'));
		$table->setCellAttributes(1, 2, array('width' => 130));
		$table->setHeaderContents(1, 3, Translation :: get('Replies'));
		$table->setCellAttributes(1, 3, array('width' => 50));
		$table->setHeaderContents(1, 4, Translation :: get('Views'));
		$table->setCellAttributes(1, 4, array('width' => 50));
		$table->setHeaderContents(1, 5, Translation :: get('LastPost'));
		$table->setCellAttributes(1, 5, array('width' => 140));
	}
	
	function create_topics_table_footer($table, $row)
	{
		$table->setCellContents($row, 0, '');
		$table->setCellAttributes($row, 0, array('colspan' => 6, 'class' => 'category'));
	}
	
	function create_topics_table_content($table, &$row)
	{
		$udm = UserDataManager :: get_instance();
		$rdm = RepositoryDataManager :: get_instance();
		
		foreach($this->topics as $topic)
		{
			$title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_TOPIC, Tool :: PARAM_PUBLICATION_ID => $this->pid, Tool :: PARAM_COMPLEX_ID => $topic->get_id())) . '">' . $topic->get_ref()->get_title() . '</a>';
			
			$count = $rdm->count_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id()));
			$last_post = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $topic->get_ref()->get_id()), array(ComplexLearningObjectItem :: PROPERTY_ADD_DATE), array(SORT_DESC), 0, 1 )->next_result();
			
			$table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . 
											 '" src="' . Theme :: get_image_path() . 'forum/topic_read.png" />');
			$table->setCellAttributes($row, 0, array('width' => 25, 'class' => 'row1', 'style' => 'height: 30px;'));
			$table->setCellContents($row, 1, $title);
			$table->setCellAttributes($row, 1, array('class' => 'row1'));
			$table->setCellContents($row, 2, $udm->retrieve_user($topic->get_user_id())->get_fullname());
			$table->setCellAttributes($row, 2, array('align' => 'center', 'class' => 'row2'));
			$table->setCellContents($row, 3, ($count > 1)?$count - 1: $count);
			$table->setCellAttributes($row, 3, array('align' => 'center', 'class' => 'row1'));
			$table->setCellContents($row, 4, '');
			$table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
			
			if($last_post)
			{
				$link = $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_TOPIC, Tool :: PARAM_PUBLICATION_ID => $this->pid, Tool :: PARAM_COMPLEX_ID => $topic->get_id())) . '#post_' . $last_post->get_id();
				$table->setCellContents($row, 5, $last_post->get_add_date() . '<br />' . $udm->retrieve_user($last_post->get_user_id())->get_fullname() . 
												 ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . 
												 '" src="' . Theme :: get_image_path() . 'forum/icon_topic_latest.png" /></a>');
			}
			else
			{
				$table->setCellContents($row, 5, '-');
			}
			
			$table->setCellAttributes($row, 5, array('align' => 'center', 'class' => 'row1'));
			$row++;
		} 
	}
	
	function get_forums_table_html()
	{
		$table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
		
		$this->create_forums_table_header($table);
		$row = 2;
		$this->create_forums_table_content($table, $row);
		
		return $table;
	}
	
	function create_forums_table_header($table)
	{
		$table->setCellContents(0, 0, '<b>' . Translation :: get('Subforums') . '</b>');
		$table->setCellAttributes(0, 0, array('colspan' => 5, 'class' => 'category'));
		
		$table->setHeaderContents(1, 0, Translation :: get('Forum'));
		$table->setCellAttributes(1, 0, array('colspan' => 2));
		$table->setHeaderContents(1, 2, Translation :: get('Topics'));
		$table->setCellAttributes(1, 2, array('width' => 50));
		$table->setHeaderContents(1, 3, Translation :: get('Posts'));
		$table->setCellAttributes(1, 3, array('width' => 50));
		$table->setHeaderContents(1, 4, Translation :: get('LastPost'));
		$table->setCellAttributes(1, 4, array('width' => 140));
	}
	
	function create_forums_table_content($table, $row)
	{
		foreach($this->forums as $forum)
		{
			$title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $this->forum->get_id(), Tool :: PARAM_COMPLEX_ID => $forum->get_id())) . '">' . $forum->get_ref()->get_title() . '</a><br />' . strip_tags($forum->get_ref()->get_description());
			
			$table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'forum/forum_read.png" />');
			$table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
			$table->setCellContents($row, 1, $title);
			$table->setCellAttributes($row, 1, array('class' => 'row1'));
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

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('NewTopic'), /*Theme :: get_image_path() . 'forum/buttons/button_topic_new.gif'*/ Theme :: get_common_image_path().'action_add.png', 
				$this->get_url(array('pid' => $this->pid, 'forum' => $this->current_forum->get_id(), Tool :: PARAM_ACTION => ForumTool :: ACTION_CREATE_TOPIC)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('forum tool'));
		return $action_bar;
	}
	
}
?>
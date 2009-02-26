<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_library_path() . 'html/bbcode_parser.class.php';

class ForumToolTopicViewerComponent extends ForumToolComponent
{
	private $action_bar;
	private $topic;
	private $posts;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
		$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$this->forum = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid)->get_learning_object();
		
		$lo = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition('id', $cid))->next_result()->get_ref();
		$this->retrieve_children($lo);
		
		$this->action_bar = $this->get_action_bar();
		$table = $this->get_posts_table();
		$trail = new BreadcrumbTrail();
		//$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $pid)), $this->forum->get_title()));
		
		$this->display_header($trail);
		echo '<a name="top"></a>';

		echo $this->action_bar->as_html() . '<br />';
		echo $table->toHtml();
		echo '<br />';

		$this->display_footer();
	}
	
	function retrieve_children($lo)
	{
		$rdm = RepositoryDataManager :: get_instance();
		
		$children = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $lo), array('add_date'), array(SORT_ASC) );
		while($child = $children->next_result())
		{
			$lo = $rdm->retrieve_learning_object($child->get_ref());
			$child->set_ref($lo);
			$this->posts[] = $child;
		}
	}
	
	function get_posts_table()
	{
		$table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));
		
		$this->create_posts_table_header($table);
		$row = 2;
		$this->create_posts_table_content($table, $row);
		
		$this->create_posts_table_footer($table, $row);
		
		return $table;
	}
	
	function create_posts_table_header($table)
	{
		$table->setCellContents(0, 0, '');
		$table->setCellAttributes(0, 0, array('colspan' => 2, 'class' => 'category'));
		
		$table->setHeaderContents(1, 0, Translation :: get('Author'));
		$table->setCellAttributes(1, 0, array('width' => 130));
		$table->setHeaderContents(1, 1, Translation :: get('Message'));
	}
	
	function create_posts_table_footer($table, $row)
	{
		$table->setCellContents($row, 0, '');
		$table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
	}
	
	function create_posts_table_content($table, &$row)
	{
		$udm = UserDataManager :: get_instance();
		
		$post_counter = 0;
		
		foreach($this->posts as $post)
		{
			$class = ($post_counter % 2 == 0 ? 'row1' : 'row2');			
			
			$user = $udm->retrieve_user($post->get_user_id()); 
			$table->setCellContents($row, 0, '<a name="post_' . $post->get_id() . '"></a><b>' . $user->get_fullname() . '</b>');
			$table->setCellAttributes($row, 0, array('class' => $class, 'width' => 150, 'valign' => 'middle', 'align' => 'center'));
			$table->setCellContents($row, 1, '<b>' . Translation :: get('Subject') . ':</b> ' . $post->get_ref()->get_title());
			$table->setCellAttributes($row, 1, array('class' => $class, 'height' => 25, 'style' => 'padding-left: 10px;'));
			
			$row++;
			
			$info = '<br /><img src="' . $user->get_full_picture_url() . '" /><br /><br />' . $post->get_add_date();
			$message = $this->format_message($post->get_ref()->get_description());
			
			$table->setCellContents($row, 0, $info);
			$table->setCellAttributes($row, 0, array('class' => $class, 'align' => 'center', 'valign' => 'top', 'height' => 150));
			$table->setCellContents($row, 1, $message);
			$table->setCellAttributes($row, 1, array('class' => $class, 'valign' => 'top', 'style' => 'padding: 10px; padding-top: 0px;'));
			
			$row++;
			
			$actions = $this->get_post_actions($post); 
			
			$table->setCellContents($row, 0, '<a href="#top"><small>' . Translation :: get('Top') . '</small></a>');
			$table->setCellAttributes($row, 0, array('class' => $class));
			$table->setCellContents($row, 1, $actions);
			$table->setCellAttributes($row, 1, array('class' => $class, 'align' => 'right', 'style' => 'padding-right: 5px;'));
			
			$row++;
			
			$table->setCellContents($row, 0, ' ');
			$table->setCellAttributes($row, 0, array('colspan' => '2', 'class' => 'spacer'));
			
			$row++;
			
			$post_counter++;
		}
	}
	
	private function format_message($message)
	{
		//$message = BbcodeParser :: get_instance()->parse($message);
		//$message = preg_replace('#\[quote(?:="(.*?)")\]((?!\[quote(?:=".*?")\]).)?#ise', 
		//						"<div class=\"quotetitle\">'\$1':</div><div class=\"quotecontent\">'\$2'" ,$message);
		
		$message = preg_replace('[\[quote="(.*)"\]]', 
								"<div class=\"quotetitle\">$1 " . Translation :: get('wrote') . ":</div><div class=\"quotecontent\">" ,$message);
		
		$message = str_replace('[/quote]', '</div>', $message);
		
		return $message;
	}
	
	function get_post_actions($cloi)
	{
		$post = $cloi->get_ref();
		
		$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
		
		$actions[] = array(
			'href' => $this->get_url(array('pid' => $pid, 'cid' => $cid, Tool :: PARAM_ACTION => ForumTool :: ACTION_QUOTE_FORUM_POST, 'quote' => $cloi->get_id())),
			'label' => Translation :: get('Quote'),
			'img' => Theme :: get_image_path() . 'forum/buttons/icon_post_quote.gif'
		);

		$actions[] = array(
			'href' => $this->get_url(array('pid' => $pid, 'cid' => $cid, Tool :: PARAM_ACTION => ForumTool :: ACTION_CREATE_FORUM_POST, 'reply' => $cloi->get_id())),
			'label' => Translation :: get('Reply'),
			'img' => Theme :: get_image_path() . 'forum/buttons/button_pm_reply.gif'
		);
		
		if($this->is_allowed(EDIT_RIGHT))
		{	
			$actions[] = array(
				'href' => $this->get_url(array('pid' => $pid, 'cid' => $cid, Tool :: PARAM_ACTION => ForumTool :: ACTION_EDIT_FORUM_POST, 'post' => $cloi->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_image_path() . 'forum/buttons/icon_post_edit.gif'
			);
		}
		
		if($this->is_allowed(DELETE_RIGHT))
		{
			$actions[] = array(
				'href' => $this->get_url(array('pid' => $pid, 'cid' => $cid, Tool :: PARAM_ACTION => ForumTool :: ACTION_DELETE_FORUM_POST, 'post' => $cloi->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_image_path() . 'forum/buttons/icon_post_delete.gif'
			);
		}
		
		return '<div style="float: right;">' . DokeosUtilities :: build_toolbar($actions) . '</div>';
		
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$cid = Request :: get(Tool :: PARAM_COMPLEX_ID);
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ReplyOnTopic'), /*Theme :: get_image_path() . 'forum/buttons/button_topic_reply.gif'*/ Theme :: get_common_image_path().'action_reply.png', $this->get_url(array('pid' => $pid, 'cid' => $cid, Tool :: PARAM_ACTION => ForumTool :: ACTION_CREATE_FORUM_POST)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	
}
?>
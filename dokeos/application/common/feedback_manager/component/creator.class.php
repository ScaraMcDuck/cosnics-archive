<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of creatorclass
 *
 * @author pieter
 */
class FeedbackManagerCreatorComponent extends FeedbackManagerComponent {
    function run()
	{

        echo "wij runnen hier de creator van feedback";
       /* $trail = $this->get_breadcrumb_trail();

        if(Request :: get(CategoryManager :: PARAM_CATEGORY_ID))
        {
            require_once dirname(__FILE__).'/../category_menu.class.php';
            $menu = new CategoryMenu(Request :: get(CategoryManager :: PARAM_CATEGORY_ID), $this->get_parent());
            $trail->merge($menu->get_breadcrumbs());
        }
        $trail->add(new Breadcrumb($this->get_url(),Translation :: get('AddCategory')));

		$category_id = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		$user = $this->get_user();

		$category = $this->get_category();
		$category->set_parent(isset($category_id)?$category_id:0);

		$form = new CategoryForm(CategoryForm :: TYPE_CREATE, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), $category, $user, $this);

		if($form->validate())
		{
			$success = $form->create_category();
			if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
				$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), 0, ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
			else
				//$this->redirect(Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
				$this->redirect(Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => 0));
		}
		else
		{
			$this->display_header($trail);
			echo '<br />';
			$form->display();
			$this->display_footer();
		}*/
	}
}
?>

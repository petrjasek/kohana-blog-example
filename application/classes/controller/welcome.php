<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Template
{
    public $template = 'index';

	public function action_index()
	{
        $posts = ORM::factory('blogpost')
            ->find_all();
        $this->template->posts = $posts;
	}

    public function action_detail($id)
    {
        $this->template = new View('detail');
        $this->template->post = ORM::factory('blogpost', $id);
    }

    public function action_edit($id = NULL)
    {
        $this->template = new View('edit');
        $post = ORM::factory('blogpost', $id);
        $form = Formo::factory()
            ->add('text', 'title', array('value' => $post->title))
            ->add('textarea', 'Post')
            ->add('submit', 'Save');

        $this->template->post = $post;
        $this->template->form = $form;

        if ( ! $form->validate()) {
        } else {
            // pass
        }
    }

    public function action_delete($id)
    {
        $posts = ORM::factory('blogpost');
        $posts->delete($id);
        //$this->request->redirect('/frameworks/kohana/');
        // not working
    }

} // End Welcome

<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class BookAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('score.value');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('image', 'string', array('template' => 'admin/book/personalized_list_field.html.twig'))
            ->add('title')
            ->add('categories')
            ->add('authors')
            ->add('description')
            ->add('score.value')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('title')
            ->add('image')
            ->add('description')
            ->add('categories')
            ->add('authors')
            ->add('score.value');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('image', 'string', array('template' => 'admin/book/personalized_show_field.html.twig'))
            ->add('title')
            ->add('description')
            ->add('score.value')
            ->add('categories')
            ->add('authors');
    }
}

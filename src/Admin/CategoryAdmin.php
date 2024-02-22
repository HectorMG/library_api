<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

final class CategoryAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('clone', $this->getRouterIdParameter() . '/clone')
            ->add('import');
    }

    protected function configureActionButtons(
        array $buttonList,
        string $action,
        ?object $object = null
    ): array {
        $buttonList['import'] = ['template' => 'admin/category/list__action_import.html.twig'];

        return $buttonList;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'clone' => [
                        'template' => 'admin/category/list__action_clone.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('name');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name', null, ['label' => 'Nombre']);
    }
}

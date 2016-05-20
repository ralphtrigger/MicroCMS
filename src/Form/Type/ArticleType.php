<?php

namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Description of ArticleType
 *
 * @author trigger
 */
class ArticleType extends AbstractType
{
    public function getName()
    {
        return 'article';
    }

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title', 'text')
                ->add('content', 'textarea');
    }

}

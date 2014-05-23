<?php

namespace Viteloge\AdminBundle\Service;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;


class MenuBlockService extends BaseBlockService
{
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        //$settings = array_merge($this->getDefaultSettings(), $block->getSettings());

        return $this->renderResponse( 'VitelogeAdminBundle:Block:menu_live.html.twig',
                                      array(
                                          'block' => $blockContext->getBlock(),
                                          'settings' => $blockContext->getSettings()
                                            ), $response);
    }
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
    }

}

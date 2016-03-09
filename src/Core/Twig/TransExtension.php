<?php

namespace Eng\Core\Twig;

use Symfony\Component\Translation\Translator;

class TransExtension extends \Twig_Extension
{
    /** @var \Symfony\Component\Translation\Translator $translator */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'trans',
                array($this, 'doTrans'),
                array('is_safe' => array('html'))
            )
        );
    }

    public function doTrans($str)
    {
        return $this->translator->trans($str);
    }

    public function getName()
    {
        return 'trans_extension';
    }
}

<?php

namespace AppBundle\Twig;

use AppBundle\Service\ShaFinder;
use Twig_Extension_GlobalsInterface;

/**
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /**
     * @var ShaFinder
     */
    private $shaFinder;

    /**
     * AppExtension constructor.
     *
     * @param ShaFinder $shaFinder
     */
    public function __construct(ShaFinder $shaFinder)
    {
        $this->shaFinder = $shaFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'sha' => $this->shaFinder->getSha(),
        ];
    }
}

<?php

namespace AppBundle\Twig;

use AppBundle\Service\ShaFinder;
use Twig_Extension_GlobalsInterface;

class AppExtension extends \Twig_Extension implements Twig_Extension_GlobalsInterface
{
	/**
	 * @var ShaFinder
	 */
	private $shaFinder;

	public function __construct(ShaFinder $shaFinder)
	{
		$this->shaFinder = $shaFinder;
	}

	public function getGlobals()
	{
		return [
			'sha' => $this->shaFinder->getSha(),
		];
	}

	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('price', [$this, 'priceFilter']),
		];
	}

	public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
	{
		$price = number_format($number, $decimals, $decPoint, $thousandsSep);
		$price = '$'.$price;

		return $price;
	}
}

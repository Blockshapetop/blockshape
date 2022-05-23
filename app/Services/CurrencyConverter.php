<?php

namespace App\Services;

use App\Site;

class CurrencyConverter
{
    /**
     * Conversion amount
     *
     * @var float
     */
    private $amount;

    /**
     * Site
     *
     * @var mixed null|Site
     */
    private $site = null;

    public function __construct($amount)
    {
        $this->amount = (double) $amount;
    }

    /**
     * @param mixed $site
     */
    public function setSite(Site $site)
    {
        $this->site = $site;

        return $this;
    }

    public function getSite()
    {
        if (null === $this->site) {
            $this->site = site();
        }

        return $this->site;
    }

    public function convert($currency = null)
    {
        if (null === $currency) {
            $currency = $this->getSite()->currency;
        }

        if (strtoupper($currency) !== 'MDL') {
            $ratio = $this->getRatio($currency);
            $this->amount *= $ratio;
        }

        return (double) $this->amount;
    }

    /**
     * @param $currency
     * @return mixed
     */
    private function getRatio($currency)
    {
        return config('app.conversion')[$currency];
    }
}

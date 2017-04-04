<?php

namespace CMPayments\IDeal\Response;

/**
 * Class DirectoryResponse
 * @package CMPayments\IDeal\Response
 */
class DirectoryResponse extends Response
{
    /**
     * Get array of iDEAL issuer bank codes for a country
     *
     * @param $country
     *
     * @return array
     */
    public function getIssuers($country)
    {
        $issuers = [];
        $query = '//i:Directory/i:Country/i:Issuer[../i:countryNames[text()="' . $country . '"]]';
        foreach ($this->query($query) as $issuer) {
            $id = $this->singleValue('./i:issuerID', $issuer);
            $name = $this->singleValue('./i:issuerName', $issuer);
            $issuers[$id] = $name;
        }
        return $issuers;
    }

    /**
     * Get all supported countries
     *
     * @return array
     */
    public function getCountries()
    {
        $countries = [];
        foreach ($this->query('//i:Directory/i:Country/i:countryNames') as $country) {
            $countries[] = $country->nodeValue;
        }
        return $countries;
    }

    /**
     * Get all iDEAL issuer bank codes
     *
     * @return array
     */
    public function getAllIssuers()
    {
        $issuers = [];
        foreach ($this->getCountries() as $country) {
            $issuers[$country] = $this->getIssuers($country);
        }
        return $issuers;
    }

    /**
     * Get iDEAL acquirer ID
     *
     * @return string
     */
    public function getAcquirerId()
    {
        return $this->singleValue('//i:Acquirer/i:acquirerID');
    }
}

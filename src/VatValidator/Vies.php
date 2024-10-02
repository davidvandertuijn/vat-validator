<?php

namespace Davidvandertuijn\VatValidator;

use Exception;
use Davidvandertuijn\VatValidator\Vies\Client as ViesClient;
use Davidvandertuijn\VatValidator\Vies\Exceptions\Timeout as ViesTimeoutException;
use SoapFault;
use stdClass;

class Vies
{
    /**
     * @var ViesClient
     */
    protected $oViesClient = null;

    /**
     * @var bool
     */
    protected $bValid = false;

    /**
     * @var bool
     */
    protected $bStrict = true;

    /**
     * @var string
     */
    protected $sName = '';

    /**
     * @var string
     */
    protected $sAddress = '';

    const WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Construct.
     */
    public function __construct()
    {
        ini_set('default_socket_timeout', 3);
        ini_set('max_execution_time', 30);

        $oViesClient = new ViesClient(self::WSDL_URL, [
            'connection_timeout' => 3,
            'exceptions'         => true,
        ]);

        $this->setViesClient($oViesClient);
    }

    /**
     * Get Vies Client.
     */
    private function getViesClient(): ViesClient
    {
        return $this->oViesClient;
    }

    /**
     * Set Vies Client.
     */
    private function setViesClient(ViesClient $oViesClient): void
    {
        $this->oViesClient = $oViesClient;
    }

    /**
     * Get Name.
     */
    public function getName(): string
    {
        return $this->sName;
    }

    /**
     * Set Name.
     */
    private function setName(string $sName): void
    {
        $this->sName = $sName;
    }

    /**
     * Get Address.
     */
    public function getAddress(): string
    {
        return $this->sAddress;
    }

    /**
     * Set Address.
     */
    private function setAddress(string $sAddress): void
    {
        $this->sAddress = $sAddress;
    }

    /**
     * Get Valid.
     */
    private function getValid(): bool
    {
        return $this->bValid;
    }

    /**
     * Set Valid.
     */
    private function setValid(bool $bValid): void
    {
        $this->bValid = $bValid;
    }

    /**
     * Get Strict.
     */
    public function getStrict(): bool
    {
        return $this->bStrict;
    }

    /**
     * Set Strict.
     */
    public function setStrict(bool $bStrict): void
    {
        $this->bStrict = $bStrict;
    }

    /**
     * Is Valid.
     */
    public function isValid(): bool
    {
        return $this->getValid();
    }

    /**
     * Validate.
     */
    public function validate(string $sVatNumber): bool
    {
        if (!$this->checkVat($sVatNumber)) {
            return false;
        }

        return true;
    }

    /**
     * Check Vat.
     */
    private function checkVat(string $sVatNumber): bool
    {
        try {
            $oViesClient = $this->getViesClient();

            $oResponse = $oViesClient->checkVat([
                'countryCode' => substr($sVatNumber, 0, 2),
                'vatNumber'   => substr($sVatNumber, 2, strlen($sVatNumber) - 2),
            ]);

            if (!$oResponse->valid) {
                $this->setValid(false);

                return false;
            }

            $this->setValid(true);

            $this->checkVatResponse($oResponse);

            return true;
        } catch (ViesTimeoutException $e) {
            if (!$this->getStrict()) {
                $this->setValid(true);

                return true;
            }

            $this->setValid(false);

            return false;
        } catch (SoapFault $e) {
            if (!$this->getStrict()) {
                $this->setValid(true);

                return true;
            }

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Check Vat Response.
     */
    private function checkVatResponse(stdClass $oResponse): void
    {
        $sName = (string) $oResponse->name;
        $this->setName($sName);

        $sAddress = (string) $oResponse->address;
        $this->setAddress($sAddress);
    }
}

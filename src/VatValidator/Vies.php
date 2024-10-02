<?php

namespace Davidvandertuijn\VatValidator;

use Davidvandertuijn\VatValidator\Vies\Client as ViesClient;
use Davidvandertuijn\VatValidator\Vies\Exceptions\Timeout as ViesTimeoutException;
use Exception;
use SoapFault;
use stdClass;

class Vies
{
    /**
     * @var ViesClient
     */
    protected $viesClient = null;

    /**
     * @var bool
     */
    protected $valid = false;

    /**
     * @var bool
     */
    protected $strict = true;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $address = '';

    public const WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Construct.
     */
    public function __construct()
    {
        ini_set('default_socket_timeout', 3);
        ini_set('max_execution_time', 30);

        $viesClient = new ViesClient(self::WSDL_URL, [
            'connection_timeout' => 3,
            'exceptions' => true,
        ]);

        $this->setViesClient($viesClient);
    }

    /**
     * Get Vies Client.
     */
    private function getViesClient(): ViesClient
    {
        return $this->viesClient;
    }

    /**
     * Set Vies Client.
     */
    private function setViesClient(ViesClient $viesClient): void
    {
        $this->viesClient = $viesClient;
    }

    /**
     * Get Name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Name.
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get Address.
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set Address.
     */
    private function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * Get Valid.
     */
    private function getValid(): bool
    {
        return $this->valid;
    }

    /**
     * Set Valid.
     */
    private function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * Get Strict.
     */
    public function getStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Set Strict.
     */
    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
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
        if (! $this->checkVat($sVatNumber)) {
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
            $viesClient = $this->getViesClient();

            $response = $viesClient->checkVat([
                'countryCode' => substr($sVatNumber, 0, 2),
                'vatNumber' => substr($sVatNumber, 2, strlen($sVatNumber) - 2),
            ]);

            if (! $response->valid) {
                $this->setValid(false);

                return false;
            }

            $this->setValid(true);

            $this->checkVatResponse($response);

            return true;
        } catch (ViesTimeoutException $e) {
            if (! $this->getStrict()) {
                $this->setValid(true);

                return true;
            }

            $this->setValid(false);

            return false;
        } catch (SoapFault $e) {
            if (! $this->getStrict()) {
                $this->setValid(true);

                return true;
            }

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Check Vat Response.
     */
    private function checkVatResponse(stdClass $response): void
    {
        $name = (string) $response->name;
        $this->setName($name);

        $address = (string) $response->address;
        $this->setAddress($address);
    }
}

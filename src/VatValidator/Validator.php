<?php

namespace Davidvandertuijn\VatValidator;

class Validator
{
    /**
     * @var array
     */
    protected $aMetaData = [];

    /**
     * @var bool
     */
    protected $bValid = false;

    /**
     * @var bool
     */
    protected $bStrict = true;

    /**
     * Get Meta Data.
     */
    public function getMetaData(): array
    {
        return $this->aMetaData;
    }

    /**
     * Set Meta Data.
     */
    private function setMetaData(array $aMetaData): void
    {
        $this->aMetaData = $aMetaData;
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
        $sPattern = '/^(AT|BE|BG|CHE|CY|CZ|DE|DK|EE|EL|ES|EU|FI|FR|GB|GR|HR|HU|IE|IT|LT|LU|LV|MT|NL|NO|PL|PT|RO|RS|SE|SI|SK)[A-Z0-9]{6,20}$/';

        $sVatNumber = strtoupper($sVatNumber);

        if (!preg_match($sPattern, $sVatNumber)) {
            $this->setValid(false);

            return false;
        }

        if (!self::check($sVatNumber)) {
            $this->setValid(false);

            return false;
        }

        $oVies = new Vies();

        $bStrict = $this->getStrict();
        $oVies->setStrict($bStrict);

        if (!$oVies->validate($sVatNumber)) {
            $this->setValid(false);

            return false;
        }

        $aMetaData = [
            'name'    => $oVies->getName(),
            'address' => $oVies->getAddress(),
        ];

        $this->setMetaData($aMetaData);

        $this->setValid(true);

        return true;
    }

    /**
     * Check.
     */
    private static function check(string $sVatNumber): bool
    {
        $sCountryCode = substr($sVatNumber, 0, 2);

        switch ($sCountryCode) {
            case 'AT': // Oostenrijk
                return (bool) preg_match('/^(AT)U(\d{8})$/', $sVatNumber);
            case 'BE': // België
                return (bool) preg_match('/(BE)(0?\d{9})$/', $sVatNumber);
            case 'BG': // Bulgarije
                return (bool) preg_match('/(BG)(\d{9,10})$/', $sVatNumber);
            case 'CHE': // Zwitserland
                return (bool) preg_match('/(CHE)(\d{9})(MWST)?$/', $sVatNumber);
            case 'CY': // Cyprus
                return (bool) preg_match('/^(CY)([0-5|9]\d{7}[A-Z])$/', $sVatNumber);
            case 'CZ': // Tsjechië
                return (bool) preg_match('/^(CZ)(\d{8,10})(\d{3})?$/', $sVatNumber);
            case 'DE': // Duitsland
                return (bool) preg_match('/^(DE)([1-9]\d{8})/', $sVatNumber);
            case 'DK': // Denemarken
                return (bool) preg_match('/^(DK)(\d{8})$/', $sVatNumber);
            case 'EE': // Estland
                return (bool) preg_match('/^(EE)(10\d{7})$/', $sVatNumber);
            case 'EL': // Griekenland
                return (bool) preg_match('/^(EL)(\d{9})$/', $sVatNumber);
            case 'ES': // Spanje
                return preg_match('/^(ES)([A-Z]\d{8})$/', $sVatNumber)
                    || preg_match('/^(ES)([A-H|N-S|W]\d{7}[A-J])$/', $sVatNumber)
                    || preg_match('/^(ES)([0-9|Y|Z]\d{7}[A-Z])$/', $sVatNumber)
                    || preg_match('/^(ES)([K|L|M|X]\d{7}[A-Z])$/', $sVatNumber);
            case 'EU':
                return (bool) preg_match('/^(EU)(\d{9})$/', $sVatNumber);
            case 'FI': // Finland
                return (bool) preg_match('/^(FI)(\d{8})$/', $sVatNumber);
            case 'FR': // Frankrijk
                return preg_match('/^(FR)(\d{11})$/', $sVatNumber)
                    || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]\d{10})$/', $sVatNumber)
                    || preg_match('/^(FR)(\d[(A-H)|(J-N)|(P-Z)]\d{9})$/', $sVatNumber)
                    || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]{2}\d{9})$/', $sVatNumber);
            case 'GB': // Verenigd Koninkrijk
                return preg_match('/^(GB)?(\d{9})$/', $sVatNumber)
                    || preg_match('/^(GB)?(\d{12})$/', $sVatNumber)
                    || preg_match('/^(GB)?(GD\d{3})$/', $sVatNumber)
                    || preg_match('/^(GB)?(HA\d{3})$/', $sVatNumber);
            case 'GR': // Griekenland
                return (bool) preg_match('/^(GR)(\d{8,9})$/', $sVatNumber);
            case 'HR': // Kroatië
                return (bool) preg_match('/^(HR)(\d{11})$/', $sVatNumber);
            case 'HU': // Hongarije
                return (bool) preg_match('/^(HU)(\d{8})$/', $sVatNumber);
            case 'IE': // Ierland
                return preg_match('/^(IE)(\d{7}[A-W])$/', $sVatNumber)
                    || preg_match('/^(IE)([7-9][A-Z\*\+)]\d{5}[A-W])$/', $sVatNumber)
                    || preg_match('/^(IE)(\d{7}[A-W][AH])$/', $sVatNumber);
            case 'IT': // Italië
                return (bool) preg_match('/^(IT)(\d{11})$/', $sVatNumber);
            case 'LT': // Litouwen
                return (bool) preg_match('/^(LT)(\d{9}|\d{12})$/', $sVatNumber);
            case 'LU': // Luxemburg
                return (bool) preg_match('/^(LU)(\d{8})$/', $sVatNumber);
            case 'LV': // Letland
                return (bool) preg_match('/^(LV)(\d{11})$/', $sVatNumber);
            case 'MT': // Malta
                return (bool) preg_match('/^(MT)([1-9]\d{7})$/', $sVatNumber);
            case 'NL': // Nederland
                return (bool) preg_match('/^(NL)(\d{9})B\d{2}$/', $sVatNumber);
            case 'NO': // Norwegen
                return (bool) preg_match('/^(NO)(\d{9})$/', $sVatNumber);
            case 'PL': // Polen
                return (bool) preg_match('/^(PL)(\d{10})$/', $sVatNumber);
            case 'PT': // Portugal
                return(bool) preg_match('/^(PT)(\d{9})$/', $sVatNumber);
            case 'RO': // Roemenië
                return (bool) preg_match('/^(RO)([1-9]\d{1,9})$/', $sVatNumber);
            case 'RS': // Servië
                return (bool) preg_match('/^(RS)(\d{9})$/', $sVatNumber);
            case 'SE': // Zweden
                return (bool) preg_match('/^(SE)(\d{10}01)$/', $sVatNumber);
            case 'SI': // Slovenië
                return (bool) preg_match('/^(SI)([1-9]\d{7})$/', $sVatNumber);
            case 'SK': // Slowakije
                return (bool) preg_match('/^(SK)([1-9]\d[(2-4)|(6-9)]\d{7})$/', $sVatNumber);
            default:
                return false;
        }
    }
}

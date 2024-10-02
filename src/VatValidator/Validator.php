<?php

namespace Davidvandertuijn\VatValidator;

class Validator
{
    /**
     * @var array
     */
    protected $metaData = [];

    /**
     * @var bool
     */
    protected $valid = false;

    /**
     * @var bool
     */
    protected $strict = true;

    /**
     * Get Meta Data.
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * Set Meta Data.
     */
    private function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
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
    public function validate(string $vatNumber): bool
    {
        $pattern = '/^(AT|BE|BG|CHE|CY|CZ|DE|DK|EE|EL|ES|EU|FI|FR|GB|GR|HR|HU|IE|IT|LT|LU|LV|MT|NL|NO|PL|PT|RO|RS|SE|SI|SK)[A-Z0-9]{6,20}$/';

        $vatNumber = strtoupper($vatNumber);

        if (! preg_match($pattern, $vatNumber)) {
            $this->setValid(false);

            return false;
        }

        if (! self::check($vatNumber)) {
            $this->setValid(false);

            return false;
        }

        $vies = new Vies();

        $strict = $this->getStrict();
        $vies->setStrict($strict);

        if (! $vies->validate($vatNumber)) {
            $this->setValid(false);

            return false;
        }

        $metaData = [
            'name' => $vies->getName(),
            'address' => $vies->getAddress(),
        ];

        $this->setMetaData($metaData);

        $this->setValid(true);

        return true;
    }

    /**
     * Check.
     */
    private static function check(string $vatNumber): bool
    {
        $countryCode = substr($vatNumber, 0, 2);

        switch ($countryCode) {
            case 'AT': // Oostenrijk
                return (bool) preg_match('/^(AT)U(\d{8})$/', $vatNumber);
            case 'BE': // België
                return (bool) preg_match('/(BE)(0?\d{9})$/', $vatNumber);
            case 'BG': // Bulgarije
                return (bool) preg_match('/(BG)(\d{9,10})$/', $vatNumber);
            case 'CHE': // Zwitserland
                return (bool) preg_match('/(CHE)(\d{9})(MWST)?$/', $vatNumber);
            case 'CY': // Cyprus
                return (bool) preg_match('/^(CY)([0-5|9]\d{7}[A-Z])$/', $vatNumber);
            case 'CZ': // Tsjechië
                return (bool) preg_match('/^(CZ)(\d{8,10})(\d{3})?$/', $vatNumber);
            case 'DE': // Duitsland
                return (bool) preg_match('/^(DE)([1-9]\d{8})/', $vatNumber);
            case 'DK': // Denemarken
                return (bool) preg_match('/^(DK)(\d{8})$/', $vatNumber);
            case 'EE': // Estland
                return (bool) preg_match('/^(EE)(10\d{7})$/', $vatNumber);
            case 'EL': // Griekenland
                return (bool) preg_match('/^(EL)(\d{9})$/', $vatNumber);
            case 'ES': // Spanje
                return preg_match('/^(ES)([A-Z]\d{8})$/', $vatNumber)
                    || preg_match('/^(ES)([A-H|N-S|W]\d{7}[A-J])$/', $vatNumber)
                    || preg_match('/^(ES)([0-9|Y|Z]\d{7}[A-Z])$/', $vatNumber)
                    || preg_match('/^(ES)([K|L|M|X]\d{7}[A-Z])$/', $vatNumber);
            case 'EU':
                return (bool) preg_match('/^(EU)(\d{9})$/', $vatNumber);
            case 'FI': // Finland
                return (bool) preg_match('/^(FI)(\d{8})$/', $vatNumber);
            case 'FR': // Frankrijk
                return preg_match('/^(FR)(\d{11})$/', $vatNumber)
                    || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]\d{10})$/', $vatNumber)
                    || preg_match('/^(FR)(\d[(A-H)|(J-N)|(P-Z)]\d{9})$/', $vatNumber)
                    || preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]{2}\d{9})$/', $vatNumber);
            case 'GB': // Verenigd Koninkrijk
                return preg_match('/^(GB)?(\d{9})$/', $vatNumber)
                    || preg_match('/^(GB)?(\d{12})$/', $vatNumber)
                    || preg_match('/^(GB)?(GD\d{3})$/', $vatNumber)
                    || preg_match('/^(GB)?(HA\d{3})$/', $vatNumber);
            case 'GR': // Griekenland
                return (bool) preg_match('/^(GR)(\d{8,9})$/', $vatNumber);
            case 'HR': // Kroatië
                return (bool) preg_match('/^(HR)(\d{11})$/', $vatNumber);
            case 'HU': // Hongarije
                return (bool) preg_match('/^(HU)(\d{8})$/', $vatNumber);
            case 'IE': // Ierland
                return preg_match('/^(IE)(\d{7}[A-W])$/', $vatNumber)
                    || preg_match('/^(IE)([7-9][A-Z\*\+)]\d{5}[A-W])$/', $vatNumber)
                    || preg_match('/^(IE)(\d{7}[A-W][AH])$/', $vatNumber);
            case 'IT': // Italië
                return (bool) preg_match('/^(IT)(\d{11})$/', $vatNumber);
            case 'LT': // Litouwen
                return (bool) preg_match('/^(LT)(\d{9}|\d{12})$/', $vatNumber);
            case 'LU': // Luxemburg
                return (bool) preg_match('/^(LU)(\d{8})$/', $vatNumber);
            case 'LV': // Letland
                return (bool) preg_match('/^(LV)(\d{11})$/', $vatNumber);
            case 'MT': // Malta
                return (bool) preg_match('/^(MT)([1-9]\d{7})$/', $vatNumber);
            case 'NL': // Nederland
                return (bool) preg_match('/^(NL)(\d{9})B\d{2}$/', $vatNumber);
            case 'NO': // Norwegen
                return (bool) preg_match('/^(NO)(\d{9})$/', $vatNumber);
            case 'PL': // Polen
                return (bool) preg_match('/^(PL)(\d{10})$/', $vatNumber);
            case 'PT': // Portugal
                return (bool) preg_match('/^(PT)(\d{9})$/', $vatNumber);
            case 'RO': // Roemenië
                return (bool) preg_match('/^(RO)([1-9]\d{1,9})$/', $vatNumber);
            case 'RS': // Servië
                return (bool) preg_match('/^(RS)(\d{9})$/', $vatNumber);
            case 'SE': // Zweden
                return (bool) preg_match('/^(SE)(\d{10}01)$/', $vatNumber);
            case 'SI': // Slovenië
                return (bool) preg_match('/^(SI)([1-9]\d{7})$/', $vatNumber);
            case 'SK': // Slowakije
                return (bool) preg_match('/^(SK)([1-9]\d[(2-4)|(6-9)]\d{7})$/', $vatNumber);
            default:
                return false;
        }
    }
}

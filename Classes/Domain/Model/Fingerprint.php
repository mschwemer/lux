<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use In2code\Lux\Exception\FingerprintMustNotBeEmptyException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Fingerprint
 */
class Fingerprint extends AbstractModel
{
    const TABLE_NAME = 'tx_lux_domain_model_fingerprint';
    const TYPE_FINGERPRINT = 0;
    const TYPE_COOKIE = 1;

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string
     */
    protected $userAgent = '';

    /**
     * @var int
     */
    protected $type = 0;

    /**
     * Fingerprint constructor.
     * @param string $domain
     * @param string $userAgent
     */
    public function __construct(string $domain = '', string $userAgent = '')
    {
        if ($this->domain === '') {
            $this->domain = ($domain !== '' ? $domain : GeneralUtility::getIndpEnv('HTTP_HOST'));
        }
        if ($this->userAgent === '') {
            $this->userAgent = ($userAgent !== '' ? $userAgent : GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     * @throws FingerprintMustNotBeEmptyException
     */
    public function setValue(string $value): self
    {
        if ($value === '') {
            throw new FingerprintMustNotBeEmptyException('Fingerprint is empty', 1585901797);
        }
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return Fingerprint
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Fingerprint
     */
    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeString(): string
    {
        if ($this->getType() === self::TYPE_COOKIE) {
            return 'Cookie';
        } else {
            return 'Fingerprint';
        }
    }

    /**
     * @return array
     */
    public function getPropertiesFromUserAgent(): array
    {
        $properties = [
            'browser' => '',
            'browserversion' => '',
            'os' => '',
            'osversion' => '',
            'manufacturer' => '',
            'type' => ''
        ];
        if (class_exists(\WhichBrowser\Parser::class)) {
            $parser = new \WhichBrowser\Parser($this->getUserAgent());
            $properties = [
                'browser' => $parser->browser->getName(),
                'browserversion' => $parser->browser->version->value,
                'os' => $parser->os->getName(),
                'osversion' => $parser->os->getVersion(),
                'manufacturer' => $parser->device->getManufacturer(),
                'type' => $parser->device->type
            ];
        }
        return $properties;
    }
}

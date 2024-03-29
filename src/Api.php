<?php

declare(strict_types=1);

namespace Yproximite\Payum\Axepta;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use phpseclib3\Crypt\Blowfish;

class Api
{
    public const ENDPOINT_TYPE = 'endpoint_type';

    public const ENDPOINT_PAYSSL   = 'https://paymentpage.axepta.bnpparibas/payssl.aspx';
    public const ENDPOINT_DIRECT   = 'https://paymentpage.axepta.bnpparibas/direct.aspx';
    public const ENDPOINT_DIRECT3D = 'https://paymentpage.axepta.bnpparibas/direct3d.aspx';
    public const ENDPOINT_CAPTURE  = 'https://paymentpage.axepta.bnpparibas/capture.aspx';
    public const ENDPOINT_CREDIT   = 'https://paymentpage.axepta.bnpparibas/credit.aspx';

    public const MSG_VER_DEFAULT = '2.0';

    public const STATUS_OK         = 'OK';
    public const STATUS_AUTHORISED = 'AUTHORIZED';
    public const STATUS_FAILED     = 'FAILED';

    public const OPTIONS_MERCHANT_ID = 'merchant_id';
    public const OPTIONS_HMAC        = 'hmac';
    public const OPTIONS_CRYPT_KEY   = 'crypt_key';

    public const FIELD_VADS_DEBUG       = 'Debug';
    public const FIELD_VADS_PAY_ID      = 'PayID';
    public const FIELD_VADS_TRANS_ID    = 'TransID';
    public const FIELD_VADS_MERCHANT_ID = 'MerchantID';
    public const FIELD_VADS_AMOUNT      = 'Amount';
    public const FIELD_VADS_CURRENCY    = 'Currency';
    public const SHASIGN_FIELD          = 'MAC';

    public const FIELD_VADS_REF_NR          = 'RefNr';
    public const FIELD_VADS_AMOUNT_3D       = 'Amount3D';
    public const FIELD_VADS_URL_SUCCESS     = 'URLSuccess';
    public const FIELD_VADS_URL_FAILURE     = 'URLFailure';
    public const FIELD_VADS_URL_NOTIFY      = 'URLNotify';
    public const FIELD_VADS_URL_BACK        = 'URLBack';
    public const FIELD_VADS_RESPONSE        = 'Response';
    public const FIELD_VADS_USER_DATA       = 'UserData';
    public const FIELD_VADS_CAPTURE         = 'Capture';
    public const FIELD_VADS_ORDER_DESC      = 'OrderDesc';
    public const FIELD_MSG_VER              = 'MsgVer';
    public const FIELD_VADS_REQ_ID          = 'ReqId';
    public const FIELD_VADS_PLAIN           = 'Plain';
    public const FIELD_VADS_CUSTOM          = 'Custom';
    public const FIELD_VADS_EXPIRATION_TIME = 'expirationTime';
    public const FIELD_VADS_ACC_VERIFY      = 'AccVerify';
    // I for initial and R for recurrent
    public const FIELD_VADS_RTF = 'RTF';
    // no if you want disable 3Dsecure for recurrent payment
    public const FIELD_VADS_VBV     = 'Vbv';
    public const FIELD_VADS_CH_DESC = 'ChDesc';

    public const FIELD_LEN  = 'Len';
    public const FIELD_DATA = 'Data';
    public const FIELD_MID  = 'MID';

    public const FIELD_VADS_TEMPLATE   = 'Template';
    public const FIELD_VADS_LANGUAGE   = 'Language';
    public const FIELD_VADS_BACKGROUND = 'Background';
    public const FIELD_VADS_CCSelect   = 'CCSelect';

    public const FIELD_VADS_MID          = 'mid';
    public const FIELD_VADS_REFNR        = 'refnr';
    public const FIELD_VADS_XID          = 'XID';
    public const FIELD_VADS_TRANS_STATUS = 'Status';
    public const FIELD_VADS_DESCRIPTION  = 'Description';
    public const FIELD_VADS_CODE         = 'Code';
    public const FIELD_VADS_PCNR         = 'PCNr';

    public const FIELD_VADS_CCNR = 'CCNr';

    public const FIELD_VADS_CCCVC       = 'CCCVC';
    public const FIELD_VADS_CC_BRAND    = 'CCBrand';
    public const FIELD_VADS_CC_EXPIRY   = 'CCExpiry';
    public const FIELD_VADS_TERM_URL    = 'TermURL';
    public const FIELD_VADS_USER_AGENT  = 'UserAgent';
    public const FIELD_VADS_HTTP_ACCEPT = 'HTTPAccept';
    public const FIELD_VADS_ABO_ID      = 'AboID';
    public const FIELD_VADS_ACSXID      = 'ACSXID';
    public const FIELD_VADS_MASKED_PAN  = 'MaskedPan';
    public const FIELD_VADS_CAVV        = 'CAVV';
    public const FIELD_VADS_ECI         = 'ECI';
    public const FIELD_VADS_DDD         = 'DDD';
    public const FIELD_VADS_TYPE        = 'Type';

    // @see https://docs.axepta.bnpparibas/display/DOCBNP/Payment+page section "How to customize the payment page?"
    // Amount and Currency
    public const FIELD_VADS_CUSTOM_FIELD_1 = 'CustomField1';
    // Order's number
    public const FIELD_VADS_CUSTOM_FIELD_2 = 'CustomField2';
    // Merchant's logo
    public const FIELD_VADS_CUSTOM_FIELD_3 = 'CustomField3';
    // Order's description
    public const FIELD_VADS_CUSTOM_FIELD_4 = 'CustomField4';
    // Buyer's information
    public const FIELD_VADS_CUSTOM_FIELD_5 = 'CustomField5';
    // Shipping information
    public const FIELD_VADS_CUSTOM_FIELD_6 = 'CustomField6';
    // Delivery information
    public const FIELD_VADS_CUSTOM_FIELD_7 = 'CustomField7';
    // Name of a new field added by the merchant
    public const FIELD_VADS_CUSTOM_FIELD_8 = 'CustomField8';
    // Value of a new field added by the merchant
    public const FIELD_VADS_CUSTOM_FIELD_9 = 'CustomField9';

    public const LANGUAGE_NL = 'nl';
    public const LANGUAGE_FR = 'fr';
    public const LANGUAGE_DE = 'de';
    public const LANGUAGE_IT = 'it';
    public const LANGUAGE_ES = 'es';
    public const LANGUAGE_CY = 'cy';
    public const LANGUAGE_EN = 'en';

    private const REQUEST_HMAC_FIELDS = [
        self::FIELD_VADS_PAY_ID,
        self::FIELD_VADS_TRANS_ID,
        self::FIELD_VADS_MERCHANT_ID,
        self::FIELD_VADS_AMOUNT,
        self::FIELD_VADS_CURRENCY,
    ];

    private const RESPONSE_HMAC_FIELDS = [
        self::FIELD_VADS_PAY_ID,
        self::FIELD_VADS_TRANS_ID,
        self::FIELD_VADS_MERCHANT_ID,
        self::FIELD_VADS_TRANS_STATUS,
        self::FIELD_VADS_CODE,
    ];

    private const BLOWFISH_FIELDS = [
        self::FIELD_VADS_PAY_ID,
        self::FIELD_VADS_TRANS_ID,
        self::FIELD_VADS_AMOUNT,
        self::FIELD_VADS_CURRENCY,
        self::SHASIGN_FIELD,
        self::FIELD_VADS_REF_NR,
        self::FIELD_VADS_URL_SUCCESS,
        self::FIELD_VADS_URL_FAILURE,
        self::FIELD_VADS_URL_NOTIFY,
        self::FIELD_VADS_RESPONSE,
        self::FIELD_VADS_USER_DATA,
        self::FIELD_VADS_CAPTURE,
        self::FIELD_VADS_ORDER_DESC,
        self::FIELD_MSG_VER,

        self::FIELD_VADS_PCNR,
        self::FIELD_VADS_CCNR,
        self::FIELD_VADS_CC_EXPIRY,
        self::FIELD_VADS_CC_BRAND,
        self::FIELD_VADS_VBV,
        self::FIELD_VADS_RTF,
        self::FIELD_VADS_REQ_ID,
    ];

    private const REQUIRED_FIELDS = [
        self::FIELD_VADS_MERCHANT_ID,
        self::FIELD_VADS_TRANS_ID,
        self::FIELD_VADS_AMOUNT,
        self::FIELD_VADS_CURRENCY,
        self::FIELD_VADS_ORDER_DESC,
    ];

    private const ALLOWED_LANGUAGES = [
        self::LANGUAGE_NL,
        self::LANGUAGE_FR,
        self::LANGUAGE_DE,
        self::LANGUAGE_IT,
        self::LANGUAGE_ES,
        self::LANGUAGE_CY,
        self::LANGUAGE_EN,
    ];

    private const CUSTOM_FIELDS = [
        self::FIELD_VADS_CUSTOM_FIELD_1,
        self::FIELD_VADS_CUSTOM_FIELD_2,
        self::FIELD_VADS_CUSTOM_FIELD_3,
        self::FIELD_VADS_CUSTOM_FIELD_4,
        self::FIELD_VADS_CUSTOM_FIELD_5,
        self::FIELD_VADS_CUSTOM_FIELD_6,
        self::FIELD_VADS_CUSTOM_FIELD_7,
        self::FIELD_VADS_CUSTOM_FIELD_8,
        self::FIELD_VADS_CUSTOM_FIELD_9,
    ];

    /** @var array<string, mixed> */
    private array               $options = [];
    private HttpClientInterface $client;
    private MessageFactory      $messageFactory;
    private string              $secretKey;
    private string              $cryptKey;
    /** @var array<string, mixed> */
    private array   $parameters = [];
    private ?string $dataString = null;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options                                    = $options;
        $this->secretKey                                  = $options[static::OPTIONS_HMAC];
        $this->cryptKey                                   = $options[static::OPTIONS_CRYPT_KEY];
        $this->parameters[static::FIELD_VADS_MERCHANT_ID] = $options[static::OPTIONS_MERCHANT_ID];
        $this->client                                     = $client;
        $this->messageFactory                             = $messageFactory;
    }

    /**
     * @param array<string, mixed> $details
     */
    public function doPayment(array $details): void
    {
        if (static::ENDPOINT_DIRECT === $this->getOption(static::ENDPOINT_TYPE, $details)) {
            throw new HttpPostRedirect($this->getDirectPayment($details), $details);
        }

        $this->parameters[static::FIELD_VADS_TRANS_ID]    = $this->getOption(static::FIELD_VADS_TRANS_ID, $details);
        $this->parameters[static::FIELD_VADS_AMOUNT]      = $this->getOption(static::FIELD_VADS_AMOUNT, $details);
        $this->parameters[static::FIELD_VADS_CURRENCY]    = $this->getOption(static::FIELD_VADS_CURRENCY, $details);
        $this->parameters[static::FIELD_VADS_REF_NR]      = $this->getOption(static::FIELD_VADS_REF_NR, $details);
        $this->parameters[static::FIELD_VADS_URL_SUCCESS] = $this->getOption(static::FIELD_VADS_URL_SUCCESS, $details);
        $this->parameters[static::FIELD_VADS_URL_FAILURE] = $this->getOption(static::FIELD_VADS_URL_FAILURE, $details);
        $this->parameters[static::FIELD_VADS_URL_NOTIFY]  = $this->getOption(static::FIELD_VADS_URL_NOTIFY, $details);
        $this->parameters[static::FIELD_VADS_URL_BACK]    = $this->getOption(static::FIELD_VADS_URL_BACK, $details);
        $this->parameters[static::FIELD_VADS_RESPONSE]    = $this->getOption(static::FIELD_VADS_RESPONSE, $details);
        $this->parameters[static::FIELD_VADS_LANGUAGE]    = $this->getOption(static::FIELD_VADS_LANGUAGE, $details);
        $this->parameters[static::FIELD_VADS_ORDER_DESC]  = $this->getOption(static::FIELD_VADS_ORDER_DESC, $details);
        $this->parameters[static::FIELD_MSG_VER]          = $this->getOption(static::FIELD_MSG_VER, $details) ?? static::MSG_VER_DEFAULT;

        if (null !== $rtf = $this->getOption(static::FIELD_VADS_RTF, $details)) {
            $this->parameters[static::FIELD_VADS_RTF] = $rtf;
        }

        if (null !== $reqId = $this->getOption(static::FIELD_VADS_REQ_ID, $details)) {
            $this->parameters[static::FIELD_VADS_REQ_ID] = $reqId;
        }

        $this->parameters[static::FIELD_VADS_ORDER_DESC] = $this->getOption(static::FIELD_VADS_ORDER_DESC, $details);
        $this->validate();

        $data = $this->getBfishCrypt();
        $len  = $this->getOption(static::FIELD_LEN, $this->parameters);

        $customFields = [];
        foreach (static::CUSTOM_FIELDS as $customField) {
            if ('' !== $option = trim((string) $this->getOption($customField, $details))) {
                $customFields[$customField] = $option;
            }
        }

        $url = sprintf('%s?MerchantID=%s&Len=%d&Data=%s&URLBack=%s&%s', static::ENDPOINT_PAYSSL, $this->parameters[static::FIELD_VADS_MERCHANT_ID], $len, $data, $this->parameters[static::FIELD_VADS_URL_BACK], http_build_query($customFields));

        throw new HttpPostRedirect($url, $details);
    }

    public function getDirectPayment(array $details): string
    {
        $this->parameters[static::FIELD_VADS_TRANS_ID] = $this->getOption(static::FIELD_VADS_TRANS_ID, $details);
        $this->parameters[static::FIELD_VADS_AMOUNT]   = $this->getOption(static::FIELD_VADS_AMOUNT, $details);
        $this->parameters[static::FIELD_VADS_CURRENCY] = $this->getOption(static::FIELD_VADS_CURRENCY, $details);

        $this->parameters[static::FIELD_VADS_PCNR]      = $this->getOption(static::FIELD_VADS_PCNR, $details);
        $this->parameters[static::FIELD_VADS_CCNR]      = $this->getOption(static::FIELD_VADS_PCNR, $details);
        $this->parameters[static::FIELD_VADS_RTF]       = $this->getOption(static::FIELD_VADS_RTF, $details);
        $this->parameters[static::FIELD_VADS_VBV]       = 'no';
        $this->parameters[static::FIELD_VADS_CC_BRAND]  = $this->getOption(static::FIELD_VADS_CC_BRAND, $details);
        $this->parameters[static::FIELD_VADS_CC_EXPIRY] = $this->getOption(static::FIELD_VADS_CC_EXPIRY, $details);
        $this->parameters[static::FIELD_MSG_VER]        = $this->getOption(static::FIELD_MSG_VER, $details) ?? static::MSG_VER_DEFAULT;

        $data = $this->getBfishCrypt(static::ENDPOINT_DIRECT);
        $len  = $this->getOption(static::FIELD_LEN, $this->parameters);

        return sprintf('%s?MerchantID=%s&Len=%d&Data=%s', static::ENDPOINT_DIRECT, $this->parameters[static::FIELD_VADS_MERCHANT_ID], $len, $data);
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     */
    public function replace(array $response): array
    {
        $this->setResponse($response);

        parse_str((string) $this->dataString, $result);

        return $result;
    }

    /**
     * @param array<string, mixed> $httpRequest
     */
    public function setResponse(array $httpRequest): void
    {
        $this->parameters = $this->filterRequestParameters($httpRequest);
    }

    public function validate(): void
    {
        foreach (static::REQUIRED_FIELDS as $field) {
            if (in_array($this->parameters[$field], [null, '', 0], true)) {
                throw new \RuntimeException($field.' can not be empty');
            }
        }
    }

    public function getShaSign(): string
    {
        $this->validate();

        return $this->shaCompose(static::REQUEST_HMAC_FIELDS);
    }

    public function getBfishCrypt(?string $type = null): string
    {
        // TODO : better validation
        if (static::ENDPOINT_DIRECT !== $type) {
            $this->validate();
        }

        return $this->bfishCompose(static::BLOWFISH_FIELDS);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function shaCompose(array $parameters): string
    {
        // compose SHA string
        $shaString = '';
        foreach ($parameters as $key) {
            if (array_key_exists($key, $this->parameters) && !in_array($this->parameters[$key], ['', null], true)) {
                $value     = $this->parameters[$key];
                $shaString .= $value;
            }
            $shaString .= (array_search($key, $parameters, true) != (count($parameters) - 1)) ? '*' : '';
        }

        $this->parameters[static::SHASIGN_FIELD] = hash_hmac('sha256', $shaString, $this->secretKey);

        return $this->parameters[static::SHASIGN_FIELD];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function bfishCompose(array $parameters): string
    {
        $blowfishString = '';

        foreach ($parameters as $key) {
            if (array_key_exists($key, $this->parameters) && !in_array($this->parameters[$key], ['', null], true)) {
                $value          = $this->parameters[$key];
                $blowfishString .= $key.'='.$value.'&';
            }
        }
        $blowfishString                             = rtrim($blowfishString, '&');
        $this->parameters[static::FIELD_VADS_DEBUG] = $blowfishString;
        $this->parameters[static::FIELD_LEN]        = strlen($blowfishString);
        $this->parameters[self::FIELD_DATA]         = bin2hex($this->encrypt($blowfishString, $this->cryptKey));

        return $this->parameters[self::FIELD_DATA];
    }

    protected function validateUri(string $uri): void
    {
        if (false === filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Uri is not valid');
        }

        if (strlen($uri) > 200) {
            throw new \InvalidArgumentException('Uri is too long');
        }
    }

    /**
     * @param array<string, mixed> $details
     *
     * @return mixed
     */
    protected function getOption(string $name, array $details = [])
    {
        if (array_key_exists($name, $details)) {
            return $details[$name];
        }

        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $httpRequest
     *
     * @return array<string, mixed>
     */
    private function filterRequestParameters(array $httpRequest): array
    {
        $parameters = $this->parameters;
        if (!array_key_exists(static::FIELD_DATA, $httpRequest) || '' == $httpRequest[static::FIELD_DATA]) {
            $parameters[self::FIELD_VADS_DEBUG] = implode('&', $httpRequest);
            foreach ($httpRequest as $key => $value) {
                $key              = ('mid' == $key) ? self::FIELD_VADS_MERCHANT_ID : $key;
                $parameters[$key] = $value;
            }
        } else {
            $parameters[self::FIELD_DATA]       = $httpRequest[self::FIELD_DATA];
            $this->dataString                   = static::decrypt((string) hex2bin($parameters[self::FIELD_DATA]), $this->cryptKey);
            $parameters[self::FIELD_VADS_DEBUG] = $this->dataString;
            $dataParams                         = explode('&', $this->dataString);
            foreach ($dataParams as $dataParamString) {
                $dataKeyValue     = explode('=', $dataParamString, 2);
                $key              = ('mid' == $dataKeyValue[0]) ? self::FIELD_VADS_MERCHANT_ID : $dataKeyValue[0];
                $parameters[$key] = $dataKeyValue[1];
            }
        }

        return $parameters;
    }

    private function encrypt(string $data, string $key): string
    {
        $l = strlen($key);
        if ($l < 16) {
            $key = str_repeat($key, (int) ceil(16 / $l));
        }

        if (($m = strlen($data) % 8) > 0) {
            $data .= str_repeat("\x00", 8 - $m);
        }

        if (!in_array('bf-ecb', openssl_get_cipher_methods(), true)) {
            $blowfish = new Blowfish('ecb');
            $blowfish->setKey($key);

            return $blowfish->encrypt($data);
        }

        $val = openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);

        return (string) $val;
    }

    public static function decrypt(string $data, string $key): string
    {
        $l = strlen($key);
        if ($l < 16) {
            $key = str_repeat($key, (int) ceil(16 / $l));
        }

        if (!in_array('bf-ecb', openssl_get_cipher_methods(), true)) {
            $blowfish = new Blowfish('ecb');
            $blowfish->setKey($key);
            $blowfish->disablePadding();

            return $blowfish->decrypt($data);
        }

        $val = openssl_decrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);

        return rtrim((string) $val, "\0");
    }
}

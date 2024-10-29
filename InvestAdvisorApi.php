<?php


/**
 * Usage example:
 * ```php
 * $iaApi    = new InvestAdvisorApi();
 * $response = $iaApi->push('secret_token', InvestAdvisorApi::dataSample())
 * ```
 */
class InvestAdvisorApi {
    public $domain   = 'new.doctorback.net';
    public $isEnvDev = false;
    public $isDebug  = false;

    /**
     * @param string $token
     * @param array  $data  {@see self::dataSample()}
     *
     * @return string
     *
     * @link https://github.com/adsirio/invest-advisor-docs/wiki/API:-create-lead API docs
     */
    public function push($token, $data)
    {
        if (empty($data['Lead'])) {
            $data['Lead'] = $data;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => "https://$this->domain/api/royal?token=$token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 100,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        ));

        if (!$this->isEnvDev) {
            curl_setopt($curl, CURLOPT_CAPATH, '/etc/ssl/certs');
        }

        $response = curl_exec($curl);
        $inf      = curl_getinfo($curl);

        if ($this->isDebug || curl_error($curl)) {
            $log = var_export([
                'curl_exec()'    => $response,
                'curl_errno()'   => curl_errno($curl),
                'curl_error()'   => curl_error($curl),
                'curl_getinfo()' => $inf,
            ], true);

            if ($this->isEnvDev) {
                throw new \Exception($log);
            } else {
                error_log($log);
            }
        }

        curl_close($curl);

        return $response;
    }

    public static function dataSample()
    {
        return [
            'Lead' => [
                // required
                'firstname'           => 'Test FN',
                'lastname'            => 'Test LN',
                'country'             => 'DE',
                'ip'                  => '46.189.72.' . rand(0, 255),
                'phoneNumber'         => '+4917' . sprintf('%08d', rand(0, 9)),
                'email'               => 'test_' . rand() . '@gmail.com',
                'offer_rule_offer_id' => 392,               //392 test value
                'funnel_url'          => 'test F url',
                'funnel_name'         => 'test F name',

                // optional
                'test'     => 1,
                'ua'       => '', // The User-Agent header from the user's request
                //Offer Info. Description of the funnel and user flow.
                // Example: The client has registered by this link after he saw a Banner/Ad on some site/Pop up/Blog,
                // which promoted a make money concept. Or the offer name, minimum first deposit amount, etc.
                'oi'       => '',
                'c'        => '', //Any comment that might help the broker. Example: Married, sex, etc.
                'so'       => '', //Funnel ID
                'click_id' => '', //your unique ID
            ],
        ];
    }
}

<?php
/**
 * 2007-2014 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 *Licensed under the Apache License, Version 2.0 (the "License");
 *you may not use this file except in compliance with the License.
 *You may obtain a copy of the License at
 *
 *http://www.apache.org/licenses/LICENSE-2.0
 *
 *Unless required by applicable law or agreed to in writing, software
 *distributed under the License is distributed on an "AS IS" BASIS,
 *WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *See the License for the specific language governing permissions and
 *limitations under the License.
 *
 *  @author    PagSeguro Internet Ltda.
 *  @copyright 2007-2014 PagSeguro Internet Ltda.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */

class PagSeguroConfigWrapper
{

    /**
     * production or sandbox
     */
    const PAGSEGURO_ENV = "production";
    /**
     *
     */
    const PAGSEGURO_EMAIL = "vendas@valepratas.com.br";
    /**
     *
     */
    const PAGSEGURO_TOKEN_PRODUCTION = "3716BE6F450A4C5DA318";
    /**
     *
     */
    const PAGSEGURO_TOKEN_SANDBOX = "";
    /**
     *
     */
    const PAGSEGURO_APP_ID_PRODUCTION = "";
    /**
     *
     */
    const PAGSEGURO_APP_ID_SANDBOX = "";
    /**
     *
     */
    const PAGSEGURO_APP_KEY_PRODUCTION = "";
    /**
     *
     */
    const PAGSEGURO_APP_KEY_SANDBOX = "";
    /**
     * UTF-8, ISO-8859-1
     */
    const PAGSEGURO_CHARSET = "UTF-8";
    /**
     *
     */
    const PAGSEGURO_LOG_ACTIVE = true;
    /**
     * Informe o path completo (relativo ao path da lib) para o arquivo, ex.: ../PagSeguroLibrary/logs.txt
     */
    const PAGSEGURO_LOG_LOCATION = "";

    /**
     * @return array
     */
    public static function getConfig()
    {
        $PagSeguroConfig = array();

        $PagSeguroConfig = array_merge_recursive(
            self::getEnvironment(),
            self::getCredentials(),
            self::getApplicationEncoding(),
            self::getLogConfig()
        );

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getEnvironment()
    {
        $PagSeguroConfig['environment'] = "production";

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getCredentials()
    {
        $PagSeguroConfig['credentials'] = array();
        $PagSeguroConfig['credentials']['email'] = self::PAGSEGURO_EMAIL;
        $PagSeguroConfig['credentials']['token']['production'] = self::PAGSEGURO_TOKEN_PRODUCTION;
        $PagSeguroConfig['credentials']['token']['sandbox'] = self::PAGSEGURO_TOKEN_SANDBOX;
        $PagSeguroConfig['credentials']['appId']['production'] = self::PAGSEGURO_APP_ID_PRODUCTION;
        $PagSeguroConfig['credentials']['appId']['sandbox'] = self::PAGSEGURO_APP_ID_SANDBOX;
        $PagSeguroConfig['credentials']['appKey']['production'] = self::PAGSEGURO_APP_KEY_PRODUCTION;
        $PagSeguroConfig['credentials']['appKey']['sandbox'] = self::PAGSEGURO_APP_KEY_SANDBOX;
        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getApplicationEncoding()
    {
        $PagSeguroConfig['application'] = array();
        $PagSeguroConfig['application']['charset'] = self::PAGSEGURO_CHARSET;

        return $PagSeguroConfig;
    }

    /**
     * @return mixed
     */
    private static function getLogConfig()
    {
        $PagSeguroConfig['log'] = array();
        $PagSeguroConfig['log']['active'] = self::PAGSEGURO_LOG_ACTIVE;
        $PagSeguroConfig['log']['fileLocation'] = self::PAGSEGURO_LOG_LOCATION;

        return $PagSeguroConfig;
    }
}

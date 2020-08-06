<?php

namespace App\Utils;

class SocialLogin
{
    protected static $instance;

    /**
     * @return SocialLogin
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new SocialLogin();
        }
        return static::$instance;
    }

    protected $enabled;
    protected $allowedEmailDomains;
    protected $deniedEmailDomains;

    private function __construct()
    {
        $this->enabled = ConfigHelper::get('social_login.enabled');
        $this->allowedEmailDomains = ConfigHelper::get('social_login.email_domain.allowed');
        $this->deniedEmailDomains = ConfigHelper::get('social_login.email_domain.denied');
    }

    public function enabled()
    {
        return $this->enabled;
    }

    public function allowedEmailDomains()
    {
        return $this->allowedEmailDomains;
    }

    public function deniedEmailDomains()
    {
        return $this->deniedEmailDomains;
    }

    public function checkEmailDomain($email)
    {
        $emailDomain = strtolower(explode('@', $email)[1]);
        return !((!empty($this->allowedEmailDomains) && !in_array($emailDomain, $this->allowedEmailDomains))
            || (!empty($this->deniedEmailDomains) && in_array($emailDomain, $this->deniedEmailDomains)));
    }
}

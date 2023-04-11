<?php

// Maybe also implement: https://github.com/serbanghita/Mobile-Detect

function browser($userAgent)
{
    $agent = new \Jenssegers\Agent\Agent();
    $agent->setUserAgent($userAgent);

    $result = new \WhichBrowser\Parser($userAgent);

    $browser = new \Browser($userAgent);

    return new class($agent, $result, $browser)
    {
        public function __construct($agent, $result, $browser)
        {
            $this->agent = $agent;
            $this->result = $result;
            $this->browser = $browser;
        }

        public function device()
        {
            return $this->result->device->manufacturer ?: $this->browser->getPlatform() ?: $this->agent->device();
        }

        public function deviceVersion()
        {
            return $this->result->device->model;
        }

        public function browser()
        {
            return $this->agent->browser()
                ?: $this->result->browser->name
                ?: $this->browser->getBrowser();
        }

        public function browserVersion()
        {
            return $this->agent->version($this->agent->browser())
                ?? $this->result->browser->version->value
                ?? $this->browser->getVersion()
                ?? null;
        }

        public function browserVersionShort()
        {
            $version = explode(".", $this->browserVersion());
            return reset($version) ?? "";
        }

        public function platform()
        {
            return $this->agent->platform()
                ?: $this->result->os->name
                ?: $this->browser->getPlatform();
        }

        public function platformVersion()
        {
            return $this->agent->version($this->agent->platform())
                ?: $this->result->os->version->value
                ?? null;
        }

        public function isMobile()
        {
            return $this->agent->isPhone()
                ?: $this->result->isType('mobile')
                ?: $this->browser->isMobile();
        }

        public function isTablet()
        {
            return $this->result->isType('tablet')
                ?: $this->browser->isTablet();
        }

        public function isDesktop()
        {
            return $this->agent->isDesktop()
                ?: $this->result->isType('desktop')
                ?: (! $this->browser->isMobile() && ! $this->browser->isTablet());
        }

        public function isRobot()
        {
            return $this->agent->isRobot()
                ?: $this->browser->isRobot();
        }
    };
}

function geoIp($ip, $raw = false)
{
    $data = [
        'country' => null,
        'state' => null,
        'city' => null,
        'postal' => null,
        'timezone' => null,
    ];

    try {
        $reader = new GeoIp2\Database\Reader(resource_path('maxmind/GeoLite2-City.mmdb'));
        $record = $reader->city($ip);

        $data['country'] = $record->country->isoCode;
        $data['state'] = $record->mostSpecificSubdivision->isoCode;
        $data['city'] = $record->city->name;
        $data['postal'] = $record->postal->code;
        $data['timezone'] = $record->location->timeZone;

    } catch (\Exception $e) {
        return $raw ? null : $data;
    }

    return $raw ? $record : $data;
}

<?php


namespace App;


use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website as WebsiteAlias;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Traits\UsesSystemConnection;

class Website extends WebsiteAlias
{
    use UsesSystemConnection;

    protected $fillable = [
        'store_name',
    ];

    public function addUpdateUrls()
    {
        $hostnames = $this->hostnames()->get();

        $urls = explode(',', request()->get('urls'));
        $urls = array_map(function ($url) {
            return trim($url);
        }, $urls);

        $hostnames->whereNotIn('fqdn', $urls)->each(function ($host) {
            $host->forceDelete();
        });

        foreach ($urls as $url) {
            if ($this->validDomain($url) && $hostnames->where('fqdn', $url)->isEmpty()) {
                $this->createHostname($url, $this);
            }
        }
    }

    private function createHostname($fqdn, $website): void
    {
        $hostname_repository = app(HostnameRepository::class);

        $hostname = $hostname_repository->findByHostname($fqdn);
        if ($hostname instanceof Hostname) {
            throw new \Exception('Hostname could not be created. The hostname with a FQDN of ' . $hostname->fqdn . ' already exists.');
        }

        $hostname = new Hostname;
        $hostname->fqdn = $fqdn;
        $hostname = app(HostnameRepository::class)->create($hostname);
        app(HostnameRepository::class)->attach($hostname, $website);
    }

    private function validDomain($url)
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $url);
    }
}

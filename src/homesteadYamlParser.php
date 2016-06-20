<?php

namespace HostsManager;

use Symfony\Component\Yaml\Yaml;

class homesteadYamlParser 
{

    protected $pattern = '/(## ------------------- ##)(.*?)(## ------------------- ##)/s';

    protected $folders = [];

    protected $boxes = [];

    // $boxes = getBoxes($boxes);

    // $sites = getSites($boxes);

    // $hosts = getHosts($boxes);

    public function __construct($folders = array())
    {
        if (!is_array($folders)) {
            $folders = array($folders);
        }
        $this->folders = $folders;

        $this->getBoxes();
    }

    public function pattern()
    {
        return $this->pattern;
    }

    protected function parseYaml($folder) 
    {
        $file = "{$folder}/Homestead.yaml";
        return Yaml::parse(file_get_contents($file));
    }

    protected function getBoxes() 
    {
        foreach ($this->folders as $key => $folder) {
            $data = $this->parseYaml($folder);
            $data['folder'] = $folder;
            $boxes[$key] = $data;
        }
        $this->boxes = $boxes;

        return $this;
    }

    public function getSites($boxes) {

        $sites = [];
        foreach ($boxes as $key => $box) {
            foreach ($box['sites'] as $site) {
                $v = $box['folder'];
                $sites[$key][$site['map']]['base'] = 'http://'.$site['map'];
                $sites[$key][$site['map']]['xip.io'] = false;
                if (lanIP($v)) {
                    $sites[$key][$site['map']]['xip.io'] = 'http://'.$site['map'].'.'.lanIP($v).'.xip.io';
                }
            }
            // add mailcatcher
            $sites[$key]['mailcatcher']['base'] = "http://{$box['ip']}:1080";
            $sites[$key]['mailcatcher']['xip.io'] = "http://{$box['ip']}:1080";

            // add phpmyadmin
            $sites[$key]['phpmyadmin']['base'] = "http://{$box['domain']}/phpmyadmin/";
            $sites[$key]['phpmyadmin']['xip.io'] = "http://{$box['domain']}/phpmyadmin/";
            
        }
        $this->sites = $sites;
        return $this;
    }

    public function getHosts() 
    {
        $now = date('Y.m.d h:i:s');
        $boxes = $this->boxes;
        $hosts = [];
        $hosts[] = "## ------------------- ##";
        $hosts[] = "## LMO HOMESTEAD BOXES ##";
        $hosts[] = "##\n## {$now} ##\n##";
        foreach ($boxes as $key => $box) {
            $sites = [];
            foreach ($box['sites'] as $site) {
                $sites[] = $site['map'];
            }
            $hosts[] = $box['ip'] . ' ' . implode(' ', $sites);
        }
        $hosts[] = "##\n## ------------------- ##";
        return implode("\n", $hosts);
    }




}
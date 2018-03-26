<?php

require_once BLEND_COMPARE_DIRECTORY.'SiteExampleMigrationSeed.php';
class CompareSiteData extends SiteExampleMigrationSeed {
    public function getSiteData()
    {
        return $this->site_data;
    }
}

require_once $this->blender->getMigrationPath().'m2018_01_10_093000_SiteExample.php';
class GeneratedSiteData extends m2018_01_10_093000_SiteExample {
    public function getSiteData()
    {
        return $this->site_data;
    }
}

$compare = new CompareSiteData($this->modx, $this->blender);
$generated = new GeneratedSiteData($this->modx, $this->blender);

return [
    'compare' => $compare->getSiteData(),
    'generated' => $generated->getSiteData()
];
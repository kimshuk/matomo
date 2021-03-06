<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Referrers;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\SiteUrls;

/**
 * @see plugins/Referrers/functions.php
 */
require_once PIWIK_INCLUDE_PATH . '/plugins/Referrers/functions.php';

/**
 */
class Referrers extends \Piwik\Plugin
{
    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'Insights.addReportToOverview'      => 'addReportToInsightsOverview',
            'Request.getRenamedModuleAndAction' => 'renameDeprecatedModuleAndAction',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'Tracker.setTrackerCacheGeneral'    => 'setTrackerCacheGeneral',
            'AssetManager.getJavaScriptFiles'   => 'getJsFiles',
            'AssetManager.getStylesheetFiles'   => 'getStylesheetFiles',
        );
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = 'plugins/Referrers/angularjs/campaign-builder/campaign-builder.directive.less';
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'General_Required2';
        $translationKeys[] = 'General_Clear';
        $translationKeys[] = 'Actions_ColumnPageURL';
        $translationKeys[] = 'CoreAdminHome_JSTracking_CampaignNameParam';
        $translationKeys[] = 'CoreAdminHome_JSTracking_CampaignKwdParam';
        $translationKeys[] = 'Referrers_CampaignSource';
        $translationKeys[] = 'Referrers_CampaignSourceHelp';
        $translationKeys[] = 'Referrers_CampaignContent';
        $translationKeys[] = 'Referrers_CampaignContentHelp';
        $translationKeys[] = 'Referrers_CampaignMedium';
        $translationKeys[] = 'Referrers_CampaignMediumHelp';
        $translationKeys[] = 'Referrers_CampaignPageUrlHelp';
        $translationKeys[] = 'Referrers_CampaignNameHelp';
        $translationKeys[] = 'Referrers_CampaignKeywordHelp';
        $translationKeys[] = 'Referrers_URLCampaignBuilderResult';
        $translationKeys[] = 'Referrers_GenerateUrl';
        $translationKeys[] = 'Goals_Optional';
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'plugins/Referrers/angularjs/campaign-builder/campaign-builder.controller.js';
        $jsFiles[] = 'plugins/Referrers/angularjs/campaign-builder/campaign-builder.directive.js';
    }

    public function setTrackerCacheGeneral(&$cacheContent)
    {
        $siteUrls = new SiteUrls();
        $urls = $siteUrls->getAllCachedSiteUrls();

        return $cacheContent['allUrlsByHostAndIdSite'] = $siteUrls->groupUrlsByHost($urls);
    }

    public function renameDeprecatedModuleAndAction(&$module, &$action)
    {
        if($module == 'Referers') {
            $module = 'Referrers';
        }
    }

    public function addReportToInsightsOverview(&$reports)
    {
        $reports['Referrers_getWebsites']  = array();
        $reports['Referrers_getCampaigns'] = array();
        $reports['Referrers_getSocials']   = array();
        $reports['Referrers_getSearchEngines'] = array();
    }

    /**
     * DataTable filter callback that returns the HTML prefix for a label in the
     * 'getAll' report based on the row's referrer type.
     *
     * @param int $referrerType The referrer type.
     * @return string
     */
    public function setGetAllHtmlPrefix($referrerType)
    {
        // get singular label for referrer type
        $indexTranslation = '';
        switch ($referrerType) {
            case Common::REFERRER_TYPE_DIRECT_ENTRY:
                $indexTranslation = 'Referrers_DirectEntry';
                break;
            case Common::REFERRER_TYPE_SEARCH_ENGINE:
                $indexTranslation = 'General_ColumnKeyword';
                break;
            case Common::REFERRER_TYPE_SOCIAL_NETWORK:
                $indexTranslation = 'Referrers_ColumnSocial';
                break;
            case Common::REFERRER_TYPE_WEBSITE:
                $indexTranslation = 'Referrers_ColumnWebsite';
                break;
            case Common::REFERRER_TYPE_CAMPAIGN:
                $indexTranslation = 'Referrers_ColumnCampaign';
                break;
            default:
                // case of newsletter, partners, before Piwik 0.2.25
                $indexTranslation = 'General_Others';
                break;
        }

        $label = strtolower(Piwik::translate($indexTranslation));

        // return html that displays it as grey & italic
        return '<span class="datatable-label-category">(' . $label . ')</span>';
    }
}

<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS 
 * Sample Module Admin
 */
class Admin extends BaseAdminController {

    public $modules_path = "./application/modules/";
    public $templates_path = "./templates/";
    public $main_path = "./application/language/main/";
    public $langs = array();
    public $langs_templates = array();
    public $langs_main = array();
    public $parsed_langs = array();
    public $paths = array();

    public function __construct() {
        parent::__construct();
        require 'lib/poparser.php';
//        var_dumps(extension_loaded('curl'));
    }

    /**
     * Get module settings
     * @return array
     */
    public function getSettings() {
        $settings = $this->db->select('settings')
                ->where('identif', 'translator')
                ->get('components')
                ->row_array();
        $settings = unserialize($settings['settings']);
        return $settings;
    }

    /**
     * Save settings
     * @param array $settings
     * @return boolean
     */
    public function setSettings($settings) {
        if ($this->input->post('originsLang')) {
            $settings = array('originsLang' => $this->input->post('originsLang'));
        }
        return $this->db->where('identif', 'translator')
                        ->update('components', array('settings' => serialize($settings)
        ));
    }

    /**
     * return set of locales 
     * @return array - locales
     */
    function isLocale($lang) {
        $langs = array(
            'af_ZA', 'am_ET', 'ar_AE',
            'ar_BH', 'ar_DZ', 'ar_EG',
            'ar_IQ', 'ar_JO', 'ar_KW',
            'ar_LB', 'ar_LY', 'ar_MA',
            'ar_OM', 'ar_QA', 'ar_SA',
            'ar_SY', 'ar_TN', 'ar_YE',
            'as_IN', 'ba_RU', 'be_BY',
            'bg_BG', 'bn_BD', 'bn_IN',
            'bo_CN', 'br_FR', 'ca_ES',
            'co_FR', 'cs_CZ', 'cy_GB',
            'da_DK', 'de_AT', 'de_CH',
            'de_DE', 'de_LI', 'de_LU',
            'dv_MV', 'el_GR', 'en_AU',
            'en_BZ', 'en_CA', 'en_GB',
            'en_IE', 'en_IN', 'en_JM',
            'en_MY', 'en_NZ', 'en_PH',
            'en_SG', 'en_TT', 'en_US',
            'en_ZA', 'en_ZW', 'es_AR',
            'es_BO', 'es_CL', 'es_CO',
            'es_CR', 'es_DO', 'es_EC',
            'es_ES', 'es_GT', 'es_HN',
            'es_MX', 'es_NI', 'es_PA',
            'es_PE', 'es_PR', 'es_PY',
            'es_SV', 'es_US', 'es_UY',
            'es_VE', 'et_EE', 'eu_ES',
            'fa_IR', 'fi_FI', 'fo_FO',
            'fr_BE', 'fr_CA', 'fr_CH',
            'fr_FR', 'fr_LU', 'fr_MC',
            'fy_NL', 'ga_IE', 'gd_GB',
            'gl_ES', 'gu_IN', 'he_IL',
            'hi_IN', 'hr_BA', 'hr_HR',
            'hu_HU', 'hy_AM', 'id_ID',
            'ig_NG', 'ii_CN', 'is_IS',
            'it_CH', 'it_IT', 'ja_JP',
            'ka_GE', 'kk_KZ', 'kl_GL',
            'km_KH', 'kn_IN', 'ko_KR',
            'ky_KG', 'lb_LU', 'lo_LA',
            'lt_LT', 'lv_LV', 'mi_NZ',
            'mk_MK', 'ml_IN', 'mn_MN',
            'mr_IN', 'ms_BN', 'ms_MY',
            'mt_MT', 'nb_NO', 'ne_NP',
            'nl_BE', 'nl_NL', 'nn_NO',
            'oc_FR', 'or_IN', 'pa_IN',
            'pl_PL', 'ps_AF', 'pt_BR',
            'pt_PT', 'ro_RO', 'ru_RU',
            'rw_RW', 'sa_IN', 'se_FI',
            'se_NO', 'se_SE', 'si_LK',
            'sk_SK', 'sl_SI', 'sq_AL',
            'sv_FI', 'sv_SE', 'sw_KE',
            'ta_IN', 'te_IN', 'th_TH',
            'tk_TM', 'tn_ZA', 'tr_TR',
            'tt_RU', 'ug_CN', 'uk_UA',
            'ur_PK', 'vi_VN', 'wo_SN',
            'xh_ZA', 'yo_NG', 'zh_CN',
            'zh_HK', 'zh_MO', 'zh_SG',
            'zh_TW', 'zu_ZA'
        );

        return in_array($lang, $langs);
    }

    public function index() {
        $offset = $this->session->userdata('offset');
        $limit = $this->session->userdata('limit');
        $translation = $this->session->userdata('translation');
        $po_table = '';
        if ($translation && $translation['name'] && $translation['type'] && $translation['lang']) {
            $name = $translation['name'];
            $type = $translation['type'];
            $lang = $translation['lang'];
            $po_table = $this->renderModulePoFile($name, $type, $lang, $offset, $limit);
            switch ($type) {
                case 'modules':
                    $this->langs = $this->session->userdata('langs');
                    break;
                case 'templates':
                    $this->langs = $this->session->userdata('langs_templates');
                    break;
                case 'main':
                    $this->langs = $this->session->userdata('langs_main');
                    break;
            }
        } else {
            $this->scanLangFiles();
        }
        $locales_unique = array();
        $locales = $this->config->item('locales');
        foreach ($locales as $locale) {
            $locales_unique[preg_replace("/_[A-Z]+/", '', $locale)] = preg_replace("/_[A-Z]+/", '', $locale);
        }

        \CMSFactory\assetManager::create()
                ->registerScript('admin')
                ->registerStyle('admin')
                ->setData('langs', $this->langs)
                ->setData('settings', $this->getSettings())
                ->setData('locales', $locales_unique)
                ->renderAdmin('list');
        if ($translation) {
            switch ($type) {
                case 'modules':
                    $names = $this->renderModulesNames($lang);
                    break;
                case 'templates':
                    $names = $this->renderTemplatesNames($lang);
                    break;
            }

            jsCode("
            document.getElementById('po_table').style.display = 'table';
            document.getElementsByTagName('tbody')[0].innerHTML = '" . trim(preg_replace('/\s\s+/', ' ', $po_table)) . "';
            document.getElementsByClassName('" . $lang . "')[0].selected = true;
            document.getElementById('types').style.display = 'inline-block';
            document.getElementsByClassName('" . $type . "')[0].selected = true;
            if('" . $type . "'!='main'){
                document.getElementById('modules_templates').style.display = 'inline-block';
                document.getElementById('modules_templates').innerHTML = '" . trim(preg_replace('/\s\s+/', ' ', $names)) . "';
            }
            document.getElementsByClassName('" . $name . "')[0].selected = true;
            document.getElementsByClassName('per_page" . $limit . "')[0].selected = true;
            document.getElementById('per_page').style.display = 'inline-block';
            var paths = $('.pathHolderClone').html();
            $('.pathHolder').html(paths);
            $('.pathParseHolder').show();
            "
            );
        }
    }

    public function createFile($module_template = 'translator', $type = "modules", $lang = 'ru_RU') {
//        switch ($type) {
//            case 'modules':
//                $po = file_put_contents($this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/' . $module_template . '.po', 'ddd');
//                break;
//            case 'templates':
//                $po = file_put_contents($this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/' . $module_template . '.po', 'gggg');
//                break;
//            case 'main':
//                $po = file_put_contents($this->main_path . $lang . '/LC_MESSAGES/' . $module_template . '.po', 'fff');
//                break;
//        }

        if ($_POST) {
            $lang = $this->input->post('locale');
            $type = $this->input->post('type');
            $module_template = $this->input->post('module_template');
            $projectName = $this->input->post('projectName');
            $translatorEmail = $this->input->post('translatorEmail');
            $translatorName = $this->input->post('translatorName');
            $langaugeTeamName = $this->input->post('langaugeTeamName');
            $langaugeTeamEmail = $this->input->post('langaugeTeamEmail');
            $basepath = $this->input->post('basepath');
            $paths = $this->input->post('paths');
            $language = $this->input->post('language');
            $country = $this->input->post('country');

            if ($module_template && $type && $lang) {

                switch ($type) {
                    case 'modules':
                        $url = $this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/' . $module_template . '.po';
                        break;
                    case 'templates':
                        $url = $this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/' . $module_template . '.po';
                        break;
                    case 'main':
                        $url = $this->main_path . $lang . '/LC_MESSAGES/' . $type . '.po';
                        break;
                }

//                $handle = @fopen($url, "wb");
//                fwrite($handle, b"\xEF\xBB\xBF");
//                if ($handle !== false) {
//                    
//                }
                
//                "Project-Id-Version: ImageCms\n"
//                "Report-Msgid-Bugs-To: \n"
//                "POT-Creation-Date: 2013-10-16 15:55+0300\n"
//                "PO-Revision-Date: 2013-11-01 10:37+0300\n"
//                "Last-Translator: Marko <sheme4ko@mail.ru>\n"
//                "Language-Team: Imagecms Team <Imagecms@mail.ru>\n"
//                "Language: ru_UA\n"
//                "MIME-Version: 1.0\n"
//                "Content-Type: text/plain; charset=UTF-8\n"
//                "Content-Transfer-Encoding: 8bit\n"
//                "X-Poedit-KeywordsList: _;gettext;gettext_noop;lang\n"
//                "X-Poedit-Basepath: ../../..\n"
//                "X-Poedit-SourceCharset: utf-8\n"
//                "X-Generator: Poedit 1.5.7\n"
//                "X-Poedit-Language: Russian\n"
//                "X-Poedit-Country: UKRAINE\n"
//                "X-Poedit-SearchPath-0: .\n"

                $content = b"\xEF\xBB\xBF";
                $content .='"Project-Id-Version: ' . $projectName . '\n"' . "\r\n";
                $content .='"Report-Msgid-Bugs-To: \n"' . "\r\n";
                $content .='"POT-Creation-Date: ' . date('Y-m-d h:iO', time()) . '\n"' . "\r\n";
                $content .='"PO-Revision-Date: ' . date('Y-m-d h:iO', time()) . '\n"' . "\r\n";
                $content .='"Last-Translator: ' . $translatorName. ' <' . $translatorEmail . '>\n"' . "\r\n";
                $content .='"Language-Team: ' . $langaugeTeamName. ' <' . $langaugeTeamEmail . '>\n"' . "\r\n";
                $content .='"Language: ' . $lang . '\n"' . "\r\n";
                $content .='"MIME-Version: 1.0\n"' . "\r\n";
                $content .='"Content-Type: text/plain; charset=UTF-8\n"' . "\r\n";
                $content .='"Content-Transfer-Encoding: 8bit\n"' . "\r\n";
                $content .='"X-Poedit-KeywordsList: _;gettext;gettext_noop;lang\n"' . "\r\n";
                $content .='"X-Poedit-Basepath: ' . $basepath . '\n"' . "\r\n";
                $content .='"X-Poedit-SourceCharset: utf-8\n"' . "\r\n";
                $content .='"X-Generator: Poedit 1.5.7\n"' . "\r\n";
                $content .='"X-Poedit-Language: ' . $language . '\n"' . "\r\n";
                $content .='"X-Poedit-Country: ' . $country . '\n"' . "\r\n";
                var_dumps($paths);
//                foreach ($paths as $number => $path){
//                    $content .='"X-Poedit-SearchPath-' . $number . ': ' . $path . '\n"' . "\r\n";
//                }
//                
//                return file_put_contents($url, $content);
                
                
                
                
                
            }
        } else {
            $url = '';
            $file_name = '';

            if ($module_template && $type && $lang) {
                switch ($type) {
                    case 'modules':
                        $url = $this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/';
                        $file_name = $module_template;
                        break;
                    case 'templates':
                        $url = $this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/';
                        $file_name = $module_template;
                        break;
                    case 'main':
                        $url = $this->main_path . $lang . '/LC_MESSAGES/';
                        $file_name = $type;
                        break;
                }
            }

            $this->scanLangFiles();

            \CMSFactory\assetManager::create()
                    ->registerScript('admin')
                    ->registerStyle('admin')
                    ->setData('url', $url)
                    ->setData('langs', $this->langs)
                    ->setData('file_name', $file_name)
                    ->renderAdmin('create');

            if ($module_template && $type && $lang) {
                switch ($type) {
                    case 'modules':
                        $names = $this->renderModulesNames($lang);
                        break;
                    case 'templates':
                        $names = $this->renderTemplatesNames($lang);
                        break;
                }
                jsCode(
                        "
                document.getElementsByClassName('" . $lang . "')[0].selected = true;
                document.getElementById('types').style.display = 'inline-block';
                document.getElementsByClassName('" . $type . "')[0].selected = true;
                if('" . $type . "'!='main'){
                    document.getElementById('modules_templates').style.display = 'inline-block';
                    document.getElementById('modules_templates').innerHTML = '" . trim(preg_replace('/\s\s+/', ' ', $names)) . "';
                }
                document.getElementsByClassName('" . $file_name . "')[0].selected = true;
            "
                );
            }
        }
    }

    public function scanLangFiles() {
        $files = scandir($this->modules_path);
        foreach ($files as $module) {
            if ($module && $module != '.' && $module != '..' && $module[0] != '.') {
                if (is_dir($this->modules_path . $module . '/language')) {
                    $langs = scandir($this->modules_path . $module . '/language');
                    foreach ($langs as $lang) {
                        if ($lang && $lang != '.' && $lang != '..' && $lang[0] != '.' && is_dir($this->modules_path . $module . '/language/' . $lang) && $this->isLocale($lang)
                        ) {
                            $this->langs[$lang][] = array('module' => $module);
                        }
                    }
                }
            }
        }

        if (is_dir($this->templates_path)) {
            $templates = scandir($this->templates_path);
            foreach ($templates as $template) {
                if (is_dir($this->templates_path . $template) && $template != "." && $template != '..' && $template[0] != '.') {
                    if (is_dir($this->templates_path . $template . '/language/' . $template)) {
                        $langs = scandir($this->templates_path . $template . '/language/' . $template);
                        foreach ($langs as $lang) {
                            if ($lang && $lang != '.' && $lang != '..' && $lang[0] != '.' && is_dir($this->templates_path . $template . '/language/' . $template . '/' . $lang) && $this->isLocale($lang)
                            ) {
                                $this->langs_templates[$lang][] = array('template' => $template);
                            }
                        }
                    }
                }
            }
        }

        if (is_dir($this->main_path)) {
            $langs = scandir($this->main_path);
            foreach ($langs as $lang) {
                if ($lang && $lang != '.' && $lang != '..' && $lang[0] != '.' && is_dir($this->main_path . $lang) && $this->isLocale($lang)
                ) {
                    $this->langs_main[$lang][] = array('main' => 'main');
                }
            }
        }

        foreach ($this->langs_templates as $key => $data) {
            if (!$this->langs[$key]) {
                $this->langs[$key] = array();
            }
        }

        foreach ($this->langs_main as $key => $data) {
            if (!$this->langs[$key]) {
                $this->langs[$key] = array();
            }
        }

        $this->session->set_userdata('langs', $this->langs);
        $this->session->set_userdata('langs_templates', $this->langs_templates);
        $this->session->set_userdata('langs_main', $this->langs_main);
    }

    public function renderModulesNames($lang) {
        $langs = $this->session->userdata('langs');
        $langs = $langs[$lang];
        return \CMSFactory\assetManager::create()
                        ->setData('langs', $langs)
                        ->fetchAdminTemplate('modules_names');
    }

    public function renderTemplatesNames($lang) {
        $langs = $this->session->userdata('langs_templates');
        $langs = $langs[$lang];
        return \CMSFactory\assetManager::create()
                        ->setData('langs', $langs)
                        ->fetchAdminTemplate('templates_names');
    }

    public function renderModulePoFile($module_template, $type, $lang, $offset = 0, $limit = 10) {
        $this->session->set_userdata('offset', $offset);
        $this->session->set_userdata('limit', $limit);

        $this->session->unset_userdata('translation');
        $translations = $this->poFileToArray($module_template, $type, $lang);

        if ($translations != "no file") {
            $this->session->set_userdata('translation', array(
                'name' => $module_template,
                'type' => $type,
                'lang' => $lang,
                'po_array' => $translations
                    )
            );
            $page = floor($offset / $limit + 1);
            return \CMSFactory\assetManager::create()
                            ->setData('po_array', $translations)
                            ->setData('paths', $this->paths)
                            ->setData('page', $page)
                            ->setData('rows_count', ceil(count($translations) / ($limit + 1)))
                            ->fetchAdminTemplate('po_table');
        } else {
            return 'no file';
        }
    }

    public function poFileToArray($module_template, $type, $lang) {
        switch ($type) {
            case 'modules':
                $po = file($this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
            case 'templates':
                $po = file($this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
            case 'main':
                $po = file($this->main_path . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
        }
        if (!$po)
            return 'no file';

        $current = null;
        $this->paths = array();
        foreach ($po as $line) {

            if (preg_match('/Basepath/', $line)) {
                $from = strpos($line, ':');
                if (substr($line, -5, -4) == '\\') {
                    $this->paths[]['base'] = substr($line, $from + 2, -5);
                } else {
                    $this->paths[]['base'] = substr($line, $from + 2, -4);
                }
            }

            if (preg_match('/SearchPath/', $line)) {
                $from = strpos($line, ':');
                if (substr($line, -5, -4) == '\\') {
                    $this->paths[] = trim(substr($line, $from + 2, -5));
                } else {
                    $this->paths[] = trim(substr($line, $from + 2, -4));
                }
            }

            if (substr($line, 0, 1) == '#' && substr($line, 0, 2) != '#:' && substr($line, 0, 2) != '#,') {
                $comment = trim(substr(trim(substr($line, 1)), 0, -1));
            }
            if (substr($line, 0, 2) == '#,') {
                $fuzzy = TRUE;
            }

            if (substr($line, 0, 2) == '#:') {
                $links[] = trim(substr(trim(substr($line, 2)), 0, strlen($line)));
            }

            if (substr($line, 0, 5) == 'msgid') {
                $current = substr(substr($line, 6), 1, -2);
                if (strlen($current) > 1) {
                    if (substr($current, -1) == '"') {
                        $current = substr($current, 0, -1);
                    }
                } else {
                    $current = 0;
                    continue;
                }
            }

            if (substr($line, 0, 6) == 'msgstr') {
                if ($current) {
                    $translations[$current] = array(
                        'text' => trim(substr(trim(substr($line, 6)), 1, -1)),
                        'comment' => $comment,
                        'links' => $links,
                        'fuzzy' => $fuzzy
                    );
                }
                $fuzzy = FALSE;
                $comment = '';
                unset($links);
            }
        }
        return $translations;
    }

    public function savePoArray($module_template, $type, $lang) {
        $po_recieved = '';
        $url = '';
        $po_array = (array) json_decode($this->input->post('po_array'));

        switch ($type) {
            case 'modules':
                $url = $this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/' . $module_template . '.po';
                $po_recieved = file($this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
            case 'templates':
                $url = $this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/' . $module_template . '.po';
                $po_recieved = file($this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
            case 'main':
                $url = $this->main_path . $lang . '/LC_MESSAGES/' . $module_template . '.po';
                $po_recieved = file($this->main_path . $lang . '/LC_MESSAGES/' . $module_template . '.po');
                break;
        }

        $handle = @fopen($url, "wb");
        fwrite($handle, b"\xEF\xBB\xBF");
        if ($handle !== false) {
            if (count($po_array) > 0) {
                foreach ($po_recieved as $po_r) {
                    if (strstr($po_r, 'msgid') || strstr($po_r, 'msgstr')) {
                        continue;
                    }

                    if (preg_match('/Basepath/', $po_r)) {
                        fwrite($handle, '"X-Poedit-Basepath: ' . $po_array['paths'][0] . '\n"');
                        fwrite($handle, "\n");
                        array_shift($po_array['paths']);
                        continue;
                    }

                    if (preg_match('/SearchPath/', $po_r)) {
                        foreach ($po_array['paths'] as $number => $path) {
                            fwrite($handle, '"X-Poedit-SearchPath-' . $number . ': ' . $path . '\n"');
                            fwrite($handle, "\n");
                        }
                        break;
                    }
                    fwrite($handle, $po_r);
                }

                fwrite($handle, "\n");

                foreach ($po_array as $key => $po) {
                    if ($po) {
                        if ($po->comment) {
                            fwrite($handle, "# " . $po->comment . "\n");
                        }

                        if ($po->links) {
                            foreach ($po->links as $link) {
                                fwrite($handle, "#: " . $link . "\n");
                            }
                        }

                        if ($po->fuzzy) {
                            fwrite($handle, "#, fuzzy\n");
                        }

                        if ($key) {
                            if ($key != 'paths') {
                                fwrite($handle, 'msgid "' . $key . "\"\n");
                            } else {
                                continue;
                            }
                        }

                        if ($po->translation) {
                            fwrite($handle, 'msgstr "' . $po->translation . "\"\n");
                        } else {
                            fwrite($handle, 'msgstr "' . "" . "\"\n");
                        }

                        fwrite($handle, "\n");
                    }
                }
                fclose($handle);
            }

            $this->convertToMO($url);
        }
    }

    public function canselTranslation() {
        return $this->session->unset_userdata('translation');
    }

    function open_https_url($url, $refer = "", $usecookie = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPTё_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        if ($refer != "") {
            curl_setopt($ch, CURLOPT_REFERER, $refer);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function translateWord($to) {
        $from = 'en';
        $text = str_replace(' ', '%20', $this->input->post('word'));
        $apiKey = 'trnsl.1.1.20131007T222753Z.ce6162bf76f36118.5e8c33d185b2e48c6504492cdf203081c5085384';
        echo $this->open_https_url('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $apiKey . '&text=' . $text . '&lang=' . $from . '-' . $to . '&format=plain');
    }

    function recurseDirs($main, $count = 0) {
        $dirHandle = opendir($main);
        while ($file = readdir($dirHandle)) {
            if (is_dir($main . $file . "/") && $file != '.' && $file != '..') {
                $count = $this->recurseDirs($main . $file . "/", $count); // Correct call and fixed counting
            } else {
                $count++;
                $content = @file($main . $file);

                foreach ($content as $line_number => $line) {
                    $lang = array();
                    if (preg_match_all("/lang\(['\"]{1}(.*?)['\"]{1}/", $line, $lang)) {
                        foreach ($lang[1] as $origin) {
                            if (!$this->parsed_langs[$origin]) {
                                $this->parsed_langs[$origin] = array();
                            }
                            array_push($this->parsed_langs[$origin], $main . $file . ':' . $line_number);
                        }
                    }
                }
            }
        }
        return $count;
    }

    public function parseFiles($dir = "./application/modules/cfcm/") {
        $this->recurseDirs($dir);
//        var_dumps($this->parsed_langs);exit;
        $result = $this->parsed_langs;
        $this->parsed_langs = array();
        return $result;
    }

    public function convertToMO($file) {
        require_once('lib/php-mo.php');
        return phpmo_convert($file);
    }

    public function updatePoFile($module_template, $type, $lang) {
        $url = '';
        switch ($type) {
            case 'modules':
                $url = $this->modules_path . $module_template . '/language/' . $lang . '/LC_MESSAGES';
                break;
            case 'templates':
                $url = $this->templates_path . $module_template . '/language/' . $module_template . '/' . $lang . '/LC_MESSAGES';
                break;
            case 'main':
                $url = $this->main_path . $lang . '/LC_MESSAGES';
                break;
        }

        $paths = $this->input->post('paths');
        $parsedLangs = array();
        if ($paths) {
            foreach ($paths as $path) {
//                var_dumps($this->makeCorrectUrl($url, $path));
                $parsedLangs[] = $this->parseFiles($this->makeCorrectUrl($url, $path));
            }
        }
//        var_dumps($parsedLangs);
//        var_dumps($parsedLangs);exit;

        $all_langs = array();
        foreach ($parsedLangs as $key => $langsOne) {
            foreach ($langsOne as $origin => $paths) {
                if ($all_langs[$origin]) {
                    array_push($all_langs[$origin], $paths);
                } else {
                    $all_langs[$origin] = $paths;
                }
            }
        }
        $results = array();
        $currentLangs = $this->poFileToArray($module_template, $type, $lang);

        foreach ($all_langs as $key => $newLang) {
            if (!isset($currentLangs[$key])) {
                $results['new'][$key] = $newLang;
            } else {
                unset($currentLangs[$key]);
            }
        }
        $results['old'] = $currentLangs;
//        var_dumps($results);
        return json_encode($results);
    }

    public function makeCorrectUrl($from = '', $to = "") {

        $dotsCount = substr_count($to, '..');

        if (substr($from, -1) == '/') {
            $from = substr_replace($from, '', strlen($from) - 2);
        }

        for ($i = 0; $i < $dotsCount; $i++) {
            $pos = strrpos($from, '/');
            $from = substr_replace($from, '', $pos);
        }

        $dotsPos = strrpos($to, '..');
        if ($dotsPos) {
            $to = substr_replace($to, '', 0, $dotsPos + 2);
        }

        if (substr($to, 0, 1) == '.') {
            $to = substr_replace($to, '', 0, 1);
        }

        $url = $from . $to . '/';
        return $url;
    }

    public function update($module_template, $type, $lang) {
        $strings = (array) json_decode($this->input->post('results'));
        $translations = $this->poFileToArray($module_template, $type, $lang);

        if (!$translations) {
            $translations = array();
        }

        $translationTEMP = array();
        $newStringsArray = (array) $strings['new'];
        $oldStringsArray = (array) $strings['old'];

        foreach ($newStringsArray as $origin => $newStrings) {
            $translationTEMP[$origin]['text'] = '';
            $translationTEMP[$origin]['comment'] = '';
            $translationTEMP[$origin]['links'] = $newStrings;
            $translationTEMP[$origin]['fuzzy'] = false;
        }

        foreach ($oldStringsArray as $origin => $oldStrings) {
            if ($translations[$origin]) {
                unset($translations[$origin]);
            }
        }

        $result_array = array_merge($translationTEMP, $translations);
        $this->session->set_userdata('translation', array(
            'name' => $module_template,
            'type' => $type,
            'lang' => $lang,
            'po_array' => $translations
                )
        );
        $offset = 0;
        $limit = 10;
        $page = floor($offset / $limit + 1);
        return \CMSFactory\assetManager::create()
                        ->setData('po_array', $result_array)
                        ->setData('page', $page)
                        ->setData('rows_count', ceil(count($result_array) / ($limit + 1)))
                        ->fetchAdminTemplate('po_table');
    }

}
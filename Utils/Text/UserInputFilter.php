<?php

namespace Utils\Text;

use HTMLPurifier;
use HTMLPurifier_AttrDef_Enum;
use HTMLPurifier_AttrDef_HTML_Length;
use HTMLPurifier_AttrDef_Text;
use HTMLPurifier_AttrTransform_SafeParam;
use HTMLPurifier_Config;
use HTMLPurifier_ElementDef;
use HTMLPurifier_HTMLModule_SafeEmbed;
use HTMLPurifier_HTMLModule_SafeObject;
use HTMLPurifier_Injector_SafeObject;

use lib\Controllers\Php7Handler;


/**
 * class designed to contain user input filters.
 */
class UserInputFilter
{

    private static $config;
    private static $lastContext;

    private static function createConfig()
    {
        global $debug_page;
        global $dynbasepath;

        $config = HTMLPurifier_Config::createDefault();

        // Cache Serializer Path - keep it in area with dynamic files,
        // since the web server needs write permission there
        $cacheSerializerPath = $dynbasepath . 'lib/htmlpurifier';

        if (!file_exists($cacheSerializerPath)) {
            mkdir($cacheSerializerPath, 0777, true);
        }

        $config->set('Cache.SerializerPath', $cacheSerializerPath);

        // coś jest z CSSem sp***, i tyle...?
        $config->def->add('CSS.DefinitionID', null, 'string', true);

        // TODO: jeżeli iframe nie wskazuje na YouTube, to trzeba go całkiem wywalać, a nie tylko atrybut src
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $config->set('HTML.SafeObject', true);
        $config->set('HTML.SafeEmbed', true);
        $config->set('HTML.MaxImgLength', null);
        $config->set('CSS.MaxImgLength', null);
        $config->set('CSS.Proprietary', true);
        $config->set('CSS.Trusted', true); // ???
        $config->set('CSS.AllowTricky', true);
        $config->set('CSS.AllowedFonts', null);

        // this is ony for debug purpose - don't turn it on at production!
        $config->set('Core.CollectErrors', isset($debug_page) ? $debug_page : false);

        $config->set('Attr.AllowedFrameTargets', array('_blank'));

        // $config->set('HTML.Trusted', true); // <-- DO NOT ENABLE!

        $cssDefinition = $config->getCSSDefinition(true);

        // do not validate values, pass as is...
        $cssDefinition->info['box-shadow'] = $cssDefinition->info['text-shadow'] = new HTMLPurifier_AttrDef_Text();
        $cssDefinition->info['max-width'] = new HTMLPurifier_AttrDef_HTML_Length();

        $htmlDefinition = $config->getHTMLDefinition(true);

        // override default, very strict modules
        $htmlDefinition->manager->registerModule(new OC_HTMLSafeEmbed(), true);
        $htmlDefinition->manager->registerModule(new OC_HTMLSafeObject(), true);

        return $config;
    }

    public static function getConfig()
    {
        global $debug_page;
        if (self::$config !== null) {
            return self::$config;
        }
        $useCache = !(isset($debug_page) ? $debug_page : false);
        $cache_key = 'HTMLPurifierConfig';
        $result = $useCache ? Php7Handler::apc_fetch($cache_key) : false;
        if ($result === false) {
            $result = self::createConfig();
            // finalize and lock the config
            $result->getHTMLDefinition();
            $result->getCSSDefinition();
            $result->getURIDefinition();

            if ($useCache) {
                Php7Handler::apc_store($cache_key, $result, 60);  # cache it for 60 seconds
            }
        }
        return self::$config = $result;
    }

    /**
     * filter html string using HTMLPurifier.
     * refer to http://htmlpurifier.org/ for details and documentation.
     *
     * @param string $dirtyHtml
     * @return string
     */
    public static function purifyHtmlString($dirtyHtml, &$context = null)
    {
        $config = self::getConfig();
        $purifier = new HTMLPurifier($config);
        $cleanHtml = $purifier->purify($dirtyHtml);
        if (($config->get('Core.CollectErrors')) && ($context !== null)) {
            $context['errors'] = & $purifier->context->get('ErrorCollector');
        }
        return $cleanHtml;

        // 1. SVG się nie osadza -> done
        // http://opencaching.pl/viewcache.php?cacheid=17638
        // http://opencaching.pl/viewcache.php?cacheid=30273
        // <embed src="http://broadcasting.miklobit.pl/broadcasting.php" type="image/svg+xml" width="210" height="210" />
        // 2. style="background-image:url('url');" -> style="background-image:url("url");" -> style="background-image: url(;"
        // TODO: Wywalić htmlspecialchars_decode i powinno być gites majonez
        // 3. Znikają definicje scroll i text-shadow z CSSa -> done
    }

    /**
     * filter html string using HTMLPurifier
     * @param string $dirtyHtml
     * @return string
     */
    public static function purifyHtmlStringAndDecodeHtmlSpecialChars($dirtyHtml, $htmlMode)
    {

        // current working implementation - the old way
        if ($htmlMode < 2) {
            // see https://github.com/opencaching/opencaching-pl/issues/1218
            return htmlspecialchars_decode($dirtyHtml);
        } else {
            return $dirtyHtml;
        }
    }

}

class OC_HTMLSafeEmbed extends HTMLPurifier_HTMLModule_SafeEmbed
{

    public function setup($config)
    {
        $newEmbed = HTMLPurifier_ElementDef::create(
            null, null, array(
                // TODO: jeżeli brak atrybutu type, lub nie spełnia kryterów, to usuń cały element ?
                'type' => 'Enum#application/x-shockwave-flash,image/svg+xml',
                'width' => 'Length#1280',
                'height' => 'Length#1920',
                'allowscriptaccess' => 'Enum#never,always,sameDomain',
                'allownetworking' => 'Enum#all,internal,none',
            )
            );
        parent::setup($config);
        $embed = &$this->info['embed'];
        $embed->mergeIn($newEmbed);
        unset($embed->attr_transform_post[count($embed->attr_transform_post) - 1]);
    }

}

class OC_HTMLSafeObject extends HTMLPurifier_HTMLModule_SafeObject
{

    public function setup($config)
    {
        $newObject = HTMLPurifier_ElementDef::create(
            null, null, array(
                // TODO: j.w.
                'type' => 'Enum#application/x-shockwave-flash,image/svg+xml',
                'width' => 'Length#1280',
                'height' => 'Length#1920',
            )
            );
        parent::setup($config);
        $object = &$this->info['object'];
        $object->mergeIn($newObject);

        $param = &$this->info['param'];
        $param->attr_transform_post[count($param->attr_transform_post) - 1] = new OC_HTMLPurifier_AttrTransform_SafeParam();

        unset($this->info_injector[count($this->info_injector) - 1]);
        $this->info_injector[] = new OC_HTMLPurifier_Injector_SafeObject();
    }

}

class OC_HTMLPurifier_AttrTransform_SafeParam extends HTMLPurifier_AttrTransform_SafeParam
{

    protected $allowScriptAccess;
    protected $allowNetworking;

    public function __construct()
    {
        parent::__construct();
        $this->allowScriptAccess = new HTMLPurifier_AttrDef_Enum(array('never', 'always', 'sameDomain'));
        $this->allowNetworking = new HTMLPurifier_AttrDef_Enum(array('all', 'internal', 'none'));
    }

    public function transform($attr, $config, $context)
    {
        switch ($attr['name']) {
            case 'allowScriptAccess':
                $attr['value'] = $this->allowScriptAccess->validate($attr['value'], $config, $context);
                return $attr;
            case 'allowNetworking':
                $attr['value'] = $this->allowNetworking->validate($attr['value'], $config, $context);
                return $attr;
        }
        $attr = parent::transform($attr, $config, $context);
        return $attr;
    }

}

class OC_HTMLPurifier_Injector_SafeObject extends HTMLPurifier_Injector_SafeObject
{

    public function __construct()
    {
        unset($this->addParam['allowScriptAccess']);
        unset($this->addParam['allowNetworking']);
        $this->allowedParam['allowScriptAccess'] = true;
        $this->allowedParam['allowNetworking'] = true;
    }

    public function handleElement(&$token)
    {
        parent::handleElement($token);
    }

    public function handleEnd(&$token)
    {
        parent::handleEnd($token);
    }

}


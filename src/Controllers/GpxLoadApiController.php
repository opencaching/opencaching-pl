<?php

namespace src\Controllers;

use DOMDocument;
use DOMElement;
use Exception;
use src\Controllers\Core\ApiBaseController;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\GeoCacheCommons;

/**
 * Contains API methods used for uploaded GPX file parsing.
 */
class GpxLoadApiController extends ApiBaseController
{
    /** Uploaded file extensions valid for GPX document */
    private const VALID_FILE_EXTENSIONS = ['gpx'];

    /** Name of a form variable used to upload a file in a new cache creation */
    private const NEW_CACHE_UPLOADED_VAR = 'myfile';

    /** Filesystem path of directory containing XSD schemas */
    private const GPX_SCHEMA_PATH = __DIR__ . '/../../resources/schema/';

    /** Topografix 1.0 schema path */
    private const GPX_XSD_1_0_PATH = self::GPX_SCHEMA_PATH . '/' . 'gpx1_0.xsd';

    /** Topographics 1.1 schema path */
    private const GPX_XSD_1_1_PATH = self::GPX_SCHEMA_PATH . '/' . 'gpx1_1.xsd';

    /** Topografix 1.0 XML namespace */
    private const GPX_1_0_NS = 'http://www.topografix.com/GPX/1/0';

    /** Topografix 1.1 XML namespace */
    private const GPX_1_1_NS = 'http://www.topografix.com/GPX/1/1';

    /** Groundspeak 1.0 XML namespace */
    private const GSPK_1_0_NS = 'http://www.groundspeak.com/cache/1/0';

    /** Groundspeak 1.0.1 XML namespace */
    private const GSPK_1_0_1_NS = 'http://www.groundspeak.com/cache/1/0/1';

    /** Opencaching GPX extension XML namespace */
    private const OC_NS = 'https://github.com/opencaching/gpx-extension-v1';

    /** Mapping of Groundspeak 1.0.1 type tag values to GeoCacheCommons cache
     * types.
     */
    private const GSPK_CACHE_TYPE_MAPPING = [
        'traditional cache' => GeoCacheCommons::TYPE_TRADITIONAL,
        'multi-cache' => GeoCacheCommons::TYPE_MULTICACHE,
        'unknown cache' => GeoCacheCommons::TYPE_QUIZ,
        'virtual cache' => GeoCacheCommons::TYPE_VIRTUAL,
        'webcam cache' => GeoCacheCommons::TYPE_WEBCAM,
        'letterbox hybrid' => GeoCacheCommons::TYPE_OTHERTYPE,
        'earthcache' => GeoCacheCommons::TYPE_OTHERTYPE,
        'wherigo cache' => GeoCacheCommons::TYPE_OTHERTYPE,
        'event cache' => GeoCacheCommons::TYPE_EVENT,
        'cache in trash out event' => GeoCacheCommons::TYPE_EVENT,
        'mega-event cache' => GeoCacheCommons::TYPE_EVENT,
        'giga-event cache' => GeoCacheCommons::TYPE_EVENT,
    ];

    /** Mapping of Groundspeak 1.0.1 container tag values to GeoCacheCommons
     * cache sizes.
     */
    private const GSPK_CACHE_SIZE_MAPPING = [
        'micro' => GeoCacheCommons::SIZE_MICRO,
        'small' => GeoCacheCommons::SIZE_SMALL,
        'regular' => GeoCacheCommons::SIZE_REGULAR,
        'large' => GeoCacheCommons::SIZE_LARGE,
        'other' => GeoCacheCommons::SIZE_OTHER,
        'virtual' => GeoCacheCommons::SIZE_NONE,
    ];

    /** Mapping of Opencaching type tag values to GeoCacheCommons cache
     * types.
     */
    private const OC_CACHE_TYPE_MAPPING = [
        'traditional cache' => GeoCacheCommons::TYPE_TRADITIONAL,
        'multi-cache' => GeoCacheCommons::TYPE_MULTICACHE,
        'quiz cache' => GeoCacheCommons::TYPE_QUIZ,
        'moving cache' => GeoCacheCommons::TYPE_MOVING,
        'virtual cache' => GeoCacheCommons::TYPE_VIRTUAL,
        'webcam cache' => GeoCacheCommons::TYPE_WEBCAM,
        'podcast cache' => GeoCacheCommons::TYPE_GEOPATHFINAL,
        'event cache' => GeoCacheCommons::TYPE_EVENT,
        'own cache' => GeoCacheCommons::TYPE_OWNCACHE,
        'other cache' => GeoCacheCommons::TYPE_OTHERTYPE,
    ];

    /** Mapping of Opencaching size tag values to GeoCacheCommons cache
     * sizes.
     */
    private const OC_CACHE_SIZE_MAPPING = [
        'nano' => GeoCacheCommons::SIZE_NANO,
        'micro' => GeoCacheCommons::SIZE_MICRO,
        'small' => GeoCacheCommons::SIZE_SMALL,
        'regular' => GeoCacheCommons::SIZE_REGULAR,
        'large' => GeoCacheCommons::SIZE_LARGE,
        'very large' => GeoCacheCommons::SIZE_XLARGE,
        'other' => GeoCacheCommons::SIZE_OTHER,
        'no container' => GeoCacheCommons::SIZE_NONE,
    ];

    public function __construct()
    {
        parent::__construct();

        $this->checkUserLoggedAjax();
    }

    /**
     * Validates uploaded GPX document and if ok, invokes parsing its contents.
     * Sends as a response an array containing 'status', where 'code',
     * 'code_txt' and 'msg' fields are filled up, and 'data' filled up with
     * parsed document contents if status is ok.
     */
    public function newCacheGpxLoad()
    {
        $result = [
            'status' => [],
            'data' => [],
        ];

        // if a file has been uploaded
        if (isset($_FILES[self::NEW_CACHE_UPLOADED_VAR])) {
            $name = $_FILES[self::NEW_CACHE_UPLOADED_VAR]['name'];

            if (strlen($name)) {
                [$base, $ext] = explode('.', $name);
                $ext = strtolower($ext);

                // if an upload file extension is valid
                if (
                    in_array($ext, self::VALID_FILE_EXTENSIONS)
                    && is_file(
                        $_FILES[self::NEW_CACHE_UPLOADED_VAR]['tmp_name']
                    )
                ) {
                    $doc = new DOMDocument();
                    // load a file contents (assumed XML) to DOM
                    $doc->load(
                        $_FILES[self::NEW_CACHE_UPLOADED_VAR]['tmp_name']
                    );
                    $validSchema = 0; // contains schema number as float

                    // try to validate against Topografix 1.0 schema
                    try {
                        $validSchema
                            = @$doc->schemaValidate(self::GPX_XSD_1_0_PATH)
                            ? 1.0
                            : 0;
                    } catch (Exception $ignore) {
                    }

                    // if it is not a Topografix 1.0 document
                    if ($validSchema <= 0) {
                        // try to validate against Topografix 1.1 schema
                        try {
                            $validSchema
                                = $doc->schemaValidate(self::GPX_XSD_1_1_PATH)
                                ? 1.1
                                : 0;
                        } catch (Exception $ignore) {
                        }
                    }

                    // if document is valid
                    if ($validSchema > 0) {
                        $result['status'] = $this->getOKStatus(
                            tr('newcache_import_wpt_ok')
                        );
                        // invoke parsing document contents
                        $result['data'] = $this->parseGpx(
                            $doc,
                            $validSchema == 1.1
                            ? self::GPX_1_1_NS
                            : self::GPX_1_0_NS
                        );
                    } else {
                        $result['status'] = $this->getErrorStatus(
                            tr('newcache_import_gpx_invalid')
                        );
                    }
                }
            } else {
                $result['status'] = $this->getErrorStatus('Empty name');
            }
        } else {
            $result['status'] = $this->getErrorStatus('No file uploaded');
        }

        $this->ajaxJsonResponse($result);
    }

    /**
     * Parses a valid PGX document, extracting selected data and putting it to
     * the resulting array.
     * @param $doc loaded XML document
     * @param $gpxNs XML GPX namespace of parsed document
     * @return array containing waypoints extracted from document during parsing
     */
    private function parseGpx(DOMDocument $doc, string $gpxNs): array
    {
        $result = [];

        foreach ($doc->getElementsByTagNameNS($gpxNs, 'wpt') as $node) {
            // extract coordinates to corresponding parts (NS|EW, h, min)
            $coords = Coordinates::FromCoordsFactory(
                (float) $node->attributes->getNamedItem('lat')->value,
                (float) $node->attributes->getNamedItem('lon')->value
            );
            $lat = array_merge(...array_map(
                fn ($k, $v): array => [$k => $v],
                ['coords_latNS', 'coords_lat_h', 'coords_lat_min'],
                $coords->getLatitudeParts()
            ));
            $lon = array_merge(...array_map(
                fn ($k, $v): array => [$k => $v],
                ['coords_lonEW', 'coords_lon_h', 'coords_lon_min'],
                $coords->getLongitudeParts()
            ));

            // initialize waypoint array with Topografix data and defaults
            $wpt = [
                'name' => $this->getElementValue($node, 'name'),
                'time' => $this->getElementValue($node, 'time'),
                'desc' => $this->getElementValue($node, 'desc'),
                'short_desc' => '',
                'type' => 0,
                'size' => 0,
                'difficulty' => 0,
                'terrain' => 0,
                'hint' => '',
                'trip_time' => 0,
                'trip_distance' => 0,
            ];
            $wpt = array_merge($wpt, $lat, $lon);

            $gspk = $node->getElementsByTagNameNS(self::GSPK_1_0_NS, 'cache');

            if ($gspk->length == 0) {
                $gspk = $node->getElementsByTagNameNS(
                    self::GSPK_1_0_1_NS,
                    'cache'
                );
            }

            // parse Groundspeak 1.0/1.0.1 cache tag with subelements, if exists,
            // overwriting corresponding existing $wpt fields
            if ($gspk->length > 0) {
                $gspk = $gspk->item(0);

                $wpt['wp_gc'] = $wpt['name'];
                $wpt['name'] = $this->getElementValue(
                    $gspk,
                    'name',
                    $wpt['name'],
                );

                $cacheType = strtolower($this->getElementValue($gspk, 'type'));
                $wpt['type']
                    = self::GSPK_CACHE_TYPE_MAPPING[$cacheType] ?? $wpt['type'];

                $cacheSize = strtolower(
                    $this->getElementValue($gspk, 'container')
                );
                $wpt['size']
                    = self::GSPK_CACHE_SIZE_MAPPING[$cacheSize] ?? $wpt['size'];

                $wpt['difficulty'] = (float) $this->getElementValue(
                    $gspk,
                    'difficulty',
                    $wpt['difficulty']
                );

                $wpt['terrain'] = (float) $this->getElementValue(
                    $gspk,
                    'terrain',
                    $wpt['terrain']
                );

                $wpt['hint'] = $this->getElementValue(
                    $gspk,
                    'encoded_hints',
                    $wpt['hint']
                );

                $wpt['desc'] = $this->getElementValue(
                    $gspk,
                    'long_description',
                    $wpt['desc']
                );

                $wpt['short_desc'] = $this->getElementValue(
                    $gspk,
                    'short_description',
                    $wpt['short_desc']
                );
            }

            $oc = $node->getElementsByTagNameNS(self::OC_NS, 'cache');

            // parse Opencaching cache tag with subelements, if exists,
            // overwriting corresponding existing $wpt fields
            if ($oc->length > 0) {
                $oc = $oc->item(0);

                $cacheType = strtolower($this->getElementValue($oc, 'type'));
                $wpt['type']
                    = self::OC_CACHE_TYPE_MAPPING[$cacheType] ?? $wpt['type'];

                $cacheSize = strtolower(
                    $this->getElementValue($oc, 'size')
                );
                $wpt['size']
                    = self::OC_CACHE_SIZE_MAPPING[$cacheSize] ?? $wpt['size'];

                $wpt['trip_time'] = (float) $this->getElementValue(
                    $oc,
                    'trip_time',
                    $wpt['trip_time']
                );
                $wpt['trip_time']
                    = $wpt['trip_time'] < 0 ? 0 : $wpt['trip_time'];

                $wpt['trip_distance'] = (float) $this->getElementValue(
                    $oc,
                    'trip_distance',
                    $wpt['trip_distance']
                );
                $wpt['trip_distance']
                    = $wpt['trip_distance'] < 0 ? 0 : $wpt['trip_distance'];
            }

            $result[] = $wpt;
        }

        return $result;
    }

    /**
     * Creates OK status for response.
     * @param $msg status message
     * @return array with 'code', 'code_txt' and 'msg' fields
     */
    private function getOKStatus(string $msg = ''): array
    {
        return [
            'code' => 0,
            'code_txt' => 'OK',
            'msg' => $msg,
        ];
    }

    /**
     * Creates Error status for response.
     * @param $msg status message
     * @return array with 'code', 'code_txt' and 'msg' fields
     */
    private function getErrorStatus(string $msg = ''): array
    {
        return [
            'code' => 1,
            'code_txt' => 'Error',
            'msg' => $msg,
        ];
    }

    /**
     * Gets a value of the first occurence of given element in passed node, or
     * returns passed default value if no element found.
     * @param $node a DOM Node to search for element within
     * @param $name the name of element to search for
     * @param $default the value to return if no element found
     * @return string the first matching element value or default if not found
     */
    private function getElementValue(
        DOMElement $node,
        string $name,
        string $default = ''
    ): string {
        $result = null;
        $nl = $node->getElementsByTagName($name);

        if ($nl->length > 0) {
            $result = $nl->item(0)->nodeValue;
        }

        return $result != null ? $result : $default;
    }
}

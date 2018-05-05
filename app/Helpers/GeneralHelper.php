<?php
/**
 * Created by TuyenNV.
 * Date: 6/3/2016
 * Time: 2:02 PM
 */

/**
 * Get value of variable (set or not set).
 * @param mixed $variable
 * @param mixed $default
 * @return mixed
 */
function apply(&$variable, $default = null)
{
    is_null($variable) and $variable = $default;

    return $variable;
}

/**
 * Check value is null, false, empty array or blank. '0' and 0 is not blank.
 * @param mixed $value
 * @return bool true = blank, false = not blank
 */
function is_blank($value)
{
    return (empty($value) and $value !== 0 and $value !== '0');
}

/**
 * Check variable (set or not set) is blank.
 * @param mixed $varibale
 * @return bool true = blank, false = not blank
 */
function is_blank_var(&$varibale)
{
    return is_blank($varibale);
}

function dir_size($dir)
{
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);
    foreach ($dir_array as $key => $filename) {
        if ($filename != ".." && $filename != ".") {
            if (is_dir($dir . "/" . $filename)) {
                $new_foldersize = dir_size($dir . "/" . $filename);
                $count_size = $count_size + $new_foldersize;
            } elseif (is_file($dir . "/" . $filename)) {
                $count_size = $count_size + filesize($dir . "/" . $filename);
                $count++;
            }
        }
    }
    return $count_size;
}

function convert_size($bytes){
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;

    if (($bytes >= 0) && ($bytes < $kb)) {
        return $bytes . ' B';

    } elseif (($bytes >= $kb) && ($bytes < $mb)) {
        return ceil($bytes / $kb) . ' KB';

    } elseif (($bytes >= $mb) && ($bytes < $gb)) {
        return ceil($bytes / $mb) . ' MB';

    } elseif (($bytes >= $gb) && ($bytes < $tb)) {
        return ceil($bytes / $gb) . ' GB';

    } elseif ($bytes >= $tb) {
        return ceil($bytes / $tb) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Array to xml.
 * @param mixed $data
 * @param \SimpleXMLElement|null $xml
 * @param string $baseNode
 * @return mixed
 * @throws XmlResponseException
 */
function xmlify($data, $baseNode = 'xml', $xml = null)
{
    // Turn off compatibility mode as simple xml throws a wobbly if you don't.
    if (ini_get('zend.ze1_compatibility_mode') == 1) {
        ini_set('zend.ze1_compatibility_mode', 0);
    }

    if (is_null($xml)) {
        $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$baseNode} />");
    }

    // Force it to be something useful
    if (!is_array($data) && !is_object($data)) {
        $data = (array) $data;
    }

    foreach ($data as $key => $value) {
        // Replace anything not alpha numeric.
        $key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

        // Convert our booleans to 0/1 integer values so they are not converted to blanks.
        if (is_bool($value)) {
            $value = (int) $value;
        }

        // If there is another array found recrusively call this function.
        if (is_array($value) or is_object($value)) {
            // No numeric keys in our xml please!
            if (is_numeric($key)) {
                // Create parent node.
                $node = $xml->addChild('items');

                // Add attribute id.
                $node->addAttribute('id', $key);

                // Recrusively add children nodes.
                xmlify($value, $baseNode, $node);
            } else {
                // Create parent node.
                $node = $xml->addChild($key);

                // Recrusively add children nodes.
                xmlify($value, $baseNode, $node);
            }

        } else {
            // Add single node.
            // Entities special chars.
            $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

            if (is_numeric($key)) {
                // Create single node.
                $node = $xml->addChild('item', $value);

                // Add attribute id.
                $node->addAttribute('id', $key);
            } else {
                // Create single node.
                $xml->addChild($key, $value);
            }
        }
    }

    // Pass back as string. or simple xml object if you want!
    return $xml->asXML();
}

/**
 * Get SAPI type name.
 * @return string
 */
function sapi_type ()
{
    switch (PHP_SAPI) {
        case 'cli':
        case 'cli-server':
            $platform = 'command';
            break;

        case 'apache':
        case 'apache2filter':
        case 'apache2handler':
        case 'cgi':
        case 'cgi-fcgi':
        case 'fpm-fcgi':
            $platform = 'http';
            break;

        default:
            $platform = PHP_SAPI;
    }

    return $platform;
}

/**
 * Get a unique key from args.
 * @param array $args
 * @return string unique key
 */
function condition_key (...$args)
{
    // Convert array params to string.
    // Calculate unique key from serialize string.
    // Return unique key.
    return md5(serialize($args));
}

function pushToFrontEnd($data) {
    $options = array(
        'cluster' => 'ap1',
        'encrypted' => true
    );
    $pusher = new \Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        $options
    );
    return $pusher->trigger('my-channel', 'my-event', $data);
}
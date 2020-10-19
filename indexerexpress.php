<?php
 /**
 * Indexer Express
 *
 * @package     IndexerExpress
 * @author      Mirado <misie.koto@gmail.com>
 * @copyright   2020 Weto
 *
 * @wordpress-plugin
 * Plugin Name: Indexer Express
 * Plugin URI:  https://indexer.express
 * Author:      Mirado
 * Author URI:  https://indexer.express
 * Description: Envoyer automatiquement des articles WordPress vers https://indexer.express
 * Version:     1.5.0
 */

if (!defined('ABSPATH')) {
    die('Permission denied');
}

use \Symfony\Component\Dotenv\Dotenv;
use \IndexerExpress\Application;

require_once __DIR__ . '/vendor/autoload.php';

if (\file_exists(__DIR__ . '/env')) {
    (new Dotenv)->load(__DIR__ . '/env');
}

$app = new Application(__FILE__);
$app->execute();

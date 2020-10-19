<?php
namespace IndexerExpress;

use \BlackhatMoney\WordPress\Hook;
use \BlackhatMoney\WordPress\Updater;
use \IndexerExpress\Logger;
use \IndexerExpress\Indexer;

/**
 * Controlleur principal
 *
 * @author Mirado <misie.koto@gmail.com>
 */
class Application extends Hook
{
    /**
     * Enregistrer les actions à realiser par les hooks de WordPress
     *
     * @return void
     */
    public function boot()
    {
        // Rendre le plugin updatable à partir de http://hub.tsena.be
        $this->updatable(new Updater($this->plugin));

        // Déclarer les options
        $this->action('admin_init', function () {
            register_setting('indexerexpress', 'indexer_express_posts');
            register_setting('indexerexpress', 'indexer_express_api');
        });


        // Page d'option
        $this->action('admin_menu', function () {
            \add_submenu_page('tools.php', 'Indexer Express', 'Indexer Express', 'manage_options', 'indexer.express', function () {
                require_once __DIR__  . '/../resources/views/option.php';
            });
        });


        // Envoyer les liens vers Indexer Express
        $this->action('indexerexpress', function () {
            if (get_option('indexer_express_done', false)) {
                $this->logger()->info('Les liens ont été déjà envoyé');

                return true;
            }

            if (getenv('INDEXER_POSTS') && !empty(getenv('INDEXER_POSTS'))) {
                $limit = getenv('INDEXER_POSTS');
            } else {
                $limit = get_option('indexer_express_posts', 200);
            }


            $posts = (wp_count_posts())->publish;

            if ($limit >= $posts) {
                $this->logger()->info('En attente du nombre d\'article');

                return true;
            }

            Indexer::send($limit, $this->logger());
        });

        // Rediriger après activation
        $pluginId = $this->plugin;

        $this->action('activated_plugin', function ($plugin) use($pluginId) {
            if ($plugin == $pluginId && !defined('WP_CLI')) {
                wp_redirect(admin_url('tools.php?page=indexer.express'));
                exit;
            }
        }, 1);

        // Enregistrer les options par defaut
        $this->plugin('install', function () {
            add_option('indexer_express_redirect', false);

            if (!wp_next_scheduled('indexerexpress')) {
                wp_schedule_event(time() + (60*60), 'hourly', 'indexerexpress');
            }
        });

        // Supprimer les options à desactivation du plugin
        $this->plugin('uninstall', function () {
            wp_clear_scheduled_hook('indexerexpress');
            delete_option('indexer_express_done');
        });
    }

}
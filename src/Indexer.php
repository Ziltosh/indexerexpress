<?php
namespace IndexerExpress;

use Monolog\Logger;
use \GuzzleHttp\Client as Http;

/**
 * Gestion des appels vers Indexer.Express
 *
 * @author Mirado <misie.koto@gmail.com>
 */
class Indexer
{
    /**
     * Envoyer les articles vers https://indexer.express
     *
     * @param integer $limit
     * @param \Monolog\Logger $logger
     * @return void
     * @todo  Privilegier WP_Http au lieu de Guzzle
     */
    public static function send(int $limit = 200, Logger $logger)
    {
        $logger->notice('Envoyer les articles vers Indexer.Express');

        try {
            $data = [
                'url' => []
            ];

            $posts = get_posts([
                'numberposts' => $limit,
                'orderby' => 'rand'
            ]);

            foreach ($posts as $post) {
                $data['url'][] = get_permalink($post->ID);
            }

            if (empty($data['url'])) {
                throw new \Exception("Les URLs sont vides. Il n'y a pas assez d'articles");
            }

            $logger->notice('Les Urls sont prets', $data);

            // On convertit au format de l'API Elite
            $data['url'] = implode(',', $data['url']);


            $http = new Http;

            $token = getenv('INDEXER_API') && !empty(getenv('INDEXER_API')) ? getenv('INDEXER_API') : get_option('indexer_express_api', false);

            if (!$token) {
                throw new \Exception('Vous devez renseigner le token');
            }

            $response = $http->request('POST', 'https://indexer.express/api/links', [
                'form_params' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ]
            ]);

            $body = json_decode($response->getBody()->getContents());

            if ($response->getStatusCode() == 200) {
                $logger->info('Les URLs ont été envoyé vers Indexer.Express');
            } elseif ($response->getStatusCode() == 400) {
                $logger->error("Vous avez envoyer des mauvaises URLs.");
            } elseif ($response->getStatusCode() == 403) {
                $logger->error("Vous n'avez plus de crédits");
            } else {
                $logger->error("Une erreur inattendue : '{$response->getReasonPhrase()}'. Veuillez contacter le responsable .");
            }

            update_option('indexer_express_done', true);
        } catch (\Exception $e) {
            $logger->error('Une erreur est survenue en contactant Indexer.Express', ['raison' => $e->getMessage()]);
        }
    }
}

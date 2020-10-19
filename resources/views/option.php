<div class="wrap">
    <h1>Configurations</h1>
    <form method="post" action="options.php"> 
        <?php settings_fields('indexerexpress'); ?>
        <?php do_settings_sections('indexerexpress'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Nombre d'article à envoyer: </th>
                <td>
                    <input type="number" name="indexer_express_posts" value="<?php echo esc_attr(get_option('indexer_express_posts', 200) ); ?>" />                                    
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Clé API: </th>
                <td>
                    <input type="text" name="indexer_express_api" value="<?php echo esc_attr(get_option('indexer_express_api') ); ?>" />                                    
                    <br/>
                    <p class="description">Récuperer votre clé sur <a target="_blank" href="https://indexer.express/account/api">https://indexer.express/account/api</a></p>
                </td>
            </tr>
        </table>        
        <?php submit_button(); ?>
    </form>
</div><!-- .wrap -->
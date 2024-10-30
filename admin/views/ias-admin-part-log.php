<li class="log">
    <?php echo wp_get_attachment_image( $result['id'] ); ?>
    <p class="log__title"><?php echo date('H:i:s') . ' - ' . $result['name']; ?> (ID <?php echo $result['id']; ?>)</p>

    <?php if ( !empty( $result['results'] ) ) : ?>
        <ul class="log__results">
            <?php foreach( $result['results'] as $log_result ) : 
                $img_name = $log_result['success'] ? 'success.svg' : 'error.svg';
                $class = $log_result['success'] ? 'log__result--success' : 'log__result--failure';
            ?>
                <li class="log__result">
                    <img class="result__img" src="<?php echo plugins_url() . '/images-advanced-settings/admin/images/' . $img_name; ?>">
                    <p class="result__text"><?php echo $log_result['message']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</li>
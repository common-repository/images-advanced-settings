<div class="wrap ias">
    <h1><?php echo get_admin_page_title(); ?></h1>

    <ul class="tabs nav-tab-wrapper" role="tablist">
        <?php $i = 0; foreach ( $sections as $section_key => $section_title ) : 
            $first_tab = $i === 0;
        ?>
            <li class="tab" role="presentation">
                <a 
                    class="<?php echo $first_tab ? 'nav-tab nav-tab-active' : 'nav-tab'; ?>"
                    role="tab" 
                    href="#<?php echo $section_key; ?>" 
                    aria-controls="<?php echo $section_key; ?>" 
                    <?php echo $first_tab ? ' aria-selected="true"' : ' tabindex="-1"'; ?>
                >
                    <?php echo $section_title; ?>
                </a>
            </li>
        <?php $i++; endforeach; ?>
    </ul>

    <?php $i = 0; foreach ( $sections as $section_key => $section_title ) : ?>
        <div
            id="<?php echo $section_key; ?>" 
            class="tab-panel" 
            role="tabpanel" 
            aria-labelledby="<?php echo $section_key; ?>-tab" 
            <?php if ( $i !== 0 ) echo ' hidden'; ?>
        >
            <h2><?php echo $section_title; ?></h2>

            <?php include IAS_Helpers::get_admin_view('ias-admin-section-' . $section_key); ?>
        </div>
    <?php $i++; endforeach; ?>
</div>

<div id="logs-container" class="logs__container" aria-hidden="true">
    <h2><?php _e( 'Logs', 'images-advanced-settings' ); ?></h2>
    <progress id="logs-bar" class="logs__bar"></progress>
    <button id="button-stop" class="button stop-button" aria-hidden="true"><?php _e( 'Stop', 'images-adva,ced-settings' ); ?></button>
    <p id="logs-status" class="logs__status"></p>
    <ol id="logs" class="logs"></ol>
</div>

<section>
    <h3><?php _e( 'Disable default WordPress sizes', 'images-advanced-settings' ); ?></h2>
    <p><?php _e( 'Checked sizes are not generated at image upload', 'images-advanced-settings' ); ?></p>

    <form id="default-form">
        <div class="default-checkboxes__container">
            <?php foreach ( $default_sizes as $default_size ) : ?>
                <div>
                    <input type="checkbox" id="default-sizes-<?php echo $default_size; ?>" name="default_sizes_disabled[]" value="<?php echo $default_size; ?>" <?php checked( in_array( $default_size, $this->ias_sizes->data['default_sizes_disabled'] ), true ); ?>>
                    <label for="default-sizes-<?php echo $default_size; ?>"><?php echo $default_size; ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="default-submit" class="button button-primary" type="submit" disabled>
            <?php _e( 'Save', 'images-advanced-settings' ); ?>
            <div class="ias-spinner"></div>
        </button>

        <p id="default-message" class="info-message"></p>
    </form>
</section> 

<section>
    <h3><?php _e( 'Add a custom size', 'images-advanced-settings' ); ?></h2>

    <form id="add-form">
        <input type="hidden" name="new_size[disabled]" value="0">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e( 'Name', 'images-advanced-settings' ); ?></label>
                </th>
                <td>
                    <input type="text" id="name" name="new_size[name]" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="width"><?php _e( 'Width', 'images-advanced-settings' ); ?></label>
                </th>
                <td>
                    <input type="number" id="width" name="new_size[width]" class="small-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="height"><?php _e( 'Height', 'images-advanced-settings' ); ?></label>
                </th>
                <td>
                    <input type="number" id="height" name="new_size[height]" class="small-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="crop"><?php _e( 'Crop', 'images-advanced-settings' ); ?></label>
                </th>
                <td>
                    <input type="hidden" id="crop" name="new_size[crop]" value="0">
                    <input class="crop" type="checkbox" id="crop" name="new_size[crop]" value="1">
                    <select class="crop-position" name="new_size[crop_position]">
                        <?php foreach ( $crop_positions as $key => $name ) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <button id="add-submit" class="button button-primary" type="submit" disabled>
            <?php _e( 'Add', 'images-advanced-settings' ); ?>
            <div class="ias-spinner"></div>
        </button>

        <p id="add-message" class="info-message"></p>
    </form>
</section>

<section>
    <h2><?php _e( 'Custom sizes', 'images-advanced-settings' ); ?></h2>

    <div id="custom-sizes">
        <?php include IAS_Helpers::get_admin_view('ias-admin-part-custom-sizes'); ?>
    </div>
</section>

<div id="remove-modal" class="remove__modal" aria-hidden="true">
    <p class="modal__title"><?php _e( 'Do you really want to remove this image size ?', 'images-advanced-settings' ); ?></p>

    <div class="modal__input-container">
        <input type="checkbox" id="remove-images-checkbox" name="remove_images" checked>
        <label for="remove-images-checkbox"><?php _e( 'Remove generated images of this size too', 'images-advanced-settings' ); ?></label>
    </div>

    <div class="modal__buttons">
        <button id="cancel-remove-button" class="button modal__button"><?php _e( 'Cancel', 'images-advanced-settings' ); ?></button>
        <button id="confirm-remove-button" class="button button-primary modal__button">
            <?php _e( 'Remove', 'images-advanced-settings' ); ?>
            <div class="ias-spinner"></div>
        </button>
    </div>
</div>
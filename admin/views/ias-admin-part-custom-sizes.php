<?php if ( !empty( $this->data['sizes'] ) ) : ?>
    <form id="update-form" class="update-form">
        <div class="sizes">
            <?php foreach ( $this->data['sizes'] as $i => $size ) : 
                $name = esc_attr( $size['name'] );
                $id_name = sanitize_title( $name );
            ?>
                <div class="size">
                    <div>
                        <p class="size__name"><?php echo $name; ?></p>
                        <input type="hidden" name="updated_sizes[name][]" value="<?php echo esc_attr( $size['name'] ); ?>" required>
                    </div>
                    <div>
                        <label for="<?php echo $id_name . '_size_width'; ?>"><?php _e( 'Width', 'images-advanced-settings' ); ?></label>
                        <input type="number" id="<?php echo $id_name . '_size_width'; ?>" class="small-text" name="updated_sizes[width][]" value="<?php echo esc_attr( $size['width'] ); ?>" required>
                    </div>
                    <div>
                        <label for="<?php echo $id_name . '_size_height'; ?>"><?php _e( 'Height', 'images-advanced-settings' ); ?></label>
                        <input type="number" id="<?php echo $id_name . '_size_height'; ?>" class="small-text" name="updated_sizes[height][]" value="<?php echo esc_attr( $size['height'] ); ?>" required>
                    </div>
                    <div>
                        <input type="hidden" name="updated_sizes[disabled][<?php echo $i; ?>]" value="0">
                        <input type="checkbox" id="<?php echo $id_name . '_size_disabled'; ?>" name="updated_sizes[disabled][<?php echo $i; ?>]" value="1" <?php checked( $size['disabled'], '1' ); ?>>
                        <label for="<?php echo $id_name . '_size_disabled'; ?>"><?php _e( 'Disabled', 'images-advanced-settings' ); ?></label>
                    </div>
                    <div>
                        <input type="hidden" name="updated_sizes[crop][<?php echo $i; ?>]" value="0">
                        <input class="crop" id="<?php echo $id_name . '_size_crop'; ?>" type="checkbox" name="updated_sizes[crop][<?php echo $i; ?>]" value="1" <?php checked( $size['crop'], '1' ); ?>>
                        <label for="<?php echo $id_name . '_size_crop'; ?>"><?php _e( 'Crop', 'images-advanced-settings' ); ?></label>
                        <select class="crop-position" class="small-text" name="updated_sizes[crop_position][]">
                            <?php foreach ( $crop_positions as $key => $name ) : ?>
                                <option value="<?php echo $key; ?>" <?php selected( $size['crop_position'], $key ); ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button data-index="<?php echo $i; ?>" class="button remove-button"><?php _e( 'Delete', 'images-advanced-settings' ); ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="update-submit" class="button button-primary" type="submit" disabled>
            <?php _e( 'Save modifications', 'images-advanced-settings' ); ?>
            <div class="ias-spinner"></div>
        </button>
    </form>
<?php else : ?>
    <p><?php _e( 'No custom sizes', 'images-advanced-settings' ); ?></p>
<?php endif; ?>

<p id="update-message" class="info-message"></p>
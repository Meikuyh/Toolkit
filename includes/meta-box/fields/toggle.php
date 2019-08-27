<?php

/**
 * Toggle field for RW Meta Box plugin.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class RWMB_Toggle_Field extends RWMB_Field {
	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{
		// Prepare meta value
		$meta = $meta == '' ? $field['std'] : $meta;

		// Print HTML
		ob_start();
		?>
		<div id="<?php echo esc_attr( $field['id'] ); ?>-container">
			<input type="checkbox" class="metabox-toggle" id="<?php echo esc_attr( $field['id'] ); ?>-checkbox" <?php
				checked( ( int ) $meta, 1 );
			?> onchange="jQuery(this).parent().children('input[name]').val(this.checked ? '1' : '0').trigger('change');">
			<label for="<?php echo esc_attr( $field['id'] ); ?>-checkbox"></label>
			<input type="hidden" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $meta ); ?>">
		</div>
		<?php
		return ob_get_clean();
	}
}

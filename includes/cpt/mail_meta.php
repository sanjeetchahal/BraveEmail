<?php

class mails
{
	private $config = '{
	  "title": "Mail",
	  "prefix": "md_",
	  "domain": "text-domain",
	  "class_name": "mails",
	  "post-type": [
	    "post"
	  ],
	  "context": "normal",
	  "priority": "high",
	  "cpt": "mails",
	  "fields": [
		{
		   "type":"select",
			"label":"Notification For",
			"id":"notification"
		},
		{
			"type":"radio",
			"label":"Email Formatting ",
			"default":"HTML Formation",
			"id":"email-formatting"
		},
	    {
	      "type": "text",
	      "label": "Subject",
	      "id": "subject"
	    },
		{
			"type":"editor",
			"label":"Message Body",
			"wpautop":"1",
			"media-buttons":"1",
			"teeny":"1",
			"id":"message-body"}
	  ]
	}';

	public function __construct()
	{
		$this->config = json_decode($this->config, true);
		$this->process_cpts();
		add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
		add_action('admin_head', [$this, 'admin_head']);
		add_action('save_post', [$this, 'save_post']);
	}

	public function process_cpts()
	{
		if (!empty($this->config['cpt'])) {
			if (empty($this->config['post-type'])) {
				$this->config['post-type'] = [];
			}
			$parts = explode(',', $this->config['cpt']);
			$parts = array_map('trim', $parts);
			$this->config['post-type'] = array_merge($this->config['post-type'], $parts);
		}
	}

	public function add_meta_boxes()
	{
		foreach ($this->config['post-type'] as $screen) {
			add_meta_box(
				sanitize_title($this->config['title']),
				$this->config['title'],
				[$this, 'add_meta_box_callback'],
				$screen,
				$this->config['context'],
				$this->config['priority']
			);
		}
	}

	public function admin_enqueue_scripts()
	{
		global $typenow;
		if (in_array($typenow, $this->config['post-type'])) {
			wp_enqueue_media();
		}
	}

	public function admin_head()
	{
		global $typenow;
		if (in_array($typenow, $this->config['post-type'])) {
			?>
			<script>
				jQuery.noConflict();
				(function ($) {
					$(function () {
						$('body').on('click', '.rwp-media-toggle', function (e) {
							e.preventDefault();
							let button = $(this);
							let rwpMediaUploader = null;
							rwpMediaUploader = wp.media({
								title: button.data('modal-title'),
								button: {
									text: button.data('modal-button')
								},
								multiple: true
							}).on('select', function () {
								let attachment = rwpMediaUploader.state().get('selection').first().toJSON();
								button.prev().val(attachment[button.data('return')]);
							}).open();
						});
					});
				})(jQuery);
			</script>
			<?php
		}
	}

	public function save_post($post_id)
	{
		foreach ($this->config['fields'] as $field) {
			switch ($field['type']) {
				case 'editor':
					if (isset($_POST[$field['id']])) {
						$sanitized = wp_filter_post_kses($_POST[$field['id']]);
						update_post_meta($post_id, $field['id'], $sanitized);
					}
					break;
				default:
					if (isset($_POST[$field['id']])) {
						$sanitized = sanitize_text_field($_POST[$field['id']]);
						update_post_meta($post_id, $field['id'], $sanitized);
					}
			}
		}
	}

	public function add_meta_box_callback()
	{
		$this->fields_table();
	}

	private function fields_table()
	{
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<?php
				foreach ($this->config['fields'] as $field) {
					?>
					<tr>
						<th scope="row">
							<?php $this->label($field); ?>
						</th>
						<td>
							<?php $this->field($field); ?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}

	private function label($field)
	{
		switch ($field['type']) {
			case 'radio':
				echo '<div class="">' . $field['label'] . '</div>';
				break;
			case 'editor':
				echo '<div class="">' . $field['label'] . '</div>';
				break;
			case 'media':
				printf(
					'<label class="" for="%s_button">%s</label>',
					$field['id'], $field['label']
				);
				break;
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}

	function generate_post_select($field)
	{
		// echo "<pre>";print_r($field);exit;
		$post_type_object = get_post_type_object($field['cptname']);
		$label = $post_type_object->label;
		$value = $this->value($field);
		$posts = get_posts(array('post_type' => $field['cptname'], 'post_status' => 'publish', 'suppress_filters' => false, 'posts_per_page' => -1));
		echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
		echo '<option value = "" >All ' . $label . ' </option>';
		foreach ($posts as $post) {
			echo '<option value="', $post->ID, '"', $value == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
		}
		echo '</select>';
	}

	private function field($field)
	{
		switch ($field['type']) {
				case 'radio':
				$this->radio( $field );
				break;
			case 'select':
				$this->select( $field );
				break;
			case 'editor':
				$this->editor($field);
				break;
			case 'media':
				$this->input($field);
				$this->media_button($field);
				break;
			case 'textarea':
				$this->textarea($field);
				break;
			default:
				$this->input($field);
		}
	}
	private function radio( $field ) {
		printf(
			'<fieldset><legend class="screen-reader-text">%s</legend>%s</fieldset>',
			$field['label'],
			$this->radio_options( $field )
		);
	}
	private function radio_checked( $field, $current ) {
		$value = $this->value( $field );
		if ( $value === $current ) {
			return 'checked';
		}
		return '';
	}
	private function radio_options( $field ) {
		$output = '';
		$options = [
			"html" => "HTML Formation",
			"plain" => "Plain Text",
		];

		foreach ( $options as $value => $label ) {
			$output .= sprintf(
				'<label><input %s id="%s-%s" name="%s" type="radio" value="%s"> %s</label><br>',
				$this->radio_checked( $field, $value ),
				$field['id'], $value, $field['id'],
				$value, $label
			);
		}

		return $output;
	}

	private function editor($field)
	{
		wp_editor($this->value($field), $field['id'], [
			'wpautop' => isset($field['wpautop']) ? true : false,
			'media_buttons' => isset($field['media-buttons']) ? true : false,
			'textarea_name' => $field['id'],
			'textarea_rows' => isset($field['rows']) ? isset($field['rows']) : 20,
			'teeny' => isset($field['teeny']) ? true : false
		]);
	}
	private function input($field)
	{
		if ($field['type'] === 'media') {
			$field['type'] = 'text';
		}
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
			isset($field['class']) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset($field['pattern']) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value($field)
		);
	}

	private function select( $field ) {
		printf(
			'<select id="%s" name="%s">%s</select>',
			$field['id'], $field['id'],
			$this->select_options( $field )
		);
	}

	private function select_selected( $field, $current ) {
		$value = $this->value( $field );
		if ( $value === $current ) {
			return 'selected';
		}
		return '';
	}
	// private function select_options( $field ) {
	// 	$output = [];
	// 	// $options = explode( "\r\n", $field['options'] );
	// 	$options = [
	// 		"option_key_1" => "Option Label 1",
	// 		"option_key_2" => "Option Label 2",
	// 		"option_key_3" => "Option Label 3",
	// 	];
	// 	$i = 0;
	// 	foreach ( $options as $option ) {
	// 		$pair = explode( ':', $option );
	// 		$pair = array_map( 'trim', $pair );
	// 		$output[] = sprintf(
	// 			'<option %s value="%s"> %s</option>',
	// 			$this->select_selected( $field, $pair[0] ),
	// 			$pair[0], $pair[1]
	// 		);
	// 		$i++;
	// 	}
	// 	return implode( '<br>', $output );
	// }
	private function select_options( $field ) {
		$output = '';
		$options = [
			"new-registration-client" => "New Registration - For User",
			"new-registration" => "New Registration - For Admin",
			"forgot-password" => "Forgot Password - For User"
		];

		foreach ( $options as $value => $label ) {
			$output .= sprintf(
				'<option %s value="%s"> %s</option>',
				$this->select_selected( $field, $value ),
				$value, $label
			);
		}

		return $output;
	}

	private function media_button($field)
	{
		printf(
			' <button class="button rwp-media-toggle" data-modal-button="%s" data-modal-title="%s" data-return="%s" id="%s_button" name="%s_button" type="button">%s</button>',
			isset($field['modal-button']) ? $field['modal-button'] : __('Select this file', 'stone-domain'),
			isset($field['modal-title']) ? $field['modal-title'] : __('Choose a file', 'stone-domain'),
			$field['return'],
			$field['id'], $field['id'],
			isset($field['button-text']) ? $field['button-text'] : __('Upload', 'stone-domain')
		);
	}

	private function textarea($field)
	{
		printf(
			'<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
			$field['id'], $field['id'],
			isset($field['rows']) ? $field['rows'] : 5,
			$this->value($field)
		);
	}

	private function value( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

}


new mails;
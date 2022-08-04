<?php

if (!defined('ABSPATH'))
{
	die();
}

?>
<div id="opening-hours-settings" class="opening-hours wrap banner closed">
	<h1><?php esc_html_e('We’re Open!', 'opening-hours'); ?></h1>
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
        <a href="#setup" class="nav-tab<?php echo ($this->section == NULL) ? ' nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('Setup', 'opening-hours'); ?></a>

        <a href="#separators" class="nav-tab<?php echo ($this->section == 'separators') ? ' nav-tab-active' : ''; ?>"><span class="dashicons dashicons-controls-forward"></span> <?php esc_html_e('Separators &amp; Text', 'opening-hours'); ?></a>

        <a href="#shortcodes" class="nav-tab<?php echo ($this->section == 'shortcodes') ? ' nav-tab-active' : ''; ?>"><span class="dashicons dashicons-editor-code"></span> <?php esc_html_e('Shortcodes', 'opening-hours'); ?></a>

        <a href="#additional" class="nav-tab<?php echo ($this->section == 'additional') ? ' nav-tab-active' : ''; ?>"><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e('Additional', 'opening-hours'); ?></a>

        <a href="#about" class="nav-tab<?php echo ($this->section == 'about') ? ' nav-tab-active' : ''; ?>"><span class="dashicons dashicons-heart"></span> <?php esc_html_e('About', 'opening-hours'); ?></a>
    </nav>

    <div id="setup" class="section<?php echo (($this->section != NULL) ? ' hide' : ''); ?>"<?php echo ($this->data_hunter('test')) ? ' data-hunter="' . esc_attr($this->data_hunter('json')) . '"' : ''; ?>>
        <form method="post" action="options.php" id="open-setup">
            <h2><?php esc_html_e('General', 'opening-hours'); ?></h2>
            <table class="form-table general">
<?php if (get_option('we_are_open_time_format') == NULL || get_option('we_are_open_day_format') == NULL) : ?>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Timezone', 'opening-hours'); ?></label></th>
                    <td>
                        <p>
                        	<input type="text" id="timezone" class="regular-text" name="we_are_open_timezone" value="<?php echo esc_html(get_option('timezone_string')); ?>" placeholder="<?php esc_attr_e('None', 'opening-hours'); ?>" readonly> <a class="button" href="<?php echo esc_attr(admin_url('options-general.php#timezone_string')); ?>"><?php esc_html_e('Change', 'opening-hours'); ?></a>
                        </p>
                        <p class="description"><?php /* translators: %s: URL and bookmark to alter the main Timezone Settings */
						echo sprintf(__('Please ensure this is set correctly in the <a href="%s">General Settings</a> to avoid unexpected results.', 'opening-hours'), esc_attr(admin_url('options-general.php#timezone_string'))); ?></p>
                    </td>
                </tr>
<?php endif; ?>
              <tr>
                <th scope="row"><label for="time-format"><?php esc_html_e('Time Format', 'opening-hours'); ?></label></th>
                <td class="<?php echo (get_option('we_are_open_time_type') == 12) ? 'hours-12' : 'hours-24'; ?>">
                    <select id="time-format" name="we_are_open_time_format" required>
                        <option value=""<?php echo (get_option('we_are_open_time_format') == NULL) ? ' selected' : ' disabled'; ?>><?php esc_html_e('Select', 'opening-hours'); ?></option>
                        <optgroup label="<?php esc_attr_e('12 Hour', 'opening-hours'); ?>">
<?php foreach ($this->time_formats as $k => $a) : ?>
<?php if ($k == '24_none'): ?>
						</optgroup>
                        <optgroup label="<?php esc_attr_e('24 Hour', 'opening-hours'); ?>">
<?php endif; ?>
                            <option value="<?php echo esc_attr($k); ?>" class="<?php echo (preg_match('/^g/', $a[1])) ? 'hours-12' : 'hours-24'; ?>" data-php="<?php echo esc_attr($a[1]); ?>" data-initial="<?php echo esc_attr($a[0]); ?>"<?php echo (get_option('we_are_open_time_format') == $k) ? ' selected' : ''; ?>><?php echo esc_html($a[0]); ?></option>
<?php endforeach; ?>
						</optgroup>
                    </select>
					<label class="hours-12" for="time-type-12"><input type="radio" id="time-type-12" name="we_are_open_time_type" value="12"<?php echo (get_option('we_are_open_time_type') != 24) ? ' checked="checked"' : ''; ?>> <?php esc_html_e('12 Hour', 'opening-hours'); ?></label>
					<label class="hours-24" for="time-type-24"><input type="radio" id="time-type-24" name="we_are_open_time_type" value="24"<?php echo (get_option('we_are_open_time_type') == 24) ? ' checked="checked"' : ''; ?>> <?php esc_html_e('24 Hour', 'opening-hours'); ?></label>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="day-format"><?php esc_html_e('Day Format', 'opening-hours'); ?></label></th>
                <td>
                    <select id="day-format" name="we_are_open_day_format" required>
                        <option value=""<?php echo (get_option('we_are_open_day_format') == NULL) ? ' selected' : ' disabled'; ?>><?php esc_html_e('Regular Days', 'opening-hours'); ?></option>
                        <optgroup label="<?php esc_attr_e('Day Names', 'opening-hours'); ?>">
<?php foreach ($this->day_formats as $k => $a) : ?> 
<?php if ($k == 'short_date_short_month'): ?>
						</optgroup>
                        <optgroup label="<?php esc_attr_e('Short Month', 'opening-hours'); ?>">
<?php elseif ($k == 'full_date'): ?>
						</optgroup>
                        <optgroup label="<?php esc_attr_e('Full Month', 'opening-hours'); ?>">
<?php endif; ?>
							<option value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_day_format') == $k) ? ' selected' : ''; ?>><?php echo esc_html(((is_numeric($a[2])) ? substr($a[0], 0, $a[2]) : $a[0])); ?></option>
<?php endforeach; ?>
						</optgroup>
                    </select>
                    <select id="day-format-special" name="we_are_open_day_format_special">
                        <option value="" disabled><?php esc_html_e('Special Days', 'opening-hours'); ?></option>
                        <option value=""<?php echo (get_option('we_are_open_day_format_special') == NULL) ? ' selected' : ''; ?>><?php esc_html_e('Same as Regular Days', 'opening-hours'); ?></option>
                        <optgroup label="<?php esc_attr_e('Day Names', 'opening-hours'); ?>">
<?php foreach ($this->day_formats as $k => $a) : ?>
<?php if ($k == 'short_date_short_month'): ?>
						</optgroup>
                        <optgroup label="<?php esc_attr_e('Short Month', 'opening-hours'); ?>">
<?php elseif ($k == 'full_date'): ?>
						</optgroup>
                        <optgroup label="<?php esc_attr_e('Full Month', 'opening-hours'); ?>">
<?php endif; ?>
							<option value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_day_format_special') == $k) ? ' selected' : ''; ?>><?php echo esc_html(((is_numeric($a[2])) ? substr($a[0], 0, $a[2]) : $a[0])); ?></option>
<?php endforeach; ?>
						</optgroup>
                    </select>
                </td>
              </tr>
              <tr>
                <th scope="row"><label><?php esc_html_e('Start of Week', 'opening-hours'); ?></label></th>
                <td>
                	<p>
<?php foreach ($this->days as $k => $v) : ?>
                        <label><input type="radio" id="<?php echo esc_attr('week-start-' . $k); ?>" name="we_are_open_week_start" value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_week_start') == $k) ? ' checked' : ''; ?>> <?php echo esc_html($v); ?></label>
<?php endforeach; ?>
                        <label><input type="radio" id="week-start-yesterday" name="we_are_open_week_start" value="-2"<?php echo (get_option('we_are_open_week_start') == -2) ? ' checked' : ''; ?>> <?php esc_html_e('Yesterday', 'opening-hours'); ?></label>
                        <label><input type="radio" id="week-start-today" name="we_are_open_week_start" value="-1"<?php echo (get_option('we_are_open_week_start') == -1) ? ' checked' : ''; ?>> <?php esc_html_e('Today', 'opening-hours'); ?></label>
					</p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label><?php esc_html_e('Weekdays', 'opening-hours'); ?></label></th>
                <td>
                	<p>
<?php foreach ($this->days as $k => $v) : ?>
                        <label><input type="checkbox" id="<?php echo esc_attr('weekdays-' . $k); ?>" name="weekdays-options[]" value="<?php echo esc_attr($k); ?>"<?php echo (is_array(get_option('we_are_open_weekdays')) && in_array($k, get_option('we_are_open_weekdays'))) ? ' checked' : ''; ?>> <?php echo esc_html($v); ?></label>
<?php endforeach; ?>
					<input type="hidden" id="weekdays" name="we_are_open_weekdays" value="<?php echo esc_attr((is_array(get_option('we_are_open_weekdays'))) ? preg_replace('/[^,\s\d]+/', '0', implode(',', get_option('we_are_open_weekdays'))) : preg_replace('/[^,\s\d]+/', '0', get_option('we_are_open_weekdays'))); ?>">
					</p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label><?php esc_html_e('Weekend', 'opening-hours'); ?></label></th>
                <td>
                	<p>
<?php foreach ($this->days as $k => $v) : ?>
                	<label><input type="checkbox" id="<?php echo esc_attr('weekend-' . $k); ?>" name="weekend-options[]" value="<?php echo esc_attr($k); ?>"<?php echo (is_array(get_option('we_are_open_weekend')) && in_array($k, get_option('we_are_open_weekend'))) ? ' checked' : ''; ?>> <?php echo esc_html($v); ?></label>
<?php endforeach; ?>
					<input type="hidden" id="weekend" name="we_are_open_weekend" value="<?php echo esc_attr((is_array(get_option('we_are_open_weekend'))) ? preg_replace('/[^,\s\d]+/', '0', implode(',', get_option('we_are_open_weekend'))) : preg_replace('/[^,\s\d]+/', '0', get_option('we_are_open_weekend'))); ?>">
                    </p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label><?php esc_html_e('Consolidation', 'opening-hours'); ?></label></th>
                <td>
                	<p>
<?php foreach ($this->consolidation_types as $k => $v) : ?>
                        <label><input type="radio" id="<?php echo esc_attr('consolidation-' . $k); ?>" name="we_are_open_consolidation" value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_consolidation') == $k) ? ' checked' : ''; ?>> <?php echo esc_html($v); ?></label>
<?php endforeach; ?>
					</p>
</td>
              </tr>
              <tr>
                <th scope="row"><label for="closed-show"><?php esc_html_e('Closed Days', 'opening-hours'); ?></label></th>
                <td><label><input type="checkbox" id="closed-show" name="we_are_open_closed_show" value="1"<?php echo (get_option('we_are_open_closed_show')) ? ' checked' : ''; ?>> <?php esc_html_e('Show closed days', 'opening-hours'); ?></label></td>
              </tr>
              <tr>
                <th scope="row"><label for="stylesheet"><?php esc_html_e('Style Sheet', 'opening-hours'); ?></label></th>
                <td><label><input type="checkbox" id="stylesheet" name="we_are_open_stylesheet" value="1"<?php echo ((is_bool(get_option('we_are_open_stylesheet')) && get_option('we_are_open_stylesheet') || is_numeric(get_option('we_are_open_stylesheet')) && get_option('we_are_open_stylesheet') > 0 || is_string(get_option('we_are_open_stylesheet')) && get_option('we_are_open_stylesheet') != NULL) ? ' checked="checked"' : ''); ?>> <?php esc_html_e('Load style sheet', 'opening-hours'); ?></label></td>
              </tr>
              <tr>
                <th scope="row"><label for="javascript"><?php esc_html_e('JavaScript', 'opening-hours'); ?></label></th>
                <td><label><input type="checkbox" id="javascript" name="we_are_open_javascript" value="1"<?php echo ((is_bool(get_option('we_are_open_javascript', TRUE)) && get_option('we_are_open_javascript', TRUE) || is_numeric(get_option('we_are_open_javascript')) && get_option('we_are_open_javascript') > 0 || is_string(get_option('we_are_open_javascript')) && get_option('we_are_open_javascript') != NULL) ? ' checked="checked"' : ''); ?>> <?php esc_html_e('Load JavaScript', 'opening-hours'); ?></label></td>
              </tr>
            </table>

            <h2><?php esc_html_e('Structured Data', 'opening-hours'); ?></h2>
            <p><?php /* translators: %s: refers to Schema URL and name, leave unchanged */ 
				echo sprintf(__('Allow search engines to easily read review data for your website using Structured Data %s which includes general business information and all regular and special opening hours.', 'opening-hours'), '(<a href="//schema.org" class="components-external-link" target="_blank">Schema.org</a>)'); ?></p>
            <table class="form-table structured-data-table">
                <tr>
                    <th scope="row"><label for="structured-data"><?php esc_html_e('Structured Data', 'opening-hours'); ?></label></th>
                    <td>
                        <p>
                            <label for="structured-data"><input type="checkbox" id="structured-data" name="we_are_open_structured_data" value="<?php echo esc_attr((is_numeric(get_option('we_are_open_structured_data')) && get_option('we_are_open_structured_data') >= 1) ? get_option('we_are_open_structured_data') : 1); ?>"<?php echo (intval(get_option('we_are_open_structured_data')) >= 1) ? ' checked="checked"' : ''; ?>> <?php esc_html_e('Enable and insert Structured Data on the front page.', 'opening-hours'); ?></label>
                            <button type="button" name="structured-data-preview" id="structured-data-preview" class="button button-secondary structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>><span class="dashicons dashicons-text-page"></span> <?php esc_html_e('Preview', 'opening-hours'); ?></button>
						</p>
                    </td>
                </tr>
                <tr class="structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>>
                    <th scope="row"><label for="name"><?php esc_html_e('Name', 'opening-hours'); ?></label></th>
                    <td>
                        <input type="text" id="name" class="regular-text" name="we_are_open_name" value="<?php echo esc_attr(get_option('we_are_open_name')); ?>">
                    </td>
                </tr>
                <tr class="structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>>
                    <th scope="row"><label for="address"><?php esc_html_e('Address', 'opening-hours'); ?></label></th>
                    <td>
                        <p class="input">
                       		<textarea id="address" class="regular-text" name="we_are_open_address"><?php echo esc_html(get_option('we_are_open_address')); ?></textarea>
                        </p>
                        <p class="description">
							<?php esc_html_e('For a well-formatted postal address, separate by new lines as follows: street address, city, state, postcode and two letter country code.', 'opening-hours'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>>
                    <th scope="row"><label for="telephone"><?php esc_html_e('Telephone', 'opening-hours'); ?></label></th>
                    <td>
                        <input type="tel" id="telephone" class="regular-text" name="we_are_open_telephone" value="<?php echo esc_attr(get_option('we_are_open_telephone')); ?>">
                    </td>
                </tr>
                <tr class="structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>>
                    <th scope="row"><label for="business-type"><?php esc_html_e('Business Type', 'opening-hours'); ?></label></th>
                    <td>
                        <select id="business-type" name="we_are_open_business_type">
                            <optgroup label="<?php esc_attr_e('Local Business', 'opening-hours'); ?>" data-type="LocalBusiness">
                                <option value=""<?php echo (get_option('we_are_open_business_type') == NULL) ? ' selected' : ''; ?>><?php esc_html_e('Not Applicable/Other', 'opening-hours'); ?></option>
<?php
	foreach ($this->business_types as $k => $name)
	{
?>
                                <option value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_business_type') == $k) ? ' selected' : ''; ?>><?php echo esc_attr($name); ?></option>
<?php
	}
?>
							</optgroup>
                            <optgroup label="<?php esc_attr_e('Airline', 'opening-hours'); ?>" data-type="Airline">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Consortium', 'opening-hours'); ?>" data-type="Consortium">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Corporation', 'opening-hours'); ?>" data-type="Corporation">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Educational Organization', 'opening-hours'); ?>" data-type="EducationalOrganization">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Funding Scheme', 'opening-hours'); ?>" data-type="FundingScheme">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Government Organization', 'opening-hours'); ?>" data-type="GovernmentOrganization">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Library System', 'opening-hours'); ?>" data-type="LibrarySystem">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Medical Organization', 'opening-hours'); ?>" data-type="MedicalOrganization">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('NGO', 'opening-hours'); ?>" data-type="NGO">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('News Media Organization', 'opening-hours'); ?>" data-type="NewsMediaOrganization">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Performing Group', 'opening-hours'); ?>" data-type="PerformingGroup">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Project', 'opening-hours'); ?>" data-type="Project">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Sports Organization', 'opening-hours'); ?>" data-type="SportsOrganization">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Workers Union', 'opening-hours'); ?>" data-type="WorkersUnion">
                                <option value="" disabled><?php esc_html_e('Structured Data Not Available', 'opening-hours'); ?></option>
                            </optgroup>
                        </select>
                    </td>
                </tr>
                <tr class="structured-data"<?php echo (get_option('we_are_open_structured_data') ? '' : ' style="display: none"'); ?>>
                    <th scope="row"><label for="price-range"><?php esc_html_e('Price Range', 'opening-hours'); ?></label></th>
                    <td>
                        <select id="price-range" name="we_are_open_price_range">
                            <option value=""<?php echo (get_option('we_are_open_price_range') == NULL) ? ' selected' : ''; ?>><?php esc_attr_e('Not Applicable', 'opening-hours'); ?></option>
<?php foreach ($this->price_ranges as $k => $a) : ?>
                            <option value="<?php echo esc_attr($k); ?>"<?php echo (get_option('we_are_open_price_range') == $k) ? ' selected' : ''; ?>><?php echo esc_attr($a['name']); ?></option>
<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr id="logo-image-row" class="structured-data<?php echo ((get_option('we_are_open_logo') == NULL) ? ' empty' : ''); ?>"<?php echo ((get_option('we_are_open_structured_data') ? '' : ' style="display: none"')); ?>>
                    <th scope="row"><?php esc_html_e('Logo', 'opening-hours'); ?></th>
                    <td>
                        <p class="logo-image<?php echo (get_option('we_are_open_logo') == NULL) ? ' empty' : ''; ?>">
                            <span id="logo-image-preview" class="image thumbnail"><?php echo (get_option('we_are_open_logo') != NULL) ? preg_replace('/\s+(?:width|height)="\d*"/i', '', wp_get_attachment_image($this->logo_image_id, 'large')) : ''; ?></span>
                            <span class="set"><button type="button" id="logo-image" class="button button-secondary ui-button" name="logo-image" value="1" data-set-text="<?php esc_attr_e('Choose Image', 'opening-hours'); ?>" data-replace-text="<?php esc_attr_e('Replace', 'opening-hours'); ?>"><span class="dashicons dashicons-format-image"></span> <?php echo (get_option('we_are_open_logo') == NULL) ? esc_attr__('Choose Image', 'opening-hours') : esc_attr__('Replace', 'opening-hours'); ?></button></span>
                            <span class="delete"<?php echo (get_option('we_are_open_logo') == NULL) ? ' style="display: none;"' : ''; ?>><button type="button" id="logo-image-delete" class="button button-secondary ui-button" name="logo-image-delete" value="1"><span class="dashicons dashicons-no"></span> Remove</button></span>
                            <input type="hidden" id="logo-image-id" name="we_are_open_logo" value="<?php echo esc_attr($this->logo_image_id); ?>">
                        </p>
                    </td>
                </tr>
            </table>
<?php
	settings_fields('we_are_open_settings');
	do_settings_sections('we_are_open_settings');
?>
			<?php submit_button(); ?>
            <p class="opening-hours-management">
				<a href="<?php echo admin_url('admin.php?page=opening_hours'); ?>" class="button ui-button button-secondary"><?php esc_html_e('Opening Hours', 'opening-hours'); ?></a>
			</p>
		</form>
    </div>
    
    <div id="separators" class="section<?php echo (($this->section != 'separators') ? ' hide' : ''); ?>">
        <form method="post" action="options.php" id="open-settings-separators">
            <h2><?php esc_html_e('Separators &amp; Text', 'opening-hours'); ?></h2>
            <table class="form-table separators-text">
              <tr>
                <th scope="row"><label for="time-separator"><?php esc_html_e('Time Separator', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="time-separator" class="small-text" name="we_are_open_time_separator" value="<?php echo esc_attr(get_option('we_are_open_time_separator')); ?>" required>
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_time_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space"><span class="dashicons <?php echo preg_match('/^.*\s+$/', get_option('we_are_open_time_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('The characters or word to separate the open and close times.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="time-group-separator"><?php esc_html_e('Time Group Separator', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="time-group-separator" class="small-text" name="we_are_open_time_group_separator" value="<?php echo esc_attr(get_option('we_are_open_time_group_separator')); ?>">
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_time_group_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space"><span class="dashicons <?php echo preg_match('/^.*\s+$/', get_option('we_are_open_time_group_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('The characters or word to separate groups of opening times in a given day; optionally use | to use separate first and last characters or words.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="day-separator"><?php esc_html_e('Individual Day Separator', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="day-separator" class="small-text" name="we_are_open_day_separator" value="<?php echo esc_attr(get_option('we_are_open_day_separator')); ?>" required>
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_day_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space"><span class="dashicons <?php echo preg_match('/^.*\s+$/', get_option('we_are_open_day_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('When consolidated, the characters or word that separates individual days as a list; optionally use | to use separate first and last characters or words.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="day-range-separator"><?php esc_html_e('Day Range Separator', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="day-range-separator" class="small-text" name="we_are_open_day_range_separator" value="<?php echo esc_attr(get_option('we_are_open_day_range_separator')); ?>" required>
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_day_range_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space"><span class="dashicons <?php echo preg_match('/^.*\s+$/', get_option('we_are_open_day_range_separator')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('When consolidated, the characters or word that separates the first and last day of the range.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="day-range-suffix"><?php esc_html_e('Day Suffix', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="day-range-suffix" class="small-text" name="we_are_open_day_range_suffix" value="<?php echo esc_attr(get_option('we_are_open_day_range_suffix')); ?>" required>
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_day_range_suffix')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space disabled"><span class="dashicons dashicons-marker"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('The optional overall suffix character for the day, date or list of days such as a colon character.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="day-range-suffix-special"><?php esc_html_e('Day Suffix for Special Days', 'opening-hours'); ?></label></th>
                <td class="leading-trailing-spaces">
                	<input type="text" id="day-range-suffix-special" class="small-text" name="we_are_open_day_range_suffix_special" value="<?php echo esc_attr(get_option('we_are_open_day_range_suffix_special')); ?>" required>
                	<span class="leading-space"><span class="dashicons <?php echo preg_match('/^\s+.*$/', get_option('we_are_open_day_range_suffix_special')) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Leading Space', 'opening-hours'); ?></span>
                	<span class="trailing-space disabled"><span class="dashicons dashicons-marker"></span> <?php _e('Trailing Space', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('The optional overall suffix character for special days or dates if the format varies.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr id="weekdays-text-row">
                <th scope="row"><label for="weekdays-text"><?php esc_html_e('Weekdays Text', 'opening-hours'); ?></label></th>
                <td class="value-text-suffix-empty">
                	<input type="text" id="weekdays-text" class="small-text" name="we_are_open_weekdays_text" value="<?php echo esc_attr(get_option('we_are_open_weekdays_text')); ?>">
                	<span class="value-text"><span class="dashicons <?php echo (get_option('we_are_open_weekdays_text') != NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Text', 'opening-hours'); ?></span>
                	<span class="value-suffix action"><span class="dashicons <?php echo (preg_match('/^.+' . preg_quote(get_option('we_are_open_day_range_suffix'), '/') . '$/i', get_option('we_are_open_weekdays_text'))) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Suffix', 'opening-hours'); ?></span>
                	<span class="value-empty action"><span class="dashicons <?php echo (get_option('we_are_open_weekdays_text') == NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Day Names', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('Text to replace all individual weekdays when consolidated. Leave blank to avoid summary word.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr id="weekend-text-row">
                <th scope="row"><label for="weekend-text"><?php esc_html_e('Weekend Text', 'opening-hours'); ?></label></th>
                <td class="value-text-suffix-empty">
                	<input type="text" id="weekend-text" class="small-text" name="we_are_open_weekend_text" value="<?php echo esc_attr(get_option('we_are_open_weekend_text')); ?>">
                	<span class="value-text"><span class="dashicons <?php echo (get_option('we_are_open_weekend_text') != NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Text', 'opening-hours'); ?></span>
                	<span class="value-suffix action"><span class="dashicons <?php echo (preg_match('/^.+' . preg_quote(get_option('we_are_open_day_range_suffix'), '/') . '$/i', get_option('we_are_open_weekend_text'))) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Suffix', 'opening-hours'); ?></span>
                	<span class="value-empty action"><span class="dashicons <?php echo (get_option('we_are_open_weekend_text') == NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Day Names', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('Text to replace all individual days in the weekend when consolidated. Leave blank to avoid summary word.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr id="everyday-text-row">
                <th scope="row"><label for="everyday-text"><?php esc_html_e('Everyday Text', 'opening-hours'); ?></label></th>
                <td class="value-text-suffix-empty">
                	<input type="text" id="everyday-text" class="small-text" name="we_are_open_everyday_text" value="<?php echo esc_attr(get_option('we_are_open_everyday_text')); ?>">
                	<span class="value-text"><span class="dashicons <?php echo (get_option('we_are_open_everyday_text') != NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Text', 'opening-hours'); ?></span>
                	<span class="value-suffix action"><span class="dashicons <?php echo (preg_match('/^.+' . preg_quote(get_option('we_are_open_day_range_suffix'), '/') . '$/i', get_option('we_are_open_everyday_text'))) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Suffix', 'opening-hours'); ?></span>
                	<span class="value-empty action"><span class="dashicons <?php echo (get_option('we_are_open_everyday_text') == NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Day Names', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('Text to replace all individual days when consolidated. Leave blank to avoid summary word.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="hours-24-text"><?php esc_html_e('24 Hours Text', 'opening-hours'); ?></label></th>
                <td class="value-text-empty">
                	<input type="text" id="hours-24-text" class="small-text" name="we_are_open_24_hours_text" value="<?php echo esc_attr(get_option('we_are_open_24_hours_text')); ?>" required>
                	<span class="value-text"><span class="dashicons <?php echo (get_option('we_are_open_day_range_suffix_special') != NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Text', 'opening-hours'); ?></span>
                	<span class="value-empty action"><span class="dashicons <?php echo (get_option('we_are_open_day_range_suffix_special') == NULL) ? ' dashicons-yes-alt' : ' dashicons-marker'; ?>"></span> <?php _e('Display Hours', 'opening-hours'); ?></span>
                    <p class="description"><?php esc_html_e('Text to display when open for a full 24 hours in a day. Leave blank to use equivalent time range.', 'opening-hours'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="closed-text"><?php esc_html_e('Closed Text', 'opening-hours'); ?></label></th>
                <td>
                	<input type="text" id="closed-text" class="small-text" name="we_are_open_closed_text" value="<?php echo esc_attr(get_option('we_are_open_closed_text')); ?>" required>
                    <p class="description"><?php esc_html_e('Text to display when there are no open hours for a given day.', 'opening-hours'); ?></p>
                </td>
              </tr>
            </table>
            <p class="submit">
                <button type="button" name="submit-separators" id="separators-button" class="button button-primary"><?php esc_html_e('Save Changes', 'opening-hours'); ?></button>
            </p>
            <p class="opening-hours-management">
            	<a href="<?php echo admin_url('admin.php?page=opening_hours'); ?>" class="button ui-button button-secondary"><?php esc_html_e('Opening Hours', 'opening-hours'); ?></a>
            </p>
		</form>
    </div>
    
    <div id="shortcodes" class="section<?php echo ($this->section != 'shortcodes') ? ' hide' : ''; ?>">
        <form method="post" action="options.php" id="open-shortcodes">
	        <h2><?php esc_html_e('Shortcodes', 'opening-hours'); ?></h2>
            <p><?php /* translators: %s: URL for Shortcode Demonstration Website */
				echo sprintf(__('Here is a selection of starting points for the Shortcodes with some of the available parameters. You can find a more comprehensive set of examples in the <a href="%s" class="components-external-link" target="_blank">demonstration website</a>.', 'opening-hours'), 'https://demo.designextreme.com/were-open/'); ?></p>
            <table class="form-table">
                <tr id="shortcode-open">
                    <th><?php esc_html_e('Table of opening hours', 'opening-hours'); ?></th>
                    <td><input id="<?php $id = 0; echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open]" readonly></td>
                </tr>
                <tr id="shortcode-open-text">
                    <th><?php esc_html_e('Opening hours as continuous text', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open text]" readonly></td>
                </tr>
                <tr id="shortcode-open-lines">
                    <th><?php esc_html_e('List of opening hours with new lines', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open lines]" readonly></td>
                </tr>
                <tr id="shortcode-open-p">
                    <th><?php esc_html_e('List of opening hours with paragraphs', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open p]" readonly></td>
                </tr>
                <tr id="shortcode-open-ul">
                    <th><?php esc_html_e('List of opening hours as an unordered list', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open ul]" readonly></td>
                </tr>
                <tr id="shortcode-open-ol">
                    <th><?php esc_html_e('List of opening hours as an ordered list', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open ol]" readonly></td>
                </tr>
                <tr id="shortcode-open-conditional-open">
                    <th><?php esc_html_e('Conditional display if open', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[open_now]Show this when open[/open_now]" readonly></td>
                </tr>
                <tr id="shortcode-open-conditional-closed">
                    <th><?php esc_html_e('Conditional display if closed', 'opening-hours'); ?></th>
                    <td><input id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" type="text" value="[closed_now]Show this when closed[/closed_now]" readonly></td>
                </tr>
                <tr id="shortcode-open-text-replacement-codes">
                    <th><?php esc_html_e('Text with replacement codes', 'opening-hours'); ?></th>
                    <td><textarea id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" readonly><?php /* translators: Do not alter any words enclosed by percentage or square bracket characters: &#37; [ ] */
					_e('[open_text]&#37;if_open&#37; Our opening hours today are &#37;hours_today&#37; &#37;end&#37; &#37;if_closed&#37; We are currently closed &#37;if_open_tomorrow&#37; ,&#37;space&#37;but open again tomorrow at &#37;space&#37; &#37;tomorrow_start&#37; &#37;end&#37; &#37;end&#37;[/open_text]', 'opening-hours'); ?></textarea></td>
                </tr>
                <tr id="shortcode-open-text-open-closed">
                    <th><?php esc_html_e('Text showing open/closed status', 'opening-hours'); ?></th>
                    <td><textarea id="<?php echo 'shortcode-' . $id; $id++; ?>" name="shortcode[]" class="shortcode" readonly><?php /* translators: Do not alter any words enclosed by percentage or square bracket characters: &#37; [ ] */
					_e('[open_text]&#37;if_open_now&#37; We’re open! &#37;end&#37; &#37;if_closed_now&#37; Sorry, we’re closed. &#37;end&#37;[/open_text]', 'opening-hours'); ?></textarea></td>
                </tr>
            </table>
            
            <h2 id="parameters"><?php esc_html_e('Parameters', 'opening-hours'); ?></h2>
            <p><?php _e('There is a wide range of parameters that are accepted, so a guide will help cover all the possibilities to customize the output of your reviews, links and text. Shortcode parameters will override the values in the Setup. All parameters are optional.', 'opening-hours'); ?></p>
            <table class="wp-list-table widefat fixed striped parameters">
                <tr>
                    <th class="parameter"><?php esc_html_e('Parameter', 'opening-hours'); ?></th>
                    <th class="description"><?php esc_html_e('Description', 'opening-hours'); ?></th>
                    <th class="accepted"><?php esc_html_e('Accepted Values', 'opening-hours'); ?></th>
                    <th class="default"><?php esc_html_e('Default', 'opening-hours'); ?></th>
                    <th class="boolean"><abbr title="<?php esc_attr_e('Accepted for Opening Hours', 'opening-hours'); ?>"><?php esc_html_e('Opening Hours', 'opening-hours'); ?></abbr></th>
                    <th class="boolean"><abbr title="<?php esc_attr_e('Accepted for Conditional Shortcode', 'opening-hours'); ?>"><?php esc_html_e('Conditions', 'opening-hours'); ?></abbr></th>
                    <th class="boolean"><abbr title="<?php esc_attr_e('Accepted for Replacement Codes', 'opening-hours'); ?>"><?php esc_html_e('Replacement Codes', 'opening-hours'); ?></abbr></th>
                </tr>
                <tr id="parameter-day-format">
                    <td class="parameter">day_format</td>
                    <td class="description"><?php _e('Set your choice of format for the day of week.', 'opening-hours'); ?></td>
                    <td class="accepted"><?php echo esc_html(implode(', ', array_keys($this->day_formats))); ?></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-time-format">
                    <td class="parameter">time_format</td>
                    <td class="description"><?php _e('Set your choice of time format for each of the times.', 'opening-hours'); ?></td>
                    <td class="accepted"><?php echo esc_html(implode(', ', array_keys($this->time_formats))); ?></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-consolidation">
                    <td class="parameter">consolidation</td>
                    <td class="description"><?php _e('Set your choice of consolidation for some or all days of the week.', 'opening-hours'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <?php $consolidation_types = array_keys($this->consolidation_types); array_shift($consolidation_types); echo esc_html(implode(', ', $consolidation_types)); ?></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-time-separator">
                    <td class="parameter">time_separator</td>
                    <td class="description"><?php _e('The character or word used to separate each opening and closing times.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default">&quot; &ndash; &quot;</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-time-group-separator">
                    <td class="parameter">time_group_separator</td>
                    <td class="description"><?php _e('The character or word used to separate each group of opening and closing times. Spaces accepted; separate first and last groups with a pipe character (e.g. &quot;, | and &quot;).', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default">&quot;, &quot;</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-time-group-prefix">
                    <td class="parameter">time_group_prefix</td>
                    <td class="description"><?php _e('The character or word added to the start of each group of opening and closing times. Trailing spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-time-group-suffix">
                    <td class="parameter">time_group_suffix</td>
                    <td class="description"><?php _e('The character or word added to the end of each group of opening and closing times. Leading spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-days">
                    <td class="parameter">days</td>
                    <td class="description"><?php _e('Overwrite the default names of the days of the week. Must start with Sunday; include all seven days in order; comma separated list.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html('"' . implode(', ', $this->days) . '"'); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-separator">
                    <td class="parameter">day_separator</td>
                    <td class="description"><?php _e('The character or word used to separate individual days when consolidation is set. Spaces accepted; separate first and last groups with a pipe character (e.g. &quot;, | and &quot;).', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default">&quot;, &quot;</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-range-separator">
                    <td class="parameter">day_range_separator</td>
                    <td class="description"><?php _e('The character or word used to separate a range of days between the start and end days. Spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default">&quot; &ndash; &quot;</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-suffix">
                    <td class="parameter">day_suffix</td>
                    <td class="description"><?php _e('The suffix character for each individual day name or date. Leading spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-suffix-special">
                    <td class="parameter">day_suffix_special</td>
                    <td class="description"><?php _e('The suffix character for each individual day name or date for special opening hours if format varies from regular days. Leading spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-range-suffix">
                    <td class="parameter">day_range_suffix</td>
                    <td class="description"><?php _e('The suffix character added to the end of a day, date, list or range of days or dates (e.g. :). Leading spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-range-suffix-special">
                    <td class="parameter">day_range_suffix_special</td>
                    <td class="description"><?php _e('The suffix character added to the end of a special day, date, list or range of days or dates if format varies from regular days. Leading spaces accepted.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-range-min">
                    <td class="parameter">day_range_min</td>
                    <td class="description"><?php _e('Minimum number of days to apply ranges, rather than a list of days, for consolidated days. Set as empty (<span class="code">NULL</span>) to always list days without a range.', 'opening-hours'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, 2, 3, 4, &hellip;</td>
                    <td class="default">3</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-weekdays-word">
                    <td class="parameter">weekdays_text</td>
                    <td class="description"><?php _e('Set text to represent consolidated weekdays. Set as empty (<span class="code">NULL</span>) to only use day names.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html(get_option('we_are_open_weekdays_text')); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-weekend-text">
                    <td class="parameter">weekend_text</td>
                    <td class="description"><?php _e('Set text to represent a consolidated weekend. Set as empty (<span class="code">NULL</span>) to only use day names.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html(get_option('we_are_open_weekend_text')); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-everyday-text">
                    <td class="parameter">everyday_text</td>
                    <td class="description"><?php _e('Set text to represent everyday the same consolidated hours. Set as empty (<span class="code">NULL</span>) to only use day names.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html(get_option('we_are_open_everyday_text')); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-closed">
                    <td class="parameter">closed</td>
                    <td class="description"><?php _e('The text for when the business is closed.', 'opening-hours'); ?></td>
                    <td class="accepted"><em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html(get_option($this->prefix . 'closed_text', __('Closed', 'opening-hours'))); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-hours-24">
                    <td class="parameter">hours_24</td>
                    <td class="description"><?php _e('The text for when the business is open for 24 hours. Set as empty (<span class="code">NULL</span>) to revert to opening and closing times.', 'opening-hours'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <em><?php _e('Any string'); ?></em></td>
                    <td class="default"><?php echo esc_html(get_option($this->prefix . '24_hours_text', __('Open 24 Hours', 'opening-hours'))); ?></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-week-start">
                    <td class="parameter">week_start</td>
                    <td class="description"><?php _e('The numerical value of the week day from <span class="code">0</span> (Sunday) to <span class="code">6</span> (Saturday), today or yesterday.', 'opening-hours'); ?></td>
                    <td class="accepted">0, 1, 2, 3, 4, 5, 6, today, yesterday</td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-count">
                    <td class="parameter">count</td>
                    <td class="description"><?php _e('Overwrite the default seven-day number of days to cover both shorter and longer periods.', 'opening-hours'); ?></td>
                    <td class="accepted">1, 2, 3, &hellip; 31</td>
                    <td class="default">7</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-start">
                    <td class="parameter">start</td>
                    <td class="description"><?php _e('Specify a start date &mdash; useful for periods of special opening hours over holiday periods. Use a number for a relative date (<span class="code">0</span> = today).', 'opening-hours'); ?></td>
                    <td class="accepted"><?php /* translators: Date string format for parameter - cannot be changed */
						echo sprintf(__('<em>Date string: %s</em> or &hellip; -2, -1, 0, 1, 2, &hellip;', 'opening-hours'), 'YYYY/MM/DD'); ?></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-end">
                    <td class="parameter">end</td>
                    <td class="description"><?php _e('Specify an end date &mdash; useful for periods of special opening hours over holiday periods. Date is inclusive; no more than 31 days accepted between start and end dates if regular opening hours are included. Use a number for a relative date (<span class="code">0</span> = today).', 'opening-hours'); ?></td>
                    <td class="accepted"><?php /* translators: Date string format for parameter - cannot be changed */
						echo sprintf(__('<em>Date string: %s</em> or &hellip; -1, 0, 1, 2, 3, &hellip;', 'opening-hours'), 'YYYY/MM/DD'); ?></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-day-end">
                    <td class="parameter">day_end</td>
                    <td class="description"><?php _e('When a opening times are displayed as text, this character or word is at the end of the sentence (e.g. a period character). Set as empty (<span class="code">NULL</span>) to remove.', 'opening-hours'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <em><?php _e('Any string'); ?></em></td>
                    <td class="default">.</td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-regular">
                    <td class="parameter">regular</td>
                    <td class="description"><?php _e('Show or hide regular opening hours.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-special">
                    <td class="parameter">special</td>
                    <td class="description"><?php _e('Show or hide special opening hours.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-closed-show">
                    <td class="parameter">closed_show</td>
                    <td class="description"><?php _e('Show or hide days when business is closed.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-id">
                    <td class="parameter">id</td>
                    <td class="description"><?php _e('Set the ID attribute for main HTML element.', 'opening-hours'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <em><?php _e('Any valid string', 'opening-hours'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="explanation" title="<?php esc_attr_e('Only when update parameter is active'); ?>"><span class="dashicons dashicons-yes"></span>*</span></td>
                </tr>
                <tr id="parameter-class">
                    <td class="parameter">class</td>
                    <td class="description"><?php /* translators: bookmark HTML classes section */
						echo sprintf(__('Set the class attribute for main HTML element; <a href="%s">pre-styled classes available</a>. Separate multiple classes using spaces; not commas.', 'opening-hours'), '#classes'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <a href="#classes" title="<?php esc_attr_e('See HTML Classes', 'opening-hours'); ?>">dark, left, center, right, &hellip; no-border, outside-flush, current-line, past-fade</a>, <em><?php _e('Any valid string', 'opening-hours'); ?></em></td>
                    <td class="default"><span class="code">NULL</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="explanation" title="<?php esc_attr_e('Only when update parameter is active'); ?>"><span class="dashicons dashicons-yes"></span>*</span></td>
                </tr>
                <tr id="parameter-tag">
                    <td class="parameter">tag</td>
                    <td class="description"><?php /* translators: bookmark HTML classes section */
						echo sprintf(__('Set the tag attribute for main enclosing HTML element. Recommend specifying <em>span</em> for <a href="%1$s" class="components-external-link" target="_blank">phrasing content</a> and <em>div</em> for layout; set as empty (<span class="code">NULL</span>) to remove enclosing tag &mdash; this will result in loss of functionality.', 'opening-hours'), 'https://www.w3.org/TR/2011/WD-html5-author-20110809/content-models.html#phrasing-content-0'); ?></td>
                    <td class="accepted"><span class="code">NULL</span>, <em>Any valid tag</em></td>
                    <td class="default"><span class="explanation" title="<?php esc_attr_e('Conditional shortcode only: if contains only phrasing content, otherwise div is the default', 'opening-hours'); ?>">span*</span></td>
                    <td class="boolean"><span class="explanation" title="<?php esc_attr_e('Only for types: paragraph, lines and text'); ?>"><span class="dashicons dashicons-yes"></span>*</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="explanation" title="<?php esc_attr_e('Only when update parameter is active'); ?>"><span class="dashicons dashicons-yes"></span>*</span></td>
                </tr>
                <tr id="parameter-update">
                    <td class="parameter">update</td>
                    <td class="description"><?php _e('Set an AJAX request to reload content when there is either a change of day for opening hours or a change of status from closed to open or open to closed. Maximum time is 24 hours. Use <em>immediate</em> to circumvent caching by refreshing data immediately.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, on, off, immediate</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="parameter-reload">
                    <td class="parameter">reload</td>
                    <td class="description"><?php _e('Used with <a href="#parameter-update">update</a>, this will reload/refresh the page when there is either a change of day for opening hours or a change of status from closed to open or open to closed.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, on, off</td>
                    <td class="default"><span class="code">FALSE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="parameter-hide">
                    <td class="parameter">hide</td>
                    <td class="description"><?php _e('Adds the class: <em>hide</em> to prevent element from appearing when conditional shortcode is empty. Adds a corresponding, unstyled <em>show</em> class.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, on, off</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-remove-html">
                    <td class="parameter">remove_html</td>
                    <td class="description"><?php _e('Clear HTML content from conditional shortcode when it it doesn’t match current status of open or closed. Recommend <span class="code">FALSE</span> for large blocks of HTML when used with a class to hide element (i.e. <em>hide</em>).', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, on, off</td>
                    <td class="default"><span class="code">TRUE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-class-strip">
                    <td class="parameter">class_strip</td>
                    <td class="description"><?php _e('You may wish to remove all class attributes in HTML tags.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">FALSE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-span-strip">
                    <td class="parameter">span_strip</td>
                    <td class="description"><?php _e('You may wish to remove all &lt;span&gt; tags including their respective classes.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">FALSE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="parameter-shortcodes">
                    <td class="parameter">shortcodes</td>
                    <td class="description"><?php _e('Process shortcodes in the text or HTML content.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="explanation" title="<?php esc_attr_e('Except for replacement text with an active update', 'opening-hours'); ?>"><span class="code">TRUE</span>*</span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="explanation" title="<?php esc_attr_e('Only when update parameter is inactive'); ?>"><span class="dashicons dashicons-yes"></span>*</span></td>
                </tr>
                <tr id="parameter-errors">
                    <td class="parameter">errors</td>
                    <td class="description"><?php _e('You can choose to hide error notices. Defaults to <span class="code">WP_DEBUG</span> if defined in <em>wp-config.php</em>.', 'opening-hours'); ?></td>
                    <td class="accepted">yes, no, true, false, <span class="code">1</span>, <span class="code">0</span>, show, hide, on, off</td>
                    <td class="default"><span class="code">FALSE</span></td>
                    <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                    <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
            </table>

            <h2 id="replacement-codes"><?php esc_html_e('Replacement Codes', 'opening-hours'); ?></h2>
			<p><?php _e('When using a shortcode with content, you can use the following replacement codes to add conditions and variables.', 'opening-hours'); ?></p>
            <table class="wp-list-table widefat fixed striped replacement-codes">
                <tr>
                    <th class="replacement-code"><?php esc_html_e('Code', 'opening-hours'); ?></th>
                    <th class="description"><?php esc_html_e('Description', 'opening-hours'); ?></th>
                </tr>
                <tr id="code-if-open-now">
                    <td class="replacement-code">%if_open_now%</td>
                    <td class="description"><?php _e('Conditionally show text if open now.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-closed-now">
                    <td class="replacement-code">%if_closed_now%</td>
                    <td class="description"><?php _e('Conditionally show text if closed now.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-open-today">
                    <td class="replacement-code">%if_open_today%</td>
                    <td class="description"><?php _e('Conditionally show text if open once or more today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-open-later">
                    <td class="replacement-code">%if_open_later%</td>
                    <td class="description"><?php _e('Conditionally show text if closed now but open later today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-open-later">
                    <td class="replacement-code">%if_not_open_later%</td>
                    <td class="description"><?php _e('Conditionally show text if closed now and for the remainder of the day.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-closed-today">
                    <td class="replacement-code">%if_closed_today%</td>
                    <td class="description"><?php _e('Conditionally show text if closed all today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-open-tomorrow">
                    <td class="replacement-code">%if_open_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if open tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-closed-tomorrow">
                    <td class="replacement-code">%if_closed_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if closed tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-24-hours-today">
                    <td class="replacement-code">%if_24_hours_today%</td>
                    <td class="description"><?php _e('Conditionally show text if open 24 hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-24-hours-today">
                    <td class="replacement-code">%if_not_24_hours_today%</td>
                    <td class="description"><?php _e('Conditionally show text if not open specifically for 24 hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-24-hours-tomorrow">
                    <td class="replacement-code">%if_24_hours_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if open 24 hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-24-hours-tomorrow">
                    <td class="replacement-code">%if_not_24_hours_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if not open specifically for 24 hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-regular-today">
                    <td class="replacement-code">%if_regular_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there are regular opening hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-regular-today">
                    <td class="replacement-code">%if_not_regular_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there are no regular opening hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-special-today">
                    <td class="replacement-code">%if_special_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there are special opening hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-special-today">
                    <td class="replacement-code">%if_not_special_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there are no special opening hours today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-closure-today">
                    <td class="replacement-code">%if_closure_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there is a temporary closure today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-closure-today">
                    <td class="replacement-code">%if_not_closure_today%</td>
                    <td class="description"><?php _e('Conditionally show text if there is no temporary closure today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-regular-tomorrow">
                    <td class="replacement-code">%if_regular_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there are regular opening hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-regular-tomorrow">
                    <td class="replacement-code">%if_not_regular_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there are no regular opening hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-special-tomorrow">
                    <td class="replacement-code">%if_special_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there are special opening hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-special-tomorrow">
                    <td class="replacement-code">%if_not_special_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there are no special opening hours tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-closure-tomorrow">
                    <td class="replacement-code">%if_closure_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there is a temporary closure tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-if-not-closure-tomorrow">
                    <td class="replacement-code">%if_not_closure_tomorrow%</td>
                    <td class="description"><?php _e('Conditionally show text if there is no temporary closure tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-else">
                    <td class="replacement-code">%else%</td>
                    <td class="description"><?php /* translators: Preserve the word if where possible */
						_e('Conditionally show text if opposite to the previous <em>if</em> statement.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-end">
                    <td class="replacement-code">%end%</td>
                    <td class="description"><?php _e('End of the current logic, required for each opening condition.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-now">
                    <td class="replacement-code">%now%</td>
                    <td class="description"><?php _e('Formatted current time.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-today">
                    <td class="replacement-code">%today%</td>
                    <td class="description"><?php _e('Today’s day name.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-tomorrow">
                    <td class="replacement-code">%tomorrow%</td>
                    <td class="description"><?php _e('Tomorrow’s day name.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-hours-today">
                    <td class="replacement-code">%hours_today%</td>
                    <td class="description"><?php _e('Formatted list of hours for today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-hours-tomorrow">
                    <td class="replacement-code">%hours_tomorrow%</td>
                    <td class="description"><?php _e('Formatted list of hours for tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-today-start">
                    <td class="replacement-code">%today_start%</td>
                    <td class="description"><?php _e('Formatted first opening time for today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-today-end">
                    <td class="replacement-code">%today_end%</td>
                    <td class="description"><?php _e('Formatted last closing time for today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-today-next">
                    <td class="replacement-code">%today_next%</td>
                    <td class="description"><?php _e('Formatted next opening or closing time today.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-tomorrow-start">
                    <td class="replacement-code">%tomorrow_start%</td>
                    <td class="description"><?php _e('Formatted first opening time for tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-tomorrow-end">
                    <td class="replacement-code">%tomorrow_end%</td>
                    <td class="description"><?php _e('Formatted last closing time for tomorrow.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-seconds">
                    <td class="replacement-code">%seconds%</td>
                    <td class="description"><?php _e('Seconds until either change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-seconds-padded">
                    <td class="replacement-code">%seconds_padded%</td>
                    <td class="description"><?php _e('Seconds with 2-digit padding for remaining time for change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-seconds-divisor">
                    <td class="replacement-code">%seconds_divisor%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Seconds as a divisor until either change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-minutes', 'minutes'); ?></td>
                </tr>
                <tr id="code-seconds-divisor-padded">
                    <td class="replacement-code">%seconds_divisor_padded%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Seconds as a divisor with 2-digit padding for remaining time for change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-minutes', 'minutes'); ?></td>
                </tr>
                <tr id="code-minutes">
                    <td class="replacement-code">%minutes%</td>
                    <td class="description"><?php _e('Minutes until either change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-minutes-padded">
                    <td class="replacement-code">%minutes_padded%</td>
                    <td class="description"><?php _e('Minutes with 2-digit padding for remaining time for change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-minutes-divisor">
                    <td class="replacement-code">%minutes_divisor%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Minutes as a divisor until either change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-hours', 'hours'); ?></td>
                </tr>
                <tr id="code-minutes-divisor-padded">
                    <td class="replacement-code">%minutes_divisor_padded%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Minutes as a divisor with 2-digit padding for remaining time for change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-hours', 'hours'); ?></td>
                </tr>
                <tr id="code-hours">
                    <td class="replacement-code">%hours%</td>
                    <td class="description"><?php _e('Hours until either change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-hours-padded">
                    <td class="replacement-code">%hours_padded%</td>
                    <td class="description"><?php _e('Hours with 2-digit padding for remaining time for change in opening or closing status.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-hours-divisor">
                    <td class="replacement-code">%hours_divisor%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Hours as a divisor until either change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-days-change', 'days_change'); ?></td>
                </tr>
                <tr id="code-hours-divisor-padded">
                    <td class="replacement-code">%hours_divisor_padded%</td>
                    <td class="description"><?php /* translators: 1: bookmark to another replacement code, 2: name of replacement code */
						echo sprintf(__('Hours as a divisor with 2-digit padding for remaining time for change in opening or closing status &mdash; useful when displayed with <a href="%1$s">%2$s</a>.', 'opening-hours'), '#code-days-change', 'days_change'); ?></td>
                </tr>
                <tr id="code-days-change">
                    <td class="replacement-code">%days_change%</td>
                    <td class="description"><?php /* translators: number of days, 7 or higher */
						echo sprintf(__('Days until change of either opening or closing status; will show no more than %u days.', 'opening-hours'), 14); ?></td>
                </tr>
                <tr id="code-space">
                    <td class="replacement-code">%space%</td>
                    <td class="description"><?php _e('Space character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-percent">
                    <td class="replacement-code">%percent%</td>
                    <td class="description"><?php _e('Percent character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-comma">
                    <td class="replacement-code">%comma%</td>
                    <td class="description"><?php _e('Comma character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-colon">
                    <td class="replacement-code">%colon%</td>
                    <td class="description"><?php _e('Colon character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-semicolon">
                    <td class="replacement-code">%semicolon%</td>
                    <td class="description"><?php _e('Semi-colon character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-exclamation">
                    <td class="replacement-code">%exclamation%</td>
                    <td class="description"><?php _e('Exclamation mark character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-question">
                    <td class="replacement-code">%question%</td>
                    <td class="description"><?php _e('Question mark character.', 'opening-hours'); ?></td>
                </tr>
                <tr id="code-fullstop">
                    <td class="replacement-code"><?php  /* translators: currently only accepted values are: period, fullstop, dot or point enclosed with percent character */ _e('&percnt;period&percnt;', 'opening-hours'); ?></td>
                    <td class="description"><?php _e('Period character.', 'opening-hours'); ?></td>
                </tr>
            </table>
            <h2 id="classes"><?php esc_html_e('HTML Classes', 'opening-hours'); ?></h2>
            <p><?php /* translators: 1: bookmark to another HTML class, 2: name of HTML class */
				echo sprintf(__('Stylistically, you may wish to make changes that are beyond the list of themes. Here is a list of HTML classes that can be used by <a href="%1$s">%2$s</a> parameter to set your design preferences.', 'opening-hours'), '#parameter-class', 'class'); ?></p>
            <table class="wp-list-table widefat fixed striped classes">
              <tr>
                  <th class="class"><?php esc_html_e('Class', 'opening-hours'); ?></th>
                  <th class="description"><?php esc_html_e('Description', 'opening-hours'); ?></th>
                  <th class="boolean"><abbr title="<?php esc_attr_e('Affects Opening Hours Table', 'opening-hours'); ?>"><?php esc_html_e('Table', 'opening-hours'); ?></abbr></th>
                  <th class="boolean"><abbr title="<?php esc_attr_e('Affects Opening Hours Text', 'opening-hours'); ?>"><?php esc_html_e('Text', 'opening-hours'); ?></abbr></th>
              </tr>
                <tr id="class-left">
                  <td class="class">left</td>
                  <td class="description"><?php _e('Align all text to the left', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-center">
                  <td class="class">center</td>
                  <td class="description"><?php _e('Align all text to the center', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-right">
                  <td class="class">right</td>
                  <td class="description"><?php _e('Align all text to the right', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-start">
                  <td class="class">start</td>
                  <td class="description"><?php _e('Align all text to the start', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-end">
                  <td class="class">end</td>
                  <td class="description"><?php _e('Align all text to the end', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-left">
                  <td class="class">day-left</td>
                  <td class="description"><?php _e('Align day name text to the left', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-center">
                  <td class="class">day-center</td>
                  <td class="description"><?php _e('Align day name text to the center', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-right">
                  <td class="class">day-right</td>
                  <td class="description"><?php _e('Align day name text  to the right', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-start">
                  <td class="class">day-start</td>
                  <td class="description"><?php _e('Align day name text to the start', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-end">
                  <td class="class">day-end</td>
                  <td class="description"><?php _e('Align day name text to the end', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-day-capitalize">
                  <td class="class">day-capitalize</td>
                  <td class="description"><?php _e('Capitalize text (first letter) for day names', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-day-uppercase">
                  <td class="class">day-uppercase</td>
                  <td class="description"><?php _e('Set all text to upper case for day names', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-day-lowercase">
                  <td class="class">day-lowercase</td>
                  <td class="description"><?php _e('Set all text to lower case for day names', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-left">
                  <td class="class">hours-left</td>
                  <td class="description"><?php _e('Align opening hours text to the left', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-hours-center">
                  <td class="class">hours-center</td>
                  <td class="description"><?php _e('Align opening hours text to the center', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-hours-right">
                  <td class="class">hours-right</td>
                  <td class="description"><?php _e('Align opening hours text  to the right', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-hours-start">
                  <td class="class">hours-start</td>
                  <td class="description"><?php _e('Align opening hours text to the start', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-hours-end">
                  <td class="class">hours-end</td>
                  <td class="description"><?php _e('Align opening hours text to the end', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-hours-capitalize">
                  <td class="class">hours-capitalize</td>
                  <td class="description"><?php _e('Capitalize text (first letter) of words in opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-uppercase">
                  <td class="class">hours-uppercase</td>
                  <td class="description"><?php _e('Set all text to upper case for opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-lowercase">
                  <td class="class">hours-lowercase</td>
                  <td class="description"><?php _e('Set all text to lower case for opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-normal">
                  <td class="class">hours-normal</td>
                  <td class="description"><?php _e('Set font weight to normal for hours names (cell headings will often default to bold)', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-day-bold">
                  <td class="class">day-bold</td>
                  <td class="description"><?php _e('Set font weight to bold for day names', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-day-normal">
                  <td class="class">day-normal</td>
                  <td class="description"><?php _e('Set font weight to normal for day names', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-closed-bold">
                  <td class="class">closed-bold</td>
                  <td class="description"><?php _e('Set closed text to bold', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-24-bold">
                  <td class="class">hours-24-bold</td>
                  <td class="description"><?php _e('Set 24 hour opening text to bold', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-closed-italic">
                  <td class="class">closed-italic</td>
                  <td class="description"><?php _e('Set closed text to italic', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-hours-24-italic">
                  <td class="class">hours-24-italic</td>
                  <td class="description"><?php _e('Set 24 hour opening text to italic', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                </tr>
                <tr id="class-wrap">
                  <td class="class">wrap</td>
                  <td class="description"><?php _e('Wrap text for day names and opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-no-wrap">
                  <td class="class">no-wrap</td>
                  <td class="description"><?php _e('Do not wrap text for day names and opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-no-border">
                  <td class="class">no-border</td>
                  <td class="description"><?php _e('Remove any table border that may exist in the theme’s style sheet', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-outside-flush">
                  <td class="class">outside-flush</td>
                  <td class="description"><?php /* translators: 1: bookmark to another HTML class, 2: name of HTML class 3: bookmark to another HTML class, 4: name of HTML class */
						echo sprintf(__('Remove padding from outside table cells so text appears flush to the edges when aligned to <a href="%1$s">%2$s</a> and <a href="%3$s">%4$s</a> respectively', 'opening-hours'), '#class-day-left', 'left', '#class-hours-right', 'right'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-current-line">
                  <td class="class">current-line</td>
                  <td class="description"><?php _e('Added a line between past days and the current day to highlight current opening hours', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
                <tr id="class-past-fade">
                  <td class="class">past-fade</td>
                  <td class="description"><?php _e('Set past days to a more transparent color', 'opening-hours'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
               </tr>
                <tr id="class-dark">
                  <td class="class">dark</td>
                  <td class="description"><?php /* translators: 1: bookmark to another HTML class, 2: name of HTML class, 3: bookmark to another HTML class, 4: name of HTML class */
						echo sprintf(__('For pages or sections with dark backgrounds &mdash; currently in use for classes: <a href="%1$s">%2$s</a> and <a href="%3$s">%4$s</a>', 'opening-hours'), '#class-current-line', 'current-line', '#class-past-fade', 'past-fade'); ?>.</td>
                  <td class="boolean"><span class="dashicons dashicons-yes"></span></td>
                  <td class="boolean"><span class="dashicons dashicons-no"></span></td>
                </tr>
            </table>
        </form>
    </div>

	<div id="additional" class="section<?php echo ($this->section != 'additional') ? ' hide' : ''; ?>">

		<h2><?php esc_html_e('Additional', 'opening-hours'); ?></h2>

        <form method="post" action="options.php" id="open-settings-google-credentials">
            <h3><?php esc_html_e('Google Credentials', 'opening-hours'); ?></h3>
            <p><?php _e('Use Google’s Places API to retrieve your current opening hours from Google My Business.', 'opening-hours'); ?></p>
            <table class="form-table google-credentials">
                <tr>
                    <th scope="row"><label for="api-key"><?php esc_html_e('Google API Key', 'opening-hours'); ?></label></th>
                    <td>
                        <p class="input">
                            <input type="text" id="api-key" class="regular-text code" name="we_are_open_api_key" placeholder="<?php echo esc_attr(str_repeat('x', 40)); ?>" value="<?php echo esc_attr(get_option('we_are_open_api_key')); ?>">
                        </p>
                        <p class="description<?php echo ((get_option('we_are_open_api_key') == NULL) ? ' unset' : ''); ?>"><?php /* translators: 1: URL of Place ID Finder, 2: IP of the web server, 3: Help icon and reveal toggle link */ 
						echo sprintf(__('In order to retrieve Google My Business data, you’ll need your own <a href="%1$s" class="components-external-link" target="_blank">API Key</a>, with API: <span class="highlight">Places API</span> and restrict to IP: <span class="highlight">%2$s</span> %3$s', 'opening-hours'), 'https://developers.google.com/maps/documentation/javascript/get-api-key', esc_html($this->server_ip()), ' <a id="google-credentials-help" href="#google-credentials-steps"><span class="dashicons dashicons-editor-help"></span></a>'); ?></p>
                        <ol id="google-credentials-steps">
							<li>
                        <?php /* translators: 1: URL of Google Developer Console, 2: URL of Place API, 3: URL of Google Developer Console, 4: IP of web server, 5: URL for Google billing account */
						echo preg_replace('/[\r\n]+/', '</li>' . PHP_EOL . str_repeat("\t", 7) . '<li>', sprintf(__('Create a new project or open an existing project in <a href="%1$s" class="components-external-link" target="_blank">Google Developer’s Console</a>
Search for <a href="%2$s" class="components-external-link" target="_blank">Places API</a> and click the button to enable this API in your account
In <a href="%3$s" class="components-external-link" target="_blank">Credentials</a>, click the button: “+ Create Credentials”
Select “API Key” from the options
Once this key is created, click “Close”
Select your newly created API Key
Under “Application restrictions”, set this to: “IP addresses” and “Add an item” with your web server’s IP: <span class="highlight">%4$s</span>
Under “API restrictions”, select “Restrict Key”, select just “Places API” from the list of options and click “OK”
Click “Save” to set the restrictions
Copy this new API Key to this plugin’s settings
Finally for regular requests, please <a href="%5$s" class="components-external-link" target="_blank">enable billing</a> for your project to receive your <em>substantial and free</em> API request allocation', 'opening-hours'), 'https://console.developers.google.com/apis/credentials', 'https://console.cloud.google.com/apis/library/places-backend.googleapis.com?q=place', 'https://console.developers.google.com/apis/credentials', $this->server_ip(), 'https://console.cloud.google.com/projectselector/billing/enable')); ?></li>
                        </ol>
                        <p class="visual-guide"><?php /* translators: %s: a URL for a visual guide */ 
						echo sprintf(__('Would you follow this better with diagrams? Check out our <a href="%s" class="components-external-link" target="_blank">visual guide</a>.', 'opening-hours'), 'https://designextreme.com/wordpress/we-are-open/#api-key'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="place-id"><?php esc_html_e('Google Place ID', 'opening-hours'); ?></label></th>
                    <td>
                        <p class="input">
                            <input type="text" id="place-id" class="regular-text code" name="we_are_open_place_id" placeholder="<?php echo esc_attr(str_repeat('x', 26)); ?>" value="<?php echo esc_attr(get_option('we_are_open_place_id')); ?>">
                            <button type="button" name="google-data-preview" id="google-data-preview" class="button button-secondary google-data"<?php echo (get_option('we_are_open_place_id') != NULL && $this->google_data_exists()) ? '' : ' style="display: none"'; ?>><span class="dashicons dashicons-text-page"></span> <?php esc_html_e('View Retrieved Data', 'opening-hours'); ?></button>
                        </p>
                        <p class="description"><?php /* translators: %s: the Google Place Finder URL */
						echo sprintf(__('You can find your unique Place ID by searching by your business’ name in <a href="%s" class="components-external-link" target="_blank">Google’s Place ID Finder</a>. Single business locations are accepted; coverage areas are not.', 'opening-hours'), 'https://developers.google.com/places/place-id'); ?></p>
                    </td>
                </tr>
                <tr class="google-data"<?php echo ($this->google_data_exists(TRUE) && get_option('we_are_open_api_key') != NULL) ? '' : ' style="display: none"'; ?>>
                    <th scope="row"><label for="place-name"><?php esc_html_e('Place Name', 'opening-hours'); ?></label></th>
                    <td>
                        <p class="input">
                            <input type="text" id="place-name" class="regular-text" name="we_are_open_place_name" placeholder="<?php echo esc_attr(str_repeat('x', 26)); ?>" value="<?php echo esc_attr(($this->google_data_exists(TRUE)) ? $this->google_data['result']['name'] : ''); ?>" disabled="disabled">
                        </p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="button" name="submit" id="google-credentials-button" class="button button-primary"><?php esc_html_e('Save', 'opening-hours'); ?></button>
            </p>
        </form>

        <form method="post" action="options.php" id="open-settings-custom-styles">
            <h3><?php esc_html_e('Custom Styles', 'opening-hours'); ?></h3>
            <p><?php _e('If you prefer to manage your style sheet outside of your theme, you may add your own customized styles.', 'opening-hours'); ?></p>
            <p>
                <textarea id="custom-styles" name="we_are_open_custom_styles" placeholder="&#x2F;&#x2A;&#x20;CSS&#x20;Document&#x20;&#x2A;&#x2F;&#10;&#10;.opening-hours&#x20;.day-name&#x20;{&#10;&#x9;font-weight:&#x20;400;&#10;}"><?php echo esc_html(get_option('we_are_open_custom_styles')); ?></textarea>
			</p>
            <p class="submit">
                <button type="button" name="reset" id="custom-styles-button" class="button button-primary"><?php esc_html_e('Save', 'opening-hours'); ?></button>
            </p>
        </form>

        <form method="post" action="options.php" id="open-settings-cache">
            <h3><?php esc_html_e('Cache', 'opening-hours'); ?></h3>
            <p><?php _e('You may wish to clear the cache after a change to the time zone in the General Settings.', 'opening-hours'); ?></p>
            <p class="submit">
                <button type="button" name="clear-cache" id="clear-cache-button" class="button button-primary"><?php esc_html_e('Clear Cache', 'opening-hours'); ?></button>
            </p>
        </form>

        <form method="post" action="options.php" id="open-settings-reset">
            <h3><?php esc_html_e('Reset', 'opening-hours'); ?></h3>
            <p><?php _e('At times you may wish to start over, so you can clear all the plugin’s settings here.', 'opening-hours'); ?></p>
            <p id="reset-confirm-text">
                <label for="reset-all"><input type="checkbox" id="reset-all" name="we_are_open_reset_all" value="1"> <?php esc_html_e('Yes, I am sure.', 'opening-hours'); ?></label>
			</p>
            <p class="submit">
                <button type="button" name="reset" id="reset-button" class="button button-primary"><?php esc_html_e('Reset', 'opening-hours'); ?></button>
            </p>
        </form>
	</div>

	<div id="about" class="section<?php echo ($this->section != 'about') ? ' hide' : ''; ?>">
    	<div class="entry-content">
            <h2><?php esc_html_e('About', 'opening-hours'); ?></h2>
<?php /* translators: 1: plugin support URL, 2: author's name, 3: author's website, 4: author's business name */ 
	echo sprintf(__('						<p>This is a second plugin I’ve created for everyone – starting with the API request to Google My Business and then switching it to a proper opening times tool. The functionality to collect data using the API is still there, but it’s much more useful to send structured data back to Google and other search engines to provide accurate business information in the rich snippets and maps.</p>
			<p>As with the Google Reviews and Rating plugin, you can use shortcodes can be used in any post, page or use the widget version. There is an extensive list of parameters, conditions and variables to enclose text and HTML elements. I have kept the style sheet minimal to allow for your customizations — as a developer/designer this is what I’d like for all plugins.</p>
			<p>This one is my second published plugin for WordPress so I’d appreciate any feedback. So if you have any comments, feature requests or wish to show me your designs, please feel free to <a href="%1$s">get in touch</a> with me.</p>
			<p><span class="signature" title="%2$s"></span><br>
				Developer, <a href="%3$s">%4$s</a></p>', 'opening-hours'), 'https://designextreme.com/wordpress/', 'Noah H', 'https://designextreme.com', 'Design Extreme'); ?>

			<h2><a href="<?php echo esc_attr(admin_url('plugin-install.php?s=designextreme&tab=search&type=author')); ?>"><?php esc_html_e('Plugins by the Developer', 'opening-hours'); ?></a></h2>
            <ul id="wordpress-plugin-list">
            	<li id="wordpress-plugin-open">
                	<h3><a href="https://wordpress.org/plugins/opening-hours/" class="components-external-link" target="_blank"><span class="icon"></span> We’re Open!</a></h3>
                    <p>Simple and easy to manage regular and special opening hours for your business, includes support for Structured Data and populating from Google My Business.</p>
                    <p class="more-details"><a href="https://wordpress.org/plugins/opening-hours/" class="components-external-link" target="_blank"><?php esc_html_e('More Details', 'opening-hours'); ?></a></p>
                    <p class="installed"><?php esc_html_e('Installed', 'opening-hours'); ?></p>
                </li>
            	<li id="wordpress-plugin-g-business-reviews-rating">
                	<h3><a href="https://wordpress.org/plugins/g-business-reviews-rating/" class="components-external-link" target="_blank"><span class="icon"></span> Reviews and Rating – Google My Business</a></h3>
                    <p>Shortcode and widget for Google reviews and rating. Give customers a chance to leave their own rating/review; includes Structured Data for SEO.</p>
                    <p class="more-details"><a href="https://wordpress.org/plugins/g-business-reviews-rating/" class="components-external-link" target="_blank"><?php esc_html_e('More Details', 'opening-hours'); ?></a></p>
<?php if (is_plugin_active('g-business-reviews-rating/g-business-reviews-rating.php')) : ?>
                    <p class="installed"><?php esc_html_e('Installed', 'opening-hours'); ?></p>
<?php endif; ?>
                </li>
            </ul>
		</div>
    	<div class="entry-meta">
            <div class="widget plugin-social">
                <h3 class="widget-title"><?php esc_html_e('Follow Us', 'opening-hours'); ?></h3>
                <p class="aside"><?php esc_html_e('Want some easy-to-follow pro tips with examples? We will help you to make your reviews really stand out. Feature requests are welcome too.', 'opening-hours'); ?></p>
                <p><a class="button" href="https://twitter.com/designextreme_"><span class="dashicons dashicons-twitter"></span> <?php esc_html_e('Follow Us', 'opening-hours'); ?></a></p>			
            </div>
            <div class="widget plugin-support">
                <h3 class="widget-title"><?php esc_html_e('Support', 'opening-hours'); ?></h3>
                <p class="aside"><?php esc_html_e('Do you have any general support queries? Please search our forums at WordPress or make your own contribution. You can see that we are always very quick to reply!', 'opening-hours'); ?></p>
                <p><a class="button" href="https://wordpress.org/support/plugin/opening-hours/"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e('View support forum', 'opening-hours'); ?></a></p>			
            </div>
            <div class="widget plugin-ratings">
                <h3 class="widget-title"><?php esc_html_e('Ratings', 'opening-hours'); ?></h3>
                <p class="aside"><?php esc_html_e('Love this plugin with as much heart as we’ve put into its code? Why not share your feedback to help others with their plugin decision.', 'opening-hours'); ?></p>
                <p><a class="button" href="https://wordpress.org/support/plugin/opening-hours/reviews/#new-post"><span class="dashicons dashicons-star-filled"></span> <?php esc_html_e('Add my review', 'opening-hours'); ?></a></p>			
            </div>
            <div class="widget plugin-donate">
                <h3 class="widget-title"><?php esc_html_e('Donate', 'opening-hours'); ?></h3>
                <p class="aside"><?php esc_html_e('This plugin is powered by oat flat whites… We welcome any show of support the advancement of this plugin, no matter how small.', 'opening-hours'); ?></p>
                <p><a class="button button-secondary" href="https://paypal.me/designextreme"><span class="dashicons dashicons-heart"></span> <?php esc_html_e('Donate to this plugin', 'opening-hours'); ?></a></p>
            </div>
		</div>
	</div>
</div>

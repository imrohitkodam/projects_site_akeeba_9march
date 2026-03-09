<?php
/*
 * Copyright (C) joomla-monster.com
 * Website: http://www.joomla-monster.com
 * Support: info@joomla-monster.com
 *
 * JM Pricing Tables is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JM Pricing Tables is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JM Pricing Tables. If not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$count = 0;

$row_number = ( $elements > $span_size ) ? $span_size : $elements;

?>

<div id="<?php echo $id; ?>" class="jmm-pricing <?php echo $theme_class . ' ' . $mod_class_suffix; ?>">
	<div class="jmm-pricing-in">
		<div class="jmm-mod-row row-<?php echo $row_number; ?>">
				<?php

				foreach($output_data as $item) {

					$count++;
					$highlight = ( $item->highlight == 1 ) ? 'highlight' : '';

				?>

				<div class="jmm-item item-<?php echo $count . ' ' . $highlight; ?>">

					<?php if( $item->title ) : ?>
						<div class="jmm-title"><?php echo $item->title; ?></div>
					<?php endif; ?>

					<?php if( isset($item->price) && $item->price != '') : ?>

						<div class="jmm-price">

							<?php if( $item->currency ) : ?>
								<span class="jmm-currency"><?php echo $item->currency; ?></span>
							<?php endif; ?>

							<span class="jmm-number"><?php echo $item->price; ?></span>

							<?php if( $item->period ) : ?>
								<span class="jmm-period"><?php echo $item->period; ?></span>
							<?php endif; ?>

						</div>
					<?php endif; ?>

					<?php if( $item->description ) : ?>
						<div class="jmm-description">
							<?php echo $item->description; ?>
						</div>
					<?php endif; ?>

					<?php if( $item->btn_text && $item->btn_url ) : ?>
						<div class="jmm-button">
							<a class="btn btn-secondary" role="button" href="<?php echo $item->btn_url; ?>"><?php echo $item->btn_text; ?></a>
						</div>
					<?php endif; ?>

				</div>

				<?php

				}

				?>
		</div>
	</div>
</div>

<?php
/**
 * Template for header of the product review widget.
 *
 * @package one2five
 */

declare(strict_types = 1);

namespace One2Five;

?>

<div class="one2five-bootstrap">
		<div class="container py-5">
			<div class="row">
				<div class="col-lg-4 sec1">
					<h2 class="mb-3">Opinions</h2>
					<div class="row align-items-center">
						<h1 class="col-auto">
							<?php echo esc_html( $average_feedback_rating ); ?><span style="color: #7b8a9c">/5</span>
						</h1>
						
					</div>
					<p>
						<?php echo esc_html( $average_feedback_rating ); ?> points
						<?php echo esc_html( $total_reviews ); ?> reviews <br />We cannot guarantee that the reviews
						below are written exclusively by people who have actually purchased
						or used the product.
					</p>
					<p>
						For certain products, we also display ratings of other products with
						similar features.
					</p>
					<a href="#" style="color: #7b8a9c">
						<p>You can read more about how we handle reviews here .</p>
					</a>
				</div>
				<div class="col-lg-4 align-self-end">
					<div class="row justify-content-center align-items-center mb-3 mt-md-4">
						<div class="col-auto">
							<span class="chart design" data-rating="<?php echo esc_attr( $average_design_rating ); ?>">
								<span class="percent">
									<?php echo esc_html( $average_design_rating ); ?>
								</span>
								<canvas height="110" width="110"></canvas>
							</span>
						</div>
						<div class="col-auto">
							<span class="chart function" data-rating="<?php echo esc_attr( $average_functionality_rating ); ?>">
								<span class="percent">
									<?php echo esc_html( $average_functionality_rating ); ?>
								</span>
								<canvas height="110" width="110"></canvas>
							</span>
						</div>
						<div class="col-auto">
							<span class="chart quality" data-rating="<?php echo esc_attr( $average_quality_rating ); ?>">
								<span class="percent">
									<?php echo esc_html( $average_quality_rating ); ?>
								</span>
								<canvas height="110" width="110"></canvas>
							</span>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<button type="button" class="btn d-block w-100 mt-2 text-white write offcanvasRight add-rating-btn"
						style="background-color: <?php echo esc_attr( $this->_primary_color ); ?> !important; color: <?php echo esc_attr( $this->_font_color ); ?> !important; text-transform: <?php echo esc_attr( $this->_text_case ); ?>">
						<?php echo esc_html($translations['leave_a_review']['label']); ?>
					</button>
				</div>
			</div>
			<hr class="mt-5" />
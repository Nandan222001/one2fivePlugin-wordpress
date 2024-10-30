<?php
/**
 * The template for displaying No Reviews Found.
 *
 * @package one2five
 */

declare(strict_types = 1);

namespace One2Five;

				$reviewer_name   = $review['reviewerName'];
				$submission_date = $review['submissionDate'];
				$review_title    = $review['reviewTitle'];
				$review_text     = $review['reviewText'];
?>
				<div class="row mb-4">
					<div class="col-lg-8">
						<div class="star user_star col-auto ms-0 mb-2" style="margin-top: 0.5px; padding-left: 0;">
							<?php
								$filled_stars = intval( $review['feedbackRating'] );
								// Star wrapper for whole stars.
								echo '<div style="position: relative; display: inline-block;">';
								echo '<div class="stars-background" style="display: inline-block; white-space: nowrap; color: #acacac;">';
							for ( $i = 1; $i <= 5; $i++ ) {
								echo '<i class="bi bi-star-fill" style="color: inherit;"></i>';
							}
								echo '</div>';
						
								// Overlay for filled stars.
								$width = ( $filled_stars / 5 ) * 100; // Calculate width percentage.
								echo '<div class="stars-foreground" style="display: inline-block; white-space: nowrap; color: ' . esc_attr( get_option( 'accent_color' ) ) . '; position: absolute; top: 0; left: 0; overflow: hidden; width: ' . esc_attr( $width ) . '%;">';
							for ( $i = 1; $i <= 5; $i++ ) {
								echo '<i class="bi bi-star-fill" style="color: inherit;"></i>';
							}
								echo '</div>';
								echo '</div>';
							?>
						</div>

						<h5 class="mb-2">
							<?php echo esc_html( $reviewer_name ); ?> <span style="color: #7b8a9c">
								<?php echo esc_html( gmdate( 'F j, Y', strtotime( $submission_date ) ) ); ?>
							</span>
						</h5>
						<b class="mb-3">
							<?php echo esc_html( $review_title ); ?>
						</b>
						<p class="mt-2 review-t">
							<?php echo esc_html( $review_text ); ?>
						</p>
					</div>
					<div class="col-lg-4 mt-4">
						<?php foreach ( $review['dynamicRating'] as $rating ) : ?>
							<?php if ( 'Feedback' !== $rating['ratingName'] ) : ?>
								<div class="row mb-3">
									<div class="rating-info d-flex align-items-center">
										<div class="side me-5">
											<div class="rating-name" style="text-wrap:nowrap !important;">
												<?php echo esc_html( $rating['ratingName'] ); ?>
											</div>
										</div>
										<div class="middle mt-4" style="margin-left: 20px;"> <!-- Added margin-left for space -->
											<div class="bar-container">
												<div class="bar-5" style="width: <?php echo esc_html( $rating['ratingScore'] ) * 20 . '%'; ?>; background-color: <?php echo esc_attr( $this->_accent_color ); ?> !important;">
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<hr class="mt-5" />
				</div>

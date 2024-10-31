<?php
/*
Plugin Name: SBW Timeline
Plugin URI: https://smallbusinesswebsites.co.uk
Description: SBW Timeline is an easy to use plugin that let's you create a responsive, vertical timeline perfect to feature on your wedding website.
Author: Ilona Ferenc
Version: 1.0.1
Author URI: http://ilonaferenc.co.uk
Text Domain: sbw-timeline
*/

function sbwhea_register_post_type() {
    $args = array(
        'public'    => true,
        'label'     => 'Timeline Events',
        'menu_icon' => 'dashicons-editor-video',
        'supports'  => array( 'title', 'editor', 'thumbnail' )
    );
    register_post_type( 'sbw_timeline_event', $args );
}
add_action( 'init', 'sbwhea_register_post_type' );

function sbwhea_add_meta_boxes() {
    add_meta_box( 'timeline-event-date', 'Event date', 'sbwhea_meta_boxes_callback', 'sbw_timeline_event' );
}
add_action( 'add_meta_boxes', 'sbwhea_add_meta_boxes' );

function sbwhea_meta_boxes_callback( $post ) {
    $event_date = get_post_meta( $post->ID, 'event_date', true );
    ?>
    <label for="ebent_date">Event date</label>
    <input type="date" name="event_date" id="event_date" value="<?php echo esc_attr( $event_date ); ?>">
    <?php
}

function sbwhea_save_metaboxes( $post_id ) {
    if ( array_key_exists( 'event_date', $_POST ) ) {
        $event_date = sanitize_text_field( $_POST['event_date'] );
        update_post_meta( $post_id, 'event_date', $event_date );
    }
}
add_action( 'save_post', 'sbwhea_save_metaboxes' );

function sbwhea_enqueue_style() {
	wp_enqueue_style( 'sbw-timeline', plugins_url( 'sbw-timeline.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'sbwhea_enqueue_style' );

function sbwhea_get_timeline() {
	$args = array(
		'post_type'			=> 'sbw_timeline_event',
		'posts_per_page'	=> -1,
		'order'				=> 'ASC'
	);
	$timeline_events = get_posts( $args);

	if ( !empty( $timeline_events ) ) {
        $timeline_events_number = count( $timeline_events );
	    ?>
		<div id="timeline-wrapper" class="clearfix">
			<div id="timeline-line">
			</div>
			<div id="timeline-content">
				<?php
				$i = 1;
				foreach ( $timeline_events as $post ) {
					setup_postdata( $post );
					$event_date = get_post_meta( $post->ID , 'event_date', true );
                    $event_date = esc_html( $event_date );

					if ( !empty( $event_date ) ) {
						$event_date = strtotime( $event_date );
						$event_date = date( 'dS F Y', $event_date );
					}

					if ( $i !== $timeline_events_number ) {
					?>
						<div class="timeline-item">
							<div class="timeline-icon">
								<i class="fas fa-heart"></i>
							</div>
							<div class="timeline-content-wrapper">
								<?php
								if ( has_post_thumbnail( $post->ID ) ) {
								?>
									<div class="image-wrap">
										<div></div>
										<div>
											<?php echo get_the_post_thumbnail( $post->ID ); ?>
										</div>
									</div>
								<?php
								}
								?>
								<div class="text-wrapper">
									<h3><?php echo get_the_title( $post->ID ); ?></h3>
									<?php
                                    if ( !empty( $event_date ) ) {
                                    ?>
                                        <span class="date">
                                        <?php echo $event_date; ?>
                                        </span>
                                    <?php
                                    }
                                    ?>
									<?php echo get_the_content( $post->ID ); ?>
								</div>
							</div>
						</div>
					<?php
					}else{
					?>
			</div>
			<div id="timeline-bottom"></div>
		</div>
		<div id="timeline-last-element">
			<div class="timeline-icon">
				<i class="fas fa-heart"></i>
			</div>
			<div class="timeline-content-wrapper">
				<?php
				if ( has_post_thumbnail( $post->ID ) ) {
				?>
					<div class="image-wrap">
						<div></div>
						<div>
							<?php echo get_the_post_thumbnail( $post->ID ); ?>
						</div>
					</div>
				<?php
				}
				?>
				<div class="text-wrapper">
					<h3><?php echo get_the_title( $post->ID ); ?></h3>
                    <?php
                    if ( !empty( $event_date ) ) {
                    ?>
                        <span class="date">
                        <?php echo $event_date; ?>
                        </span>
                    <?php
                    }
                    ?>
					<?php echo get_the_content( $post->ID ); ?>
				</div>
			</div>
		</div>
					<?php }
					$i++;
		 		}
				wp_reset_postdata();
				?>
	</div>
	<?php
	}
}

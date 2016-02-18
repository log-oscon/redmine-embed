<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$issue_class = array(
	'redmine-issue',
	'redmine-issue--tracker-'  . (int) $issue->tracker->id,
	'redmine-issue--status-'   . (int) $issue->status->id,
	'redmine-issue--priority-' . (int) $issue->priority->id,
);

$attributes = array(
	'status' => array(
		'label' => \__( 'Status:', 'redmine-embed' ),
		'value' => \esc_html( $issue->status->name ),
	),
	'start-date' => array(
		'label' => \__( 'Start date:', 'redmine-embed' ),
		'value' => \esc_html( $issue->start_date ),
	),
	'priority-date' => array(
		'label' => \__( 'Priority:', 'redmine-embed' ),
		'value' => \esc_html( $issue->priority->name ),
	),
	'due-date' => array(
		'label' => \__( 'Due date:', 'redmine-embed' ),
		'value' => \esc_html( $issue->due_date ),
	),
	'assigned-to' => array(
		'label' => \__( 'Assignee:', 'redmine-embed' ),
		'value' => $this->get_attribute_link( $issue->assigned_to ),
	),
	'done-ratio' => array(
		'label' => \__( '% Done:', 'redmine-embed' ),
		'value' => sprintf(
			'<progress class="redmine-issue__progress redmine-issue__progress--%1$d" value="%1$d" max="100"></progress>' .
			'<span class="redmine-issue__value redmine-issue__value--percent">%1$d%%</span>',
			(int) $issue->done_ratio
		),
	),
	'category' => array(
		'label' => \__( 'Category:', 'redmine-embed' ),
		'value' => $this->get_attribute_link( $issue->category ),
	),
	'spent-time' => array(
		'label' => \__( 'Spent time:', 'redmine-embed' ),
		'value' => sprintf(
			'<a href="%s">%s</a>',
			$issue->spent_hours_link,
			sprintf(
				\_nx( '%s hour', '%s hours', $issue->spent_hours, 'spent time', 'redmine-embed' ),
				\number_format_i18n( $issue->spent_hours )
			)
		),
	),
	'fixed-version' => array(
		'label' => \__( 'Target version:', 'redmine-embed' ),
		'value' => $this->get_attribute_link( $issue->fixed_version ),
	),
);

/**
 * Filter issue attributes for display.
 *
 * @param  array  $attributes Issue attributes.
 * @param  object $issue      Original issue object.
 * @return array              Filtered issue attributes.
 */
$attributes = \apply_filters( 'redmine_embed_issue_attributes', $attributes, $issue );

?>
<div class="<?php echo \esc_attr( implode( ' ', $issue_class ) ); ?>">
    <h2><a href="<?php echo \esc_url( $issue->link ); ?>"><?php
		printf( \__( 'Task #%d', 'redmine-embed' ), (int) $issue->id );
	?></a></h2>

    <div class="redmine-issue__subject">
        <h3><?php echo \esc_html( $issue->subject ); ?></h3>
    </div>

    <p class="redmine-issue__author"><?php
		printf(
			\__( 'Added by %s on %s. Updated on %s.', 'redmine-embed' ),
			$this->get_attribute_link( $issue->author ),
			\esc_html( $issue->rendered->created_on ),
			\esc_html( $issue->rendered->updated_on )
		);
	?></p>

    <div class="redmine-issue__attributes"><?php
		foreach( $attributes as $name => $attr ) {
			echo '<div class="redmine-issue__attribute">';
			printf( '<div class="redmine-issue__label redmine-issue__label--%s">%s</div>', $name, $attr['label'] );
			printf( '<div class="redmine-issue__value redmine-issue__value--%s">%s</div>', $name, $attr['value'] );
			echo '</div>';
		}
	?></div>

    <hr />

    <div class="redmine-issue__description">
        <p><strong><?php _e( 'Description', 'redmine-embed' ); ?></strong></p>
        <div class="wiki"><?php
            echo \wp_kses( $issue->rendered->description, \wp_kses_allowed_html( 'comment' ) );
        ?></div>
    </div>

</div>

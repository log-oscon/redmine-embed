<div class="redmine-error">
    <?php printf( __( 'Unable to display issue <a href="%s">#%d</a>.', 'redmine-embed' ),
        \esc_url_raw( $this->url->get_public_url( 'issues', $issue_id ) ),
        $issue_id
    ); ?>
</div>
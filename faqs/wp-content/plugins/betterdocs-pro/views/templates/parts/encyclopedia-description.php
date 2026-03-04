<?php

if ( empty( $description ) ) {
    return;
}

$extra_class = isset( $widget_type ) && $widget_type == 'blocks' ? ' ' . $blockId : '';
echo '<div class="term-description' . $extra_class . '">' . wp_kses_post( wpautop( $description ) ) . '</div>';

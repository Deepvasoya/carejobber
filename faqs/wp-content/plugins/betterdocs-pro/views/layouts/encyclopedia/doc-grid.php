<?php $permalink = rtrim($doc['permalink'], '/'); 

$learnmore_text = 'Learn More ';

if(!empty($dictionary_learn_more_text))
{
    $learnmore_text = $dictionary_learn_more_text;
}


?>

<div class="encyclopedia-item" data-first-letter="<?php echo $letter; ?>">
    <div class="tools-card">
        <div class="top-tools-card">
            <?php
            $title_tag = !empty($item_heading_tag) ? $item_heading_tag : 'h2';
            echo '<' . esc_attr($title_tag) . ' class="heading-small tools-card__title-text encyclopedia-item-title-tag">' . esc_html($doc['post_title']) . '</' . esc_attr($title_tag) . '>';
            ?>
            <p class="text-size-small tools-card__sample-text"><?php echo esc_html($excerpt); ?></p>
        </div>
        <div class="tools-card_link-container">
            <a href="<?php echo esc_url($permalink ); ?>" class="text-style-link"><?php echo esc_html($learnmore_text); ?></a>
            <a href="#" class="text-style-link tools-card_arrow">→</a>
        </div>
        <a href="<?php echo esc_url($permalink ); ?>" class="tools-card__link-block w-inline-block"></a>
    </div>
</div>
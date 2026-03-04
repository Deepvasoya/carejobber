<div class="betterdocs-cross-domain-code">
    <aside id="betterdocs-ia"></aside>
    <link rel="stylesheet" href="<?php echo betterdocs_pro()->assets->asset_url( 'public/css/instant-answer.css' ); ?>">
    <link rel="stylesheet" href="<?php echo betterdocs_pro()->assets->asset_url( 'public/css/ai-chatbot.css' ); ?>">
    <style type="text/css"><?php echo $styles; ?></style>
    <script> window.betterdocs = <?php echo wp_json_encode( $scripts ); ?> </script>
    <script> window.betterdocsAIChatbot = <?php echo wp_json_encode( $chatbot_scripts ); ?> </script>
    <script src="<?php echo betterdocs_pro()->assets->asset_url( 'public/js/instant-answer-cd.js' ); ?>"></script>
</div>

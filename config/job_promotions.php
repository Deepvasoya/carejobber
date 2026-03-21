<?php

return [

    'currency' => strtolower(env('JOB_PROMOTIONS_CURRENCY', 'cad')),

    'featured_price' => (float) env('JOB_PROMOTION_FEATURED_PRICE', 10),

    'urgent_price' => (float) env('JOB_PROMOTION_URGENT_PRICE', 15),

    'highlighted_price' => (float) env('JOB_PROMOTION_HIGHLIGHTED_PRICE', 5),

];

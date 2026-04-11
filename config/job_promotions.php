<?php

return [

    'currency' => strtolower(env('JOB_PROMOTIONS_CURRENCY', 'cad')),

    /** Urgent job: employer chooses 7 or 15 days */
    'urgent_7_price' => (float) env('JOB_PROMOTION_URGENT_7_PRICE', 19),
    'urgent_15_price' => (float) env('JOB_PROMOTION_URGENT_15_PRICE', 30),

    /** Featured job: employer chooses 15 or 30 days */
    'featured_15_price' => (float) env('JOB_PROMOTION_FEATURED_15_PRICE', 25),
    'featured_30_price' => (float) env('JOB_PROMOTION_FEATURED_30_PRICE', 40),

    /** Highlighted listing background: fixed duration (default 30 days) */
    'highlighted_price' => (float) env('JOB_PROMOTION_HIGHLIGHTED_PRICE', 20),
    'highlighted_days' => (int) env('JOB_PROMOTION_HIGHLIGHTED_DAYS', 30),

];

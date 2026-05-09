<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = DB::table('jobs')->select('id', 'functional_area_id', 'job_category_id', 'medo_category_id')->get();
$total = count($jobs);
$has_fa = 0;
$has_jc = 0;
$has_mc = 0;
foreach($jobs as $j) {
    if ($j->functional_area_id) $has_fa++;
    if ($j->job_category_id) $has_jc++;
    if ($j->medo_category_id) $has_mc++;
}
echo "Total Jobs: $total\n";
echo "Has Functional Area: $has_fa\n";
echo "Has Job Category: $has_jc\n";
echo "Has Medo Category: $has_mc\n";

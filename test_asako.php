<?php

$html = file_get_contents("https://asako.mg/emploi");

if (preg_match('/<script[^>]*id=["\']__NEXT_DATA__["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
    $json = json_decode($matches[1], true);
    if ($json) {
        $pageProps = $json["props"]["pageProps"] ?? [];
        echo "Keys: " . implode(", ", array_keys($pageProps)) . "\n";
        
        foreach (["offers", "jobs", "listings", "results", "data", "items", "jobsList", "offersList"] as $key) {
            if (isset($pageProps[$key])) {
                $count = is_array($pageProps[$key]) ? count($pageProps[$key]) : 'not array';
                echo "Found: $key (count: $count)\n";
                if (is_array($pageProps[$key]) && count($pageProps[$key]) > 0) {
                    echo "First item keys: " . implode(", ", array_keys($pageProps[$key][0])) . "\n";
                }
            }
        }
        
        // Also check for any array with job-like data
        foreach ($pageProps as $k => $v) {
            if (is_array($v) && count($v) > 5) {
                $first = array_values($v)[0] ?? null;
                if ($first && is_array($first) && isset($first['title'])) {
                    echo "Potential job array: $k\n";
                }
            }
        }
    }
} else {
    echo "No __NEXT_DATA__ found\n";
}
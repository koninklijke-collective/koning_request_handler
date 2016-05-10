<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Request Handler',
    'description' => 'Handles GET requests and caches this in the caching framework',
    'category' => 'library',
    'version' => '1.0.0',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'Jesper Paardekooper,Benjamin Serfhos',
    'author_email' => 'jesper@koninklijk.io, benjamin@koninklijk.io',
    'author_company' => 'Koninklijke Collective',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.99-8.99.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);

<?php

namespace TGP\Utils;

function dump($thing) {
    echo '<pre>';
    print_r($thing);
    die;
}

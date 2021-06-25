<?php

$bbcode = new ChrisKonnertz\BBCode\BBCode();

$disabled_default_tags = ['b', 'i', 's', 'u', 'code', 'email',
                            'url', 'quote', 'youtube',
                            'font', 'size', 'color', 'left',
                            'center', 'right', 'spoiler', 'list',
                            '*', 'li'];

foreach($disabled_default_tags as $v) {
    $bbcode->forgetTag($v);
}
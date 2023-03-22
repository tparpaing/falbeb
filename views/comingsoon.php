<?php echo $renderer->render(
    'header',
    [
        'params' => [
            'styles' => ['comingsoon.css'],
            'no_footer' => true,
            'scripts' => [
                'https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js',
                'comingsoon.js'
            ]
        ],
        'title' => 'Coming soon'
    ]
); ?>

<div id="scene" class="scene" data-hover-only="false">


    <div class="circle" data-depth="1.2"></div>

    <div class="one" data-depth="0.9">
        <div class="content">
            <span class="piece"></span>
            <span class="piece"></span>
            <span class="piece"></span>
        </div>
    </div>

    <div class="two" data-depth="0.60">
        <div class="content">
            <span class="piece"></span>
            <span class="piece"></span>
            <span class="piece"></span>
        </div>
    </div>

    <div class="three" data-depth="0.40">
        <div class="content">
            <span class="piece"></span>
            <span class="piece"></span>
            <span class="piece"></span>
        </div>
    </div>

    <p class="p404" data-depth="0.50">SOON</p>
    <p class="p404" data-depth="0.10">SOON</p>

</div>

<?= $renderer->render(
    'footer',
    [
        'params' => [
            'no_about' => false,
            'no_footer' => true
        ]
    ]
); ?>
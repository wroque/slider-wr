<?php
if (!empty($items)):
    $jcarousel = 'slide_' . uniqid();
    $atts = array_merge(array('slideWidth' => $w), json_decode($items[0]->slider_atts, true));
    $style = sprintf('width: %dpx', $atts['slideWidth']);
    ?>
    <div id="<?php echo $jcarousel ?>">
        <?php foreach ($items as $item): ?>
            <div class="slide">
                <a href="<?php echo $item->item_link; ?>" <?php echo empty($item->item_target) ? '' : "target=\"{$item->item_target}\""; ?>>
                    <img src="<?php echo $item->item_link_img; ?>" style="<?= $style ?>" alt="<?php echo $item->item_title; ?>" />
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <script type="text/javascript">
        (function($) {
            $('#<?php echo $jcarousel ?>').bxSlider(<?php echo json_encode($atts); ?>);
        })(jQuery);
    </script>

<?php endif; ?>

<?php
//kuteshop_woocommerce_before_loop_start
    function gallery_cate()
    {
        if(is_product_category())
        {
            $term_id = get_queried_object()->term_id;
            $galleries = get_term_meta($term_id, 'cate_gallery', true);
            if (!empty($galleries))
            {
                $galleries = json_decode($galleries, true);
                ?>
                <div class="owl-carousel nav-center" data-autoplay="true" data-nav="true" data-loop="true" data-slidespeed="800" data-margin="30"  data-responsive = '{"0":{"items":1, "margin":0}, "480":{"items":1, "margin":0}, "768":{"items":1, "margin":0}, "992":{"items":1, "margin":0}, "1200":{"items":1, "margin":0}}'>
                    <?php  foreach ($galleries['items'] as $count => $gallery) { ?>
                        <div class="owl-one-row">
                            <div class="wrap-media">
                                <figure>
                                    <img src="<?php echo $gallery['url']; ?>" alt="<?php echo $gallery['name']; ?>">
                                </figure>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php
            }
        }

    }
    add_action('kuteshop_woocommerce_before_loop_start', 'gallery_cate');
?>
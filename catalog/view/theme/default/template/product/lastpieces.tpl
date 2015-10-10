<?php echo $header; ?>
<h1><?php echo $heading_title; ?></h1>
<?php if ($products) : ?>
    <form class="form-inline clearfix">
        <div class="form-control-static input-sm pull-left">
            <strong><?php echo $text_display; ?>&nbsp;</strong>
        </div>
        <div class="btn-group pull-left">
            <a onclick="display('grid');" class="display-grid btn btn-default btn-sm" disabled="disabled"><?php echo $text_grid; ?></a>
            <a onclick="display('list');" class="display-list btn btn-default btn-sm"><?php echo $text_list; ?></a>
        </div>
        <div class="form-control-static input-sm pull-left">
            <strong>&nbsp;<?php echo $text_limit; ?>&nbsp;</strong>
        </div>
        <select class="form-control input-sm pull-left" onchange="location = this.value;">
            <?php foreach ($limits as $limits) : ?>
            <option value="<?php echo $limits['href']; ?>"<?php if ($limits['value'] == $limit) : ?> selected="selected"<?php endif; ?>><?php echo $limits['text']; ?></option>
            <?php endforeach; ?>
        </select>
        <div class="form-control-static input-sm pull-left">
            <strong>&nbsp;<?php echo $text_sort; ?>&nbsp;</strong>
        </div>
        <select class="form-control input-sm pull-left" onchange="location = this.value;">
            <?php foreach ($sorts as $sorts) : ?>
            <option value="<?php echo $sorts['href']; ?>"<?php if ($sorts['value'] == $sort . '-' . $order) : ?> selected="selected"<?php endif; ?>><?php echo $sorts['text']; ?></option>
            <?php endforeach; ?>
        </select>
        <a href="<?php echo $compare; ?>" class="btn btn-default btn-sm pull-right" id="compare-total"><?php echo $text_compare; ?></a>
    </form>
    <div class="product-list row">
        <?php foreach ($products as $product) : ?>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <div class="thumbnail">
                    <?php if ($product['thumb']) : ?>
                    <img src="<?php echo $product['thumb']; ?>" class="thumbnail" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
                    <?php endif; ?>
                    <div class="caption">
                        <h3 class="name">
                            <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                        </h3>
                        <div class="description"><?php echo $product['description']; ?></div>
                        <?php if ($product['price']) : ?>
                        <h4 class="prices">
                            <?php if (!$product['special']) : ?>
                                <strong class="text-success"><?php echo $product['tax']; ?></strong>
                                <br />
                                <small class="text-muted"><?php echo $text_tax; ?> <?php echo $product['priceB']; ?></small>
                            <?php else : ?>
                                <s><?php echo $product['price']; ?></s>
                                <strong class="text-danger"><?php echo $product['special']; ?></strong>
                                <br />
                                <small class="text-muted"><?php echo $text_tax; ?> <?php echo $product['specialB']; ?></small>
                            <?php endif; ?>
                        </h4>
                        <?php endif; ?>
                        <?php if ($product['rating']) : ?>
                        <div class="rating text-center">
                            <img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" />
                        </div>
                        <?php endif; ?>
                        <div class="cart text-center">
                            <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="btn btn-success btn-block" />
                            <a class="btn btn-warning btn-xs" onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a>
                            <a class="btn btn-default btn-xs" onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pagination"><?php echo $pagination; ?></div>
<?php else : ?>
    <p><?php echo $text_empty; ?></p>
<?php endif; ?>
<script type="text/javascript">
//<!--
    function display(view) {
        if (view == 'list') {
            $('.product-list > div').each(function(index, element) {
                $(element).attr('class', 'col-lg-12 col-md-12 col-sm-12 col-xs-12');
                html = '<div class="row"><div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">';
                var image = $(element).find('img.thumbnail');
                if (image.length > 0) {
                    html += image[0].outerHTML;
                }
                html += '</div><div class="col-lg-4 col-lg-push-6 col-md-3 col-md-push-6 col-sm-6 col-xs-12">';
                var price = $(element).find('.prices').html();
                if (price != null) {
                    html += '<h5 class="prices">' + price  + '</h5>';
                }
                html += '<div class="cart text-center">' + $(element).find('.cart').html() + '</div>';
                html += '</div><div class="col-lg-6 col-lg-pull-4 col-md-6 col-md-pull-3 col-sm-12 col-xs-12">';
                html += '<h3 class="name">' + $(element).find('.name').html() + '</h3>';
                html += '<div class="description">' + $(element).find('.description').html() + '</div>';
                var rating = $(element).find('.rating').html();
                if (rating != null) {
                    html += '<div class="rating text-left">' + rating + '</div>';
                }
                html += '</div></div>';
                $(element).html(html);
            });
            $('.display-grid').attr('disabled', false);
            $('.display-list').attr('disabled', true);
            $.totalStorage('display', 'list');
        } else {
            $('.product-list > div').each(function(index, element) {
                $(element).attr('class', 'col-lg-4 col-md-6 col-sm-6 col-xs-12');
                html = '<div class="thumbnail">';
                var image = $(element).find('img.thumbnail');
                if (image.length > 0) {
                    html += image[0].outerHTML;
                }
                html += '<div class="caption">';
                html += '<h3 class="name">' + $(element).find('.name').html() + '</h3>';
                html += '<div class="description">' + $(element).find('.description').html() + '</div>';
                var price = $(element).find('.prices').html();
                if (price != null) {
                    html += '<h5 class="prices">' + price  + '</h5>';
                }
                var rating = $(element).find('.rating').html();
                if (rating != null) {
                    html += '<div class="rating text-center">' + rating + '</div>';
                }
                html += '<div class="cart text-center">' + $(element).find('.cart').html() + '</div>';
                html += '</div>';
                $(element).html(html);
            });
            $('.display-grid').attr('disabled', true);
            $('.display-list').attr('disabled', false);
            $.totalStorage('display', 'grid');
        }
    }
    $(document).ready(function(){
        view = $.totalStorage('display');
        display(view || 'grid');
    });
//-->
</script> 
<?php echo $footer; ?>
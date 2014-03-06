<div id="tab2" class="tab_content">

    <fieldset>
        <legend><?php echo __('Add Item'); ?></legend>
        <form method="POST" enctype="multipart/form-data" id="formItem">
            <input type="hidden" name="item[ID]" value="">
            <div class="row">
                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Title'); ?></label>
                        <input type="text" name="item[item_title]" size="25"/>
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Description'); ?></label>
                        <textarea name="item[item_description]" rows="3" cols="25"></textarea>
                    </div>
                </div>

                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Link'); ?></label>
                        <input type="text" name="item[item_link]" size="5" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Target'); ?></label>
                        <input type="text" name="item[item_target]" size="5" />
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Order'); ?></label>
                        <input type="text" name="item[item_order]" size="5" />
                    </div>
                </div>

                <div class="col2">
                    <div class="form-item">
                        <label><?php echo __('Slider'); ?></label>
                        <select name="item[slider_id]">
                            <?php foreach ($sliders as $slider): ?>
                                <option value="<?php echo $slider->ID; ?>"><?php echo $slider->slider_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-item">
                        <label><?php echo __('Image'); ?></label>
                        <input type="file" name="file" />
                    </div>
                    <div class="form-item">
                        <image id="image" width="160" height="100" ALT="IMAGEN">
                    </div>
                </div>

            </div>
            <div class="form-options">
                <label>&nbsp;</label>
                <button type="submit" class="button button-primary button-large"><b><?php echo __('Upload'); ?></b></button>
                <button type="reset" class="button"><?php echo __('Clear'); ?></button>
            </div>
        </form>

    </fieldset>

    <table class="wp-list-table widefat fixed">
        <thead>
            <tr>
                <th><?php echo __('Image'); ?></th>
                <th><?php echo __('Link'); ?></th>
                <th><?php echo __('Title'); ?></th>
                <th><?php echo __('Slider'); ?></th>
                <th><?php echo __('Order'); ?></th>
                <th><?php echo __('Fecha'); ?></th>
                <th width="140"><?php echo __('Panel'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($items as $item):
                ?>
                <tr data-id="<?php echo $item->ID; ?>">
                    <td>
                        <a href="<?php echo $item->item_link_img; ?>" target="_blank">
                            <img src="<?php echo $item->item_link_img; ?>" alt="NO IMAGE" width="70" height="50"/>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo $item->item_link; ?>" target="_blank"><?php echo $item->item_link; ?></a>
                    </td>
                    <td><?php echo $item->item_title; ?></td>
                    <td><?php echo $item->slider_name; ?></td>
                    <td><?php echo $item->item_order; ?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($item->created)); ?></td>
                    <td>
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>" data-item-option="edit"><?php echo __('Edit'); ?></a> &nbsp;
                        <a href="<?php echo admin_url('admin-ajax.php'); ?>" data-item-option="delete"><?php echo __('Remove'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">

    jQuery('button[type=reset]').click(function() {
        var img = document.querySelector('#image');
        img.setAttribute('src', '');
    });

    jQuery('[data-item-option]').click(function(event) {
        var elem = jQuery(this);
        jQuery.ajax({
            async: false,
            url: elem.attr('href'),
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'item_wr_ajax',
                option: elem.data('item-option'),
                ID: elem.parents('tr').data('id')
            },
            beforeSend: function() {
                event.preventDefault();
            }
        }).done(function(data) {
            if (typeof data === 'object') {
                for (var i in data) {
                    jQuery('[name="item[' + i + ']"]').val(data[i]);
                }
                var img = document.querySelector('#image');
                img.setAttribute('src', data.item_link_img);
            } else {
                jQuery('[data-id=' + data + ']').remove();
            }
        });
    });

    elita.validate({
        form: '#formItem',
        alert: '#alert',
        data: {
            'item[item_title]': 'required',
            'item[slider_id]': 'required'
        }
    });
</script>
